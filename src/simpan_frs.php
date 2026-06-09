<?php
// simpan_frs.php
// Menyimpan mata kuliah yang diselect ke dalam tabel Enroll 
// Enroll punya: Id_Enroll (PK), Tanggal_Ambil, Kode_Matkul, Id_Semester, Id_FRS
// Digunakan pengisian_frs.html (Simpan FRS button)
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) {
    http_response_code(401);
    echo "Unauthorized";
    exit;
}

$data    = json_decode(file_get_contents("php://input"), true);
$npm     = $_SESSION["npm"];
$matkul  = $data["matkul"] ?? [];   // array of { kode: int }

if (empty($matkul)) {
    http_response_code(400);
    echo "Pilih minimal 1 mata kuliah.";
    exit;
}

try {
    // Get the active FRS for this student
    $stmtFRS = $pdo->prepare("
        SELECT TOP 1 f.Id_FRS, f.Id_Semester
        FROM FRS f
        JOIN Semester s ON f.Id_Semester = s.Id_Semester
        WHERE f.NPM = ?
        ORDER BY s.Tahun_Akademik DESC, s.Periode DESC
    ");
    $stmtFRS->execute([$npm]);
    $frs = $stmtFRS->fetch();

    if (!$frs) {
        http_response_code(404);
        echo "FRS tidak ditemukan untuk mahasiswa ini.";
        exit;
    }

    $pdo->beginTransaction();

    // Clear existing enrollments for this FRS
    $del = $pdo->prepare("DELETE FROM Enroll WHERE Id_FRS = ?");
    $del->execute([$frs["Id_FRS"]]);

    // Insert each selected course
    $ins = $pdo->prepare("
        INSERT INTO Enroll (Tanggal_Ambil, Kode_Matkul, Id_Semester, Id_FRS)
        VALUES (GETDATE(), ?, ?, ?)
    ");
    foreach ($matkul as $mk) {
        $ins->execute([$mk["kode"], $frs["Id_Semester"], $frs["Id_FRS"]]);
    }

    $pdo->commit();
    echo "FRS berhasil disimpan!";

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Error: " . $e->getMessage();
}
?>