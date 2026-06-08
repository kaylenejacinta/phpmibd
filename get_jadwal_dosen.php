<?php
// get_jadwal_dosen.php
// Return jadwal and mata kuliah dosen yang terlogin
// Digunakan dashboard_dosen.html, jadwal_mata_kuliah.html

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
    $stmt = $pdo->prepare("
        SELECT
            j.Id_Jadwal,
            j.Hari,
            CONVERT(varchar, j.Jam_Mulai, 108)   AS Jam_Mulai,
            CONVERT(varchar, j.Jam_Selesai, 108) AS Jam_Selesai,
            j.Ruangan,
            mk.Kode_Matkul,
            mk.Nama_Matkul,
            mk.SKS,
            s.Tahun_Akademik,
            s.Periode
        FROM Jadwal_Matkul j
        JOIN Mata_Kuliah mk ON j.Kode_Matkul = mk.Kode_Matkul
        JOIN Semester s     ON j.Id_Semester  = s.Id_Semester
        WHERE j.Id_Dosen = ?
        ORDER BY
            CASE j.Hari
                WHEN 'Senin'  THEN 1
                WHEN 'Selasa' THEN 2
                WHEN 'Rabu'   THEN 3
                WHEN 'Kamis'  THEN 4
                WHEN 'Jumat'  THEN 5
                ELSE 6
            END,
            j.Jam_Mulai
    ");
    $stmt->execute([$id_dosen]);
    echo json_encode($stmt->fetchAll());

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
