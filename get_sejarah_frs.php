<?php
// get_sejarah_frs.php
// Return semua record FRS yang lalu milik mahasiswa yang terlogin
// Digunakan sejarah_frs.html, detail_sejarah_frs.html

session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesi tidak ditemukan. Silakan login ulang."]);
    exit;
}

$npm = $_SESSION["npm"];

// If id_frs dipass sebagai parameter query, return detail untuk FRS tersebut
$id_frs = $_GET["id_frs"] ?? null;

try {
    if ($id_frs) {
        // Detail matkul dalam FRS 
        $stmt = $pdo->prepare("
            SELECT
                mk.Kode_Matkul,
                mk.Nama_Matkul,
                mk.SKS,
                s.Tahun_Akademik,
                s.Periode
            FROM Enroll e
            JOIN Mata_Kuliah mk ON e.Kode_Matkul = mk.Kode_Matkul
            JOIN FRS f          ON e.Id_FRS = f.Id_FRS
            JOIN Semester s     ON f.Id_Semester = s.Id_Semester
            WHERE e.Id_FRS = ? AND f.NPM = ?
        ");
        $stmt->execute([$id_frs, $npm]);
        $matkul    = $stmt->fetchAll();
        $total_sks = array_sum(array_column($matkul, "SKS"));

        echo json_encode(["matkul" => $matkul, "total_sks" => $total_sks]);

    } else {
        // List semua record FRS mahasiswa ini
        $stmt = $pdo->prepare("
            SELECT
                f.Id_FRS,
                s.Tahun_Akademik,
                s.Periode,
                COUNT(e.Kode_Matkul) AS jumlah_mk,
                SUM(mk.SKS)          AS total_sks
            FROM FRS f
            JOIN Semester s     ON f.Id_Semester = s.Id_Semester
            LEFT JOIN Enroll e  ON f.Id_FRS = e.Id_FRS
            LEFT JOIN Mata_Kuliah mk ON e.Kode_Matkul = mk.Kode_Matkul
            WHERE f.NPM = ?
            GROUP BY f.Id_FRS, s.Tahun_Akademik, s.Periode
            ORDER BY s.Tahun_Akademik DESC, s.Periode DESC
        ");
        $stmt->execute([$npm]);
        echo json_encode($stmt->fetchAll());
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
