<?php
// get_sejarah_frs.php
// Return semua record FRS yang lalu milik mahasiswa yang terlogin
// Digunakan sejarah_frs.html, detail_sejarah_frs.html
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesi tidak ditemukan."]);
    exit;
}

$npm = $_SESSION["npm"];

try {
    $stmt = $pdo->prepare("
        SELECT
            f.Id_FRS,
            CONCAT(
                CASE s.Periode WHEN 1 THEN 'Ganjil' ELSE 'Genap' END,
                ' ', s.Tahun_Akademik
            ) AS Semester,
            COALESCE(SUM(mk.SKS), 0) AS Total_SKS,
            'Disetujui' AS Status
        FROM FRS f
        JOIN Semester s          ON f.Id_Semester = s.Id_Semester
        LEFT JOIN Enroll e       ON f.Id_FRS = e.Id_FRS
        LEFT JOIN Mata_Kuliah mk ON e.Kode_Matkul = mk.Kode_Matkul
        WHERE f.NPM = ?
        GROUP BY f.Id_FRS, s.Tahun_Akademik, s.Periode
        ORDER BY s.Tahun_Akademik DESC, s.Periode DESC
    ");
    $stmt->execute([$npm]);
    echo json_encode($stmt->fetchAll());

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
