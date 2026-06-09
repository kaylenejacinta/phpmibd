<?php
header("Content-Type: application/json");

include "koneksidb.php";

$sql = "
SELECT
    mk.Kode_Matkul,
    mk.Nama_Matkul,
    d.Nama AS Nama_Dosen,
    j.Hari,
    CONVERT(VARCHAR(5), j.jam_mulai, 108) AS jam_mulai,
    CONVERT(VARCHAR(5), j.jam_selesai, 108) AS jam_selesai,
    j.Ruangan,
    mk.SKS
FROM Jadwal_Matkul j
JOIN Mata_Kuliah mk ON mk.Kode_Matkul = j.Kode_Matkul
JOIN Dosen d ON d.Id_Dosen = j.Id_Dosen
ORDER BY j.Hari, j.jam_mulai
";

$stmt = sqlsrv_query($conn, $sql);

if ($stmt === false) {
    echo json_encode(["error" => sqlsrv_errors()]);
    exit;
}

$data = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $data[] = $row;
}

echo json_encode($data);
?>