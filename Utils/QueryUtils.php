<?php
    include_once("DataMapUtils.php");

    function BuildInsertQuery($combineDataMap)
    {
        $fields = array_keys($combineDataMap); 
        
        $values = array_map(function($value) 
        { 
            return "'" . $value . "'"; 
        }
        , array_values($combineDataMap)); 

        $sql_query = "INSERT INTO evcharger (" . implode(", ", $fields) . ") VALUES (" . implode(", ", $values) . ")"; 
        return $sql_query;
    }

    function BuildUpdateQuery($station_id, $dataMap)
    {
        $updates = [];
        foreach ($dataMap as $key => $value) 
        {
            if (is_numeric($value)) 
            {
                $updates[] = "`$key` = $value";
            } 
            else if($value != '')
            {
                $value = addslashes($value);
                $updates[] = "`$key` = '$value'";
            }
        }

        $sql = "UPDATE evcharger SET " . implode(', ', $updates);
        $sql .= " WHERE station_id = '" . $station_id . "'";
        
        return $sql;
    }

    function SetChargersToQuery($value, $chargerType)
    {
        $chargers = 
        [
            "standard" => 'STANDARD_BS1363_no',
            "medium" => array('MEDIUM_IEC62196_no', 'MEDIUM_SAEJ1772_no', 'MEDIUM_OTHERS_no'),
            "quick" => array('QUICK_CHAdeMO_no', 'QUICK_CCS_DC_COMBO_no', 'QUICK_IEC62196_no', 'QUICK_GB_T20234_3_DC__no', 'QUICK_OTHERS_no')
        ];
        
        $conditions = array();
        $sql_query = "";
        $errorMessage = "Please input valid $value charger value! ";

        switch($value)
        {
            case "standard":
                if(isset($chargerType) && $chargerType != $chargers[$value])
                {
                    $errorMessage .= "(".$chargers[$value].")";
                    return message("1002", $errorMessage);
                }
                return "( ". $chargers[$value] ." > 0 )";
            
            case "medium":
            case "quick":
                if(!isset($chargerType))
                {
                    foreach($chargers[$value] as $charge) 
                    { 
                        $conditions[] = "($charge > 0)"; 
                    } 
                    
                    if (!empty($conditions)) 
                    { 
                        $sql_query .= implode(" OR ", $conditions);
                    }
                    return $sql_query;
                }
                else
                {
                    foreach($chargers[$value] as $charger)
                    {
                        $conditions[] .= $charger;
                        if($chargerType == $charger)
                        {
                            $sql_query .= "($chargerType > 0)";
                            return $sql_query;
                        }
                    }

                    $errorMessage .= "(".implode(", ", $conditions).")";
                    return message("1002", $errorMessage);
                        
                }
 
            default:
                return message("1002","Invalid charger Type: ". $value);
        }
    }
?>