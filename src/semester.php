<?php
// Return semua pilihan semester di dropdown (Pengisian FRS page)
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesi tidak ditemukan."]);
    exit;
}

try {
    $stmt = $pdo->query("
        SELECT Id_Semester, Tahun_Akademik, Periode
        FROM Semester
        ORDER BY Tahun_Akademik DESC, Periode DESC
    ");
    echo json_encode($stmt->fetchAll());
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
