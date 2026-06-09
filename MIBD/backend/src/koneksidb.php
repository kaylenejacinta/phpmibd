<?php

$serverName = "localhost";

$connectionInfo = array(
    "Database" => "FRS_db"
);

$conn = sqlsrv_connect($serverName, $connectionInfo);

if (!$conn) {
    die(print_r(sqlsrv_errors(), true));
}

?>