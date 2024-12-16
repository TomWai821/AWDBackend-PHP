<?php
    $server = 'localhost:3307';
	$user = 'root';
    $password = '';
    $dbName = 'ev_charger_db';

    $connect = mysqli_connect($server, $user, $password, $dbName);

    if(!$connect)
    {
        die("Database connection failed:". mysqli_connect_error());
    }
?>