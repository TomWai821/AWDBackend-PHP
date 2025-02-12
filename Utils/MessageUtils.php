<?php
	function message($errorCode, $message, $result = null)
	{
		header('Content-Type: application/json; charset=utf-8');
		
		$debug = [
			'timestamp' => date('Y-m-d H:i:s'),
			'request_method' => $_SERVER['REQUEST_METHOD'] ?? 'unknown',
			'request_uri' => $_SERVER['REQUEST_URI'] ?? 'unknown'
		];

		$response = [
			'header' => HeaderBuilder($errorCode, $message),
			'result' => $result,
			'debug' => $debug
		];
		
		echo json_encode($response, 
			JSON_UNESCAPED_UNICODE | 
			JSON_UNESCAPED_SLASHES | 
			JSON_PRETTY_PRINT
		);
		exit;
	}

	function HeaderBuilder($errorCode, $message)
	{
		$errorCodeMap = array(
			// If response result
			"0000" => "No error found",
			"0001" => "Cannot find any result with the current data!",
			"0002" => "Delete more than 1 record!",
			"0003" => "Failed to Update data",

			// For URL
			"1000" => "Invalid Column Name!",
			"1001" => "Missing Value!",
			"1002" => "Invalid Value!",

			// For JSON file
			"2000" => "Missing data in JSON file",
			"2001" => "Missing column!",
			"2002" => "Missing column data!",
			"2003" => "Invalid data!",	
			
			// For SQL Server Error
			"3000" => "SQL failure while GET data",
			"3001" => "SQL failure while POST data",
			"3002" => "SQL failure while PUT data",
			"3003" => "SQL failure while DELETE data",
		);

		if(($errorCode === "0000") || ($errorCode === "0002"))
		{
			$header['success'] = 'true';
		}
		else
		{
			$header['success'] = 'false';
		}

		// Header information 
		$header['message'] = $message;
		$header['errorCode'] = $errorCode;
		$header['errorMessage'] = $errorCodeMap[$errorCode];

		return $header;
	}
?>