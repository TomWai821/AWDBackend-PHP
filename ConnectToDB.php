<?php
    $server = 'localhost';
	$user = 'root';
    $password = '';
    $dbName = 'ev_charger_db';

    $connect = new mysqli($server, $user, $password, $dbName);

    if($connect -> connect_error)
    {
        die("Database connection failed:". mysqli_connect_error());
    }
?>