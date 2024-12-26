<?php

	header('Content-Type: application/json; charset=utf-8');
	header("Access-Control-Allow-Origin: *");
	header("Access-Control-Allow-Headers: *");

	include_once("MessageHandler.php");
	include_once("QueryBuilder.php");

	Class EVChargerService
	{

		function __construct()
		{

		}

		function GET($parameter)
		{

			$type = array_shift($parameter);
			$value = array_shift($parameter);
			include("ConnectToDB.php");

			$sql_query = "SELECT * FROM evcharger ";
			$error_Message = "Failed to Get Data";

			if($type != null)
			{
				switch($type)
				{
					case "address":
						if($value == null)
						{
							message("1001", "Please input the address!");
						}
						$sql_query .= "WHERE address LIKE '%$value%'";
						break;
					
					case "district":
						if($value == null)
						{
							message("1001", "Please input the district!");
						}
						$sql_query .= "WHERE district = '$value'";
						break;
					
					default:
						message("1000", $error_Message);
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
				$output = array();

				$output_message = $result->num_rows." EV Charger records retrieved";

				while($row = $result->fetch_assoc()) 
				{ 
					$output[] = $row; 
				}
				
				if(empty($output)) 
				{ 
					message("0001", $output_message); 
				} 

				message("0000", $output_message, $output); 
			} 
			catch (Exception $e) 
			{ 
				message("5000", $e->getMessage()); 
			}
		}

		function POST($parameter)
		{
			$body = file_get_contents("php://input");
			$dataArray = json_decode($body, true);

			$station_id = GetStationID($dataArray);

			$requireDataMap = array(
				'district' => $dataArray['district'] ?? null,
				'location' => $dataArray['location'] ?? null,
				'address' => $dataArray['address'] ?? null
			);

			foreach($requireDataMap as $requireData)
			{
				if(empty($requireData))
				{
					message("6000", "Please input $requireData and its value in JSON file (type: string)");
				}
			}

			$requireDataMap = array_merge($station_id, $requireDataMap);
			
			// Put other data to otherDataMap (exclude station_id, district, location and address)
			$otherDataMap = BuildOtherDataMap($dataArray);
			$combineDataMap = array_merge($requireDataMap, $otherDataMap);

			include("connectToDB.php");
			
			$sql_query = BuildInsertQuery($combineDataMap);

			try
			{
				$result = $connect->query($sql_query);
				message("0000", "EVCharger with ID ".$dataArray['station_id']." inserted successfully");
			}
			catch(Exception $e)
			{
				message('5001', "Failed to insert data".$e->getMessage());
			}
		}

		function PUT($parameter)
		{
			$body = file_get_contents("php://input");
			$dataArray = json_decode($body, true);

			$station_id = GetStationID($dataArray);
			
			// This is not required in PUT method (User may not update data about location, address, and district)
			$fieldsToUpdate = [ 
				'district' => $dataArray['district'] ?? null,
				'location' => $dataArray['location'] ?? null,
				'address' => $dataArray['address'] ?? null
			]; 
			
			$addressDataMap = array();
			foreach ($fieldsToUpdate as $field => $value) 
			{ 
				if (isset($value)) 
				{ 
					if ($value === "") 
					{ 
						message("6002", $field. " cannot be empty when updating."); 
					} 

					$addressDataMap[$field] = $value;
				}
			}
			
			// Put other data to otherDataMap (exclude station_id, district, location and address)
			$otherDataMap = BuildOtherDataMap($dataArray);

			if($addressDataMap != null)
			{
				$combineDataMap = array_merge($addressDataMap, $otherDataMap);
			}
			else
			{
				$combineDataMap = $otherDataMap;
			}

			include("connectToDB.php");

			$sql_query = BuildUpdateQuery($station_id, $combineDataMap);

			try
			{
				$result = $connect->query($sql_query);
				message("0000", "EVCharger with ID ".$dataArray['station_id']." update successfully");
			}
			catch(Exception $e)
			{
				message('5002', "Failed to update data:".$e->getMessage());
			}
		}

		function DELETE($parameter)
		{
			$type = array_shift($parameter);
			$data = array_shift($parameter);
			include("ConnectToDB.php");

			$error_Message = "Failed to delete data";

			if($type !== "id")
			{
				message("4001", $error_Message);
			}

			if($data === null)
			{
				message("4000", $error_Message);
			}

			$sql_query = "DELETE FROM evcharger WHERE station_id = '$data'";

			try
			{
				$result = $connect->query($sql_query);
				
				if($connect -> affected_rows == 0)
				{
					message('0001', "Failed to delete data");
				}
				else
				{
					message('0000', $connect -> affected_rows.' EV Charger record deleted');
				}
			}
			catch(Exception $e)
			{
				message('5003', "Failed to delete data:".$e->getMessage());
			}
		}
	}
?>