<?php
    function BuildOtherDataMap($dataArray)
    {
        $charges = GetCharges($dataArray);
        $geometryCoordinatesFields = GetGeometryCoordinates($dataArray);
<<<<<<< HEAD
        $remark = array('REMARK_FOR__OTHERS_' => $dataArray['REMARK_FOR__OTHERS_'] ?? null);
    
        $otherDataMap = array_merge($charges, $geometryCoordinatesFields, $remark);
        return $otherDataMap;
    }

=======
    
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

>>>>>>> 01b3c0212c4a7d3fdeaf0bbf1855c680ad0f7890
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
<<<<<<< HEAD
                return message("2003", "Please ensure that charges is numeric value or not null");
            }
        }
        return $charges;
    }

    function GetGeometryCoordinates($dataArray)
    {
        if(isset($dataArray['geometry_coordinates_Latitude']) && isset($dataArray['geometry_coordinates_Longitude']))
        {
            if(!is_numeric($dataArray['geometry_coordinates_Latitude']) || !is_numeric($dataArray['geometry_coordinates_Longitude']))
            {
                return message("2003", "Please ensure that geometry_coordinates_Latitude and geometry_coordinates_Longitude is numeric value or not null");
            }
        }

        $geometryCoordinatesFields = array(
            'geometry_coordinates_Latitude' => $dataArray['geometry_coordinates_Latitude'] ?? null,
            'geometry_coordinates_Longitude' => $dataArray['geometry_coordinates_Longitude'] ?? null
        );

        return $geometryCoordinatesFields;
    }

    function GetStationID($dataArray)
    {
        if(isset($dataArray['station_id']))
        {
            if($dataArray['station_id'] == null)
            {
                return message("2002", "Please input station_id data");
            }
            return $dataArray['station_id'];
        }
        return message("2001", "Please input station_id in JSON file (type: string)");
=======
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
>>>>>>> 01b3c0212c4a7d3fdeaf0bbf1855c680ad0f7890
    }
?>