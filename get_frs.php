<?php
// get_frs.php
// Return current FRS dan mata kuliah terdaftar mahasiswa yang terlogin
// Digunakan dashboard.html, pengisian_frs.html

session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesi tidak ditemukan. Silakan login ulang."]);
    exit;
}

$npm = $_SESSION["npm"];

try {
    // Get FRS aktif mahasiswa ini (latest semester)
    $stmtFRS = $pdo->prepare("
        SELECT TOP 1
            f.Id_FRS,
            f.Id_Semester,
            s.Tahun_Akademik,
            s.Periode
        FROM FRS f
        JOIN Semester s ON f.Id_Semester = s.Id_Semester
        WHERE f.NPM = ?
        ORDER BY s.Tahun_Akademik DESC, s.Periode DESC
    ");
    $stmtFRS->execute([$npm]);
    $frs = $stmtFRS->fetch();

    if (!$frs) {
        echo json_encode(["frs" => null, "matkul" => [], "total_sks" => 0]);
        exit;
    }

    // Get matkul terdaftar untuk FRS ini
    $stmtEnroll = $pdo->prepare("
        SELECT
            mk.Kode_Matkul,
            mk.Nama_Matkul,
            mk.SKS
        FROM Enroll e
        JOIN Mata_Kuliah mk ON e.Kode_Matkul = mk.Kode_Matkul
        WHERE e.Id_FRS = ?
    ");
    $stmtEnroll->execute([$frs["Id_FRS"]]);
    $matkul = $stmtEnroll->fetchAll();

    $total_sks = array_sum(array_column($matkul, "SKS"));

    echo json_encode([
        "frs"       => $frs,
        "matkul"    => $matkul,
        "total_sks" => $total_sks
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
