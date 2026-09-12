<?php
    $serverName = "EldrjdgeBOOK\\SQLEXPRESS";
    $connectionOptions = array(
        "Database" => "ThePawfectCarePH",
        "UID" => "sa",
        "PWD" => "sisc1234",
    );

    //Starts connection
    $conn = sqlsrv_connect($serverName, $connectionOptions);

    if ($conn === false) {
        die(print_r(sqlsrv_errors(), true));
    }
?>
