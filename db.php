<?php
$serverName = "LAPTOP-H2UFRUQF\SQLEXPRESS"; 
$database = "TUBES_FRS";

try {
    $pdo = new PDO("sqlsrv:Server=LAPTOP-H2UFRUQF\SQLEXPRESS;Database=TUBES_FRS;TrustServerCertificate=true");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(["error" => $e->getMessage()]));
}
?>


