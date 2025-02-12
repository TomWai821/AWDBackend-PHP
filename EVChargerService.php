<?php
header('Content-Type: application/json; charset=utf-8');
header("Access-Control-Allow-Origin: http://localhost:4200");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Access-Control-Allow-Credentials: true");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

include_once("./Utils/MessageUtils.php");
include_once("./Utils/QueryUtils.php");

Class EVChargerService 
{

    function __construct() {}

    function GET($parameter) 
    {
        $type = array_shift($parameter);
        $value = array_shift($parameter);
        $directValue = array_shift($parameter);
        include("ConnectToDB.php");

        if($type == "district" && $value == "distinct")
        {
            $sql_query = "SELECT DISTINCT district FROM evcharger";
        }
        else
        {
            $sql_query = "SELECT id, concat('EVCS_', id) as station_id, district, location, address, STANDARD_BS1363_no, MEDIUM_IEC62196_no, MEDIUM_SAEJ1772_no, MEDIUM_OTHERS_no, 
            QUICK_CHAdeMO_no, QUICK_CCS_DC_COMBO_no, QUICK_IEC62196_no, QUICK_GB_T20234_3_DC__no, QUICK_OTHERS_no, REMARK_FOR__OTHERS_, 
            geometry_coordinates_Latitude, geometry_coordinates_Longitude FROM evcharger ";
        }
        
        $error_Message = "Failed to Get Data";

        if($type != null) 
        {
            switch($type) 
            {
                case "address":
                    if($value === null) 
                    {
                        message("1001", "Please input the address value in URL!");
                    }
                    $sql_query .= "WHERE address LIKE '%$value%'";
                    break;

                case "district":
                    if($value === null) 
                    {
                        message("1001", "Please input the district value in URL!");
                    }

                    if($value !== "distinct")
                    {
                        $sql_query .= "WHERE district = '$value'";
                    }
                    break;

                case "type":
                    if($value == null)
                    {
                        message("1001", "Please input the charger type (standard/medium/quick) in URL!");
                    }
                    $sql_query .= "WHERE " .SetChargersToQuery($value, $directValue);
                    break;
                
                case "remark":
                    if($value == null)
                    {
                        message("1001", "Please input remark value (e.g. connector/supercharger) in URL!");
                    }
                    $sql_query .= "WHERE REMARK_FOR__OTHERS_ LIKE '%$value%'";
                    break;

                default:
                    message("1000", "Please input the available column name! (address/district/charger)");
                    break;
            }
        }

        try 
        {
            $result = $connect->query($sql_query);
            if ($result === false) 
            {
                // Handle query error 
                throw new Exception("Query error: " . $connect->error);
            }

            $output_message = $result->num_rows." EV Charger records retrieved";
            $output = array();

            while($row = $result->fetch_assoc()) 
            {
                $output[] = $row;

                if($type !== "distinct" && $value !== "distinct")
                {
                    // Update station_id for each record 
                    $update_query = "UPDATE evcharger SET station_id = CONCAT('EVCS_', id) WHERE id = " . $row['id']; 

                    if ($connect->query($update_query) === FALSE) 
                    { 
                        message("0003", "Failed to update station_id: " . $connect->error);
                    }
                }
            }
            
            if(empty($output)) 
            {
                message("0001", $output_message);
            } 

            message("0000", $output_message, $output);
        } 
        catch (Exception $e) 
        {
            message("3000", $e->getMessage());
        }
    }

    function POST($parameter) 
    {
        $body = file_get_contents("php://input");
        $dataArray = json_decode($body, true);

        if (json_last_error() !== JSON_ERROR_NONE) 
        {
            message("2000", "Invalid file format(Only accept JSON)");
        }

        $requireDataMap = array(
            'district' => $dataArray['district'] ?? null,
            'location' => $dataArray['location'] ?? null,
            'address' => $dataArray['address'] ?? null,
        );

        foreach ($requireDataMap as $key => $requireData) 
        {
            if (empty($requireData) && $key !== 'geometry_coordinates_Longitude' && $key !== 'geometry_coordinates_Latitude') 
            {
                message("2000", "Please input $key and its value in JSON file (type: string)");
            }
        }
        
        // Put other data to otherDataMap (exclude station_id, district, location and address)
        $otherDataMap = BuildOtherDataMap($dataArray);
        $combineDataMap = array_merge($requireDataMap, $otherDataMap);

        include("connectToDB.php");
        
        $sql_query = BuildInsertQuery($combineDataMap);

        try 
        {
            $result = $connect->query($sql_query);

            if (!$result) 
            {
                throw new Exception($connect->error);
            }
            message("0000", "EVCharger record inserted successfully");
        } 
        catch (Exception $e) 
        {
            message('3001', "Failed to insert data: " . $e->getMessage());
        }
    }

    function PUT($parameter) 
    {
        try 
        {
            $body = file_get_contents("php://input");
            $dataArray = json_decode($body, true);

            if (json_last_error() !== JSON_ERROR_NONE) 
            {
                message("2000", "Invalid file format(Only accept JSON)");
            }

            error_log("Received PUT data: " . print_r($dataArray, true));

            $station_id = GetStationID($dataArray);
    
            $fieldsToUpdate = array_filter([
                'district' => $dataArray['district'] ?? null,
                'location' => $dataArray['location'] ?? null,
                'address' => $dataArray['address'] ?? null,
                'REMARK_FOR__OTHERS_' => $dataArray['REMARK_FOR__OTHERS_'] ?? null,
                'geometry_coordinates_Longitude' => $dataArray['geometry_coordinates_Longitude'] ?? null,
                'geometry_coordinates_Latitude' => $dataArray['geometry_coordinates_Latitude'] ?? null,
            ], function($value) {
                return $value !== null && $value !== '';
            });

            error_log("Fields to update: " . print_r($fieldsToUpdate, true));

            $otherDataMap = BuildOtherDataMap($dataArray);
            $combineDataMap = !empty($fieldsToUpdate) ? array_merge($fieldsToUpdate, $otherDataMap) : $otherDataMap;

            error_log("Combined data map: " . print_r($combineDataMap, true));

            if (empty($combineDataMap)) 
            {
                message("2003", "There are no data for update");
            }

            include("connectToDB.php");
            $sql_query = BuildUpdateQuery($station_id, $combineDataMap);
            
            error_log("Update SQL query: " . $sql_query);
            $result = $connect->query($sql_query);

            if ($result === false) 
            {
                message("0003", "Failed to UPDATE data: " .$connect->error);
            }

            if ($connect->affected_rows === 0) 
            {
                message("0000", "No data has been updated!");
            }

            message("0000", "Charger Record with station_id: ".$dataArray['station_id']." was Update successfully!");
        } 
        catch(Exception $e) 
        {
            error_log("Update error: " . $e->getMessage());
            message('3002', "Failed to UPDATE data: " . $e->getMessage());
        }
    }

    function DELETE($parameter) 
    {
        $type = array_shift($parameter);
        $value = array_shift($parameter);
        include("ConnectToDB.php");

        $sql_query = "DELETE FROM evcharger WHERE ";

        switch($type)
        {
            case "id":
                if($value == null)
                {
                    message("1001", "Please input station_id value!");
                }
                $sql_query .= "station_id = '$value'";
                break;

            case "district":
                if($value == null)
                {
                    message("1001", "Please input district value!");
                }
                $sql_query .= "district = '$value'";
                break;
            
            default:
                message("1000", "Please input the available column name! (id/district)");
                break;
        }

        if($value === null) 
        {
            message("1001", "Please input the value with ".$type." column in URL");
        }

        try 
        {
            $result = $connect->query($sql_query);
            
            if($connect -> affected_rows == 0) 
            {
                message('0001', "Failed to delete data");
            }

            $connect -> affected_rows > 1 ? message('0002', $connect -> affected_rows.' EV Charger record deleted') : message('0000', $connect -> affected_rows.' EV Charger record deleted');
        } 
        catch(Exception $e) 
        {
            message('3003', "Failed to DELETE data:".$e->getMessage());
        }
    }
}
?>