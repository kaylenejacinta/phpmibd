<?php
// frs_aktif.php
// Returns MK enrolled in the student's FRS for a specific semester.
// Requires GET param: ?id_semester=X
// Used by: Pengisian FRS (to pre-check enrolled MK), Review FRS page
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) {
    echo json_encode([]);
    exit;
}

$npm         = $_SESSION["npm"];
$id_semester = isset($_GET["id_semester"]) ? (int)$_GET["id_semester"] : 0;

if (!$id_semester) {
    echo json_encode([]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT
            mk.Kode_Matkul,
            mk.Nama_Matkul,
            mk.SKS,
            j.Hari,
            CONVERT(varchar, j.Jam_Mulai,   108) AS jam_mulai,
            CONVERT(varchar, j.Jam_Selesai, 108) AS jam_selesai,
            j.Ruangan,
            f.Id_FRS
        FROM Enroll e
        JOIN FRS f          ON e.Id_FRS      = f.Id_FRS
        JOIN Mata_Kuliah mk ON e.Kode_Matkul = mk.Kode_Matkul
        LEFT JOIN Jadwal_Matkul j
               ON j.Kode_Matkul = mk.Kode_Matkul
              AND j.Id_Semester  = f.Id_Semester
        WHERE f.NPM = ? AND f.Id_Semester = ?
        ORDER BY mk.Kode_Matkul
    ");
    $stmt->execute([$npm, $id_semester]);
    echo json_encode($stmt->fetchAll());

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
