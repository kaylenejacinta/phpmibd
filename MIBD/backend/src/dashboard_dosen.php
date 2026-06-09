<?php
session_start();
header("Content-Type: application/json");

include "koneksidb.php";

$id = $_SESSION["id_dosen"] ?? null;

if (!$id) {
    echo json_encode(["mk"=>[], "jadwal"=>[]]);
    exit;
}

/* ===== MATA KULIAH ===== */
$sql_mk = "
SELECT DISTINCT
    mk.Kode_Matkul,
    mk.Nama_Matkul,
    mk.SKS
FROM Jadwal_Matkul j
JOIN Mata_Kuliah mk ON j.Kode_Matkul = mk.Kode_Matkul
WHERE j.Id_Dosen = ?
";

$stmt1 = sqlsrv_query($conn, $sql_mk, array($id));

$mk = [];
while ($row = sqlsrv_fetch_array($stmt1, SQLSRV_FETCH_ASSOC)) {
    $mk[] = $row;
}

/* ===== JADWAL ===== */
$sql_jadwal = "
SELECT
    mk.Nama_Matkul,
    j.Hari,
    j.Jam_Mulai,
    j.Jam_Selesai,
    j.Ruangan,
    mk.SKS
FROM Jadwal_Matkul j
JOIN Mata_Kuliah mk ON j.Kode_Matkul = mk.Kode_Matkul
WHERE j.Id_Dosen = ?
";

$stmt2 = sqlsrv_query($conn, $sql_jadwal, array($id));

$jadwal = [];
while ($row = sqlsrv_fetch_array($stmt2, SQLSRV_FETCH_ASSOC)) {
    $jadwal[] = $row;
}

echo json_encode([
    "mk" => $mk,
    "jadwal" => $jadwal
]);