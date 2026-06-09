<?php
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) { 
    echo json_encode([]); exit; 
}

$stmt = $pdo->prepare("
    SELECT 
        mk.Kode_Matkul, mk.Nama_Matkul, mk.SKS,
        j.Hari,
        CONVERT(varchar, j.Jam_Mulai, 108)   AS jam_mulai,
        CONVERT(varchar, j.Jam_Selesai, 108) AS jam_selesai,
        j.Ruangan,
        d.Nama AS Nama_Dosen
    FROM Enroll e
    JOIN FRS f          ON e.Id_FRS = f.Id_FRS
    JOIN Mata_Kuliah mk ON e.Kode_Matkul = mk.Kode_Matkul
    LEFT JOIN Jadwal_Matkul j ON mk.Kode_Matkul = j.Kode_Matkul
    LEFT JOIN Dosen d         ON j.Id_Dosen = d.Id_Dosen
    WHERE f.NPM = ?
    ORDER BY j.Hari, j.Jam_Mulai
");
$stmt->execute([$_SESSION["npm"]]);
echo json_encode($stmt->fetchAll());
?>
