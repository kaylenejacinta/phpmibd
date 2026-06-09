<?php
// Return matkul yang terenroll dalam record FRS tertentu
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesi tidak ditemukan."]);
    exit;
}

$id_frs = $_GET["id"] ?? null;
if (!$id_frs) {
    http_response_code(400);
    echo json_encode(["error" => "ID FRS tidak ditemukan."]);
    exit;
}

$npm = $_SESSION["npm"];

try {
    $stmt = $pdo->prepare("
        SELECT
            mk.Kode_Matkul,
            mk.Nama_Matkul,
            mk.SKS,
            j.Hari,
            CONVERT(varchar, j.Jam_Mulai,   108) AS jam_mulai,
            CONVERT(varchar, j.Jam_Selesai, 108) AS jam_selesai,
            j.Ruangan
        FROM Enroll e
        JOIN FRS f          ON e.Id_FRS      = f.Id_FRS
        JOIN Mata_Kuliah mk ON e.Kode_Matkul = mk.Kode_Matkul
        LEFT JOIN Jadwal_Matkul j ON mk.Kode_Matkul = j.Kode_Matkul
        WHERE e.Id_FRS = ? AND f.NPM = ?
    ");
    $stmt->execute([$id_frs, $npm]);
    echo json_encode($stmt->fetchAll());
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>