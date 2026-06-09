<?php
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) { 
    http_response_code(401); echo "Unauthorized"; exit; 
}

$data = json_decode(file_get_contents("php://input"), true);
$kode = $data["kode"] ?? null;

if (!$kode) { 
    http_response_code(400); echo "Kode MK tidak ditemukan."; exit; 
}

// Get active FRS id for this student
$stmt = $pdo->prepare("
    SELECT TOP 1 f.Id_FRS FROM FRS f
    JOIN Semester s ON f.Id_Semester = s.Id_Semester
    WHERE f.NPM = ?
    ORDER BY s.Tahun_Akademik DESC, s.Periode DESC
");
$stmt->execute([$_SESSION["npm"]]);
$frs = $stmt->fetch();

if (!$frs) { echo "FRS tidak ditemukan."; exit; }

$del = $pdo->prepare("DELETE FROM Enroll WHERE Id_FRS = ? AND Kode_Matkul = ?");
$del->execute([$frs["Id_FRS"], $kode]);
echo "MK berhasil di-drop.";
?>
