<?php
session_start();
header("Content-Type: application/json");

include "koneksidb.php";

$id_dosen = $_SESSION["id_dosen"] ?? null;

if (!$id_dosen) {
    echo json_encode([]);
    exit;
}

$sql = "
SELECT
    j.Id_Jadwal,
    mk.Kode_Matkul,
    mk.Nama_Matkul,
    j.Hari,
    CONVERT(VARCHAR(5), j.jam_mulai, 108) AS jam_mulai,
    CONVERT(VARCHAR(5), j.jam_selesai, 108) AS jam_selesai,
    j.Ruangan,
    mk.SKS
FROM Jadwal_Matkul j
JOIN Mata_Kuliah mk ON mk.Kode_Matkul = j.Kode_Matkul
WHERE j.Id_Dosen = ?
";

$stmt = sqlsrv_query($conn, $sql, [$id_dosen]);

$data = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $data[] = $row;
}

echo json_encode($data);
?>