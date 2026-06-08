<?php
$serverName = "localhost, 1433";
$database   = "FRS_db";
$user       = "Project";
$password   = "project123";

try {
    $pdo = new PDO(
        "sqlsrv:Server=$serverName;Database=$database;TrustServerCertificate=1",
        $user,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    http_response_code(500);
    die(json_encode(["error" => $e->getMessage()]));
}
?>
