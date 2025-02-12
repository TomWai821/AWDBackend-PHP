<?php
    $server = 'localhost:3307';
<<<<<<< HEAD
    $user = 'root';
=======
	$user = 'root';
>>>>>>> 01b3c0212c4a7d3fdeaf0bbf1855c680ad0f7890
    $password = '';
    $dbName = 'ev_charger_db';

    $connect = new mysqli($server, $user, $password, $dbName);

    if($connect -> connect_error)
    {
        die("Database connection failed:". mysqli_connect_error());
    }
?>