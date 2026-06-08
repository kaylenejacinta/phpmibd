<?php
// get_matkul_dosen.php
// Return semua mata kuliah yang tersedia dan dapat dikelola dosen 
// Digunakan oleh kelola_mata_kuliah.html

session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["id_dosen"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesi tidak ditemukan. Silakan login ulang."]);
    exit;
}

$id_dosen = $_SESSION["id_dosen"];

try {
    // Semua mata kuliah
  // Di flag jika dosen memang lagi mengajar mata kuliah tersebut 
    $stmt = $pdo->prepare("
        SELECT
            mk.Kode_Matkul,
            mk.Nama_Matkul,
            mk.SKS,
            s.Tahun_Akademik,
            s.Periode,
            CASE WHEN j.Id_Dosen = ? THEN 1 ELSE 0 END AS Sedang_Ajar
        FROM Mata_Kuliah mk
        LEFT JOIN Jadwal_Matkul j ON mk.Kode_Matkul = j.Kode_Matkul
                                  AND j.Id_Dosen = ?
        LEFT JOIN Semester s      ON mk.Id_Semester = s.Id_Semester
        ORDER BY mk.Kode_Matkul
    ");
    $stmt->execute([$id_dosen, $id_dosen]);
    echo json_encode($stmt->fetchAll());

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
