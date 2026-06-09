<?php
session_start();
header("Content-Type: application/json");

include "koneksidb.php";

/* =========================
   SESSION CHECK
========================= */
if (!isset($_SESSION["id_dosen"])) {
    echo json_encode(["error" => "NOT_LOGIN"]);
    exit;
}

$id_dosen = $_SESSION["id_dosen"];

/* =========================
   JADWAL DOSEN
========================= */
$sql_jadwal = "
SELECT
    j.Kode_Matkul,
    mk.Nama_Matkul,
    j.Hari,
    CONVERT(VARCHAR(5), j.jam_mulai, 108) AS jam_mulai,
    CONVERT(VARCHAR(5), j.jam_selesai, 108) AS jam_selesai,
    j.Ruangan,
    mk.SKS
FROM Jadwal_Matkul j
JOIN Mata_Kuliah mk ON mk.Kode_Matkul = j.Kode_Matkul
WHERE j.Id_Dosen = ?
ORDER BY j.Hari, j.jam_mulai
";

$stmt = sqlsrv_query($conn, $sql_jadwal, [$id_dosen]);

if ($stmt === false) {
    echo json_encode(["error" => sqlsrv_errors()]);
    exit;
}

$jadwal = [];

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $jadwal[] = $row;
}

/* =========================
   RESPONSE
========================= */
echo json_encode($jadwal);
?>