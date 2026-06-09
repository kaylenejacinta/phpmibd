<?php
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) { 
    echo json_encode([]); exit; 
}

$stmt = $pdo->prepare("
    SELECT mk.Kode_Matkul, mk.Nama_Matkul, mk.SKS
    FROM Enroll e
    JOIN FRS f          ON e.Id_FRS = f.Id_FRS
    JOIN Mata_Kuliah mk ON e.Kode_Matkul = mk.Kode_Matkul
    JOIN Semester s     ON f.Id_Semester = s.Id_Semester
    WHERE f.NPM = ?
    ORDER BY s.Tahun_Akademik DESC, s.Periode DESC
");
$stmt->execute([$_SESSION["npm"]]);
echo json_encode($stmt->fetchAll());
?>
