<?php
    function BuildOtherDataMap($dataArray)
    {
        $charges = GetCharges($dataArray);
        $geometryCoordinatesFields = GetGeometryCoordinates($dataArray);
    
        $otherDataMap = array_merge($charges, $geometryCoordinatesFields);
        return $otherDataMap;
    }

    function GetStationID($dataArray)
    {
        if($dataArray['station_id'] === null || $dataArray['station_id'] === "")
		{
			return message("6000", "Please input station id in JSON file (name: station_id, type: string)");
		}

        return array('station_id' => "EVCS_".$dataArray['station_id']);
    }

    function GetCharges($dataArray)
    {
        $charges = 
        [ 
            'STANDARD_BS1363_no' => $dataArray['STANDARD_BS1363_no'] ?? null,
            'MEDIUM_IEC62196_no' => $dataArray['MEDIUM_IEC62196_no'] ?? null,
            'MEDIUM_SAEJ1772_no' => $dataArray['MEDIUM_SAEJ1772_no'] ?? null,
            'MEDIUM_OTHERS_no' => $dataArray['MEDIUM_OTHERS_no'] ?? null,
            'QUICK_CHAdeMO_no' => $dataArray['QUICK_CHAdeMO_no'] ?? null,
            'QUICK_CCS_DC_COMBO_no' => $dataArray['QUICK_CCS_DC_COMBO_no'] ?? null,
            'QUICK_IEC62196_no' => $dataArray['QUICK_IEC62196_no'] ?? null,
            'QUICK_GB_T20234_3_DC__no' => $dataArray['QUICK_GB_T20234_3_DC__no'] ?? null,
            'QUICK_OTHERS_no' => $dataArray['QUICK_OTHERS_no'] ?? null
        ];

        foreach($charges as $key => $charge)
        {
            if(!is_numeric($charge) && !empty($charge))
            {
                return message("6001", "Please ensure that charges is numeric value or not null");
            }
        }

        ChargeAmountValidation($charges);
        return $charges;
    }

    function ChargeAmountValidation($charges)
    {
        $count = 0;
        foreach ($charges as $key => $value) 
        { 
            if ($value !== null) 
            { 
                $count++;
            }
        }

        if($count <= 0)
        {
            return message("6000", "Please Input at least 1 charge");
        }
    }

    function GetGeometryCoordinates($dataArray)
    {
        return array(
            'geometry_coordinates_Latitude' => $dataArray['geometry_coordinates_Latitude'] ?? null,
            'geometry_coordinates_Longitude' => $dataArray['geometry_coordinates_Longitude'] ?? null
        );
    }
?>