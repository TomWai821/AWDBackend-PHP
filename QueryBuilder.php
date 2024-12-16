<?php
    include_once("Utils/DataMapUtils.php");

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

    function BuildUpdateQuery($station_id, $combineDataMap)
    {
        $fields = array_keys($combineDataMap); 
        $values = array_values($combineDataMap);
        $station_id = $station_id['station_id'];
        $setClause = []; 

        foreach ($fields as $index => $field) 
        { 
            $value = $values[$index]; 
            $setClause[] = "$field = '$value'"; 
        }

        $sql_query = "UPDATE evcharger SET " . implode(", ", $setClause) . " WHERE station_id = '$station_id'"; 
        return $sql_query;
    }
?>
