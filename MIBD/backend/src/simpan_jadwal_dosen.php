<?php
session_start();
header("Content-Type: text/plain");

include "koneksidb.php";

/* =========================
   CEK SESSION DOSEN
========================= */
$id_dosen = $_SESSION["id_dosen"] ?? null;

if (!$id_dosen) {
    die("SESSION ERROR");
}

/* =========================
   AMBIL DATA DARI JS
========================= */
$data = json_decode(file_get_contents("php://input"), true);

$kode    = $data["kode"] ?? null;
$hari    = $data["hari"] ?? null;
$mulai   = $data["jam_mulai"] ?? null;
$selesai = $data["jam_selesai"] ?? null;
$ruangan = $data["ruangan"] ?? null;

if (!$kode) {
    die("DATA KOSONG");
}

/* =========================
   AMBIL ID SEMESTER DARI MK
========================= */
$sql_sem = "
SELECT Id_Semester
FROM Mata_Kuliah
WHERE Kode_Matkul = ?
";

$stmt_sem = sqlsrv_query($conn, $sql_sem, [$kode]);
$row_sem = sqlsrv_fetch_array($stmt_sem, SQLSRV_FETCH_ASSOC);

$id_semester = $row_sem["Id_Semester"] ?? null;

if (!$id_semester) {
    die("SEMESTER TIDAK DITEMUKAN");
}

/* =========================
   AUTO ID (JDW001, JDW002...)
========================= */
$sql_last = "
SELECT MAX(CAST(SUBSTRING(Id_Jadwal, 4, 10) AS INT)) AS last_id
FROM Jadwal_Matkul
";

$stmt_last = sqlsrv_query($conn, $sql_last);
$row_last = sqlsrv_fetch_array($stmt_last, SQLSRV_FETCH_ASSOC);

$next_id = ($row_last["last_id"] ?? 0) + 1;
$id_jadwal = "JDW" . str_pad($next_id, 3, "0", STR_PAD_LEFT);

/* =========================
   INSERT JADWAL
========================= */
$sql = "
INSERT INTO Jadwal_Matkul
(Id_Jadwal, Hari, jam_mulai, jam_selesai, Ruangan, Kode_Matkul, Id_Semester, Id_Dosen)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)
";

$params = [
    $id_jadwal,
    $hari,
    $mulai,
    $selesai,
    $ruangan,
    $kode,
    $id_semester,
    $id_dosen
];

$stmt = sqlsrv_query($conn, $sql, $params);

/* =========================
   RESPONSE
========================= */
if ($stmt === false) {
    die(print_r(sqlsrv_errors(), true));
}

echo "JADWAL TERSIMPAN: " . $id_jadwal;
?>