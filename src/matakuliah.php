<?php
// get_matkul.php
// Return semua Mata Kuliah yang di join dgn Jadwal and Dosen
// Digunakan oleh pengisian_frs.html, jadwal_kuliah.html, dashboard.html (mahasiswa)
header("Content-Type: application/json");
require 'db.php';

try {
    $stmt = $pdo->query("
        SELECT
            mk.Kode_Matkul,
            mk.Nama_Matkul,
            mk.SKS,
            j.Id_Jadwal,
            j.Hari,
            CONVERT(varchar, j.Jam_Mulai, 108)   AS jam_mulai,
            CONVERT(varchar, j.Jam_Selesai, 108) AS jam_selesai,
            j.Ruangan,
            d.Nama AS Nama_Dosen
        FROM Mata_Kuliah mk
        LEFT JOIN Jadwal_Matkul j ON mk.Kode_Matkul = j.Kode_Matkul
        LEFT JOIN Dosen d         ON j.Id_Dosen = d.Id_Dosen
        ORDER BY mk.Kode_Matkul
    ");
    echo json_encode($stmt->fetchAll());
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
