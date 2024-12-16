<?php
	function message($errorCode, $message, $result = null)
	{
		$header = HeaderBuilder($errorCode, $message);
		
		if($result == null)
		{
			$output = $header;
		}
		else
		{
			$output = array( 
				'header' => $header, 
				'result' => $result
			);
		}
		
		echo json_encode($output);
		exit;
	}

	function HeaderBuilder($errorCode, $message)
	{
		$errorCodeMap = array(
			// If there are no error
			"0000" => "No error found",
			"0001" => "Cannot find any result with the current data or column name!",
			"0002" => "Data with this ID already exists!",

			// For GET method
			"1000" => "Please input the available column name! (address/district)",
			"1001" => "Missing Data! (address/district)",
			
			// 2000 for POST and 3000 for PUT
			// However, these two method error usually from JSON file 

			// For DELETE method
			"4000" => "Please input valid data with the current column name!",
			"4001" => "Please input the available column name! (id)",

			// For SQL Server Error
			"5000" => "SQL failure while GET data",
			"5001" => "SQL failure while POST data",
			"5002" => "SQL failure while PUT data",
			"5003" => "SQL failure while DELETE data",

			// For JSON file
			"6000" => "Missing data in JSON file",
			"6001" => "Invalid column name!",
			"6002" => "Invalid data!"
		);

		if($errorCode != "0000")
		{
			$header['success'] = 'false';
		}
		else
		{
			$header['success'] = 'true';
		}

		// Header information 
		$header['message'] = $message;
		$header['errorCode'] = $errorCode;
		$header['errorMessage'] = $errorCodeMap[$errorCode];

		return $header;
	}
?>