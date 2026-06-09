<?php
// edit_frs.php
// Replaces all Enroll records for an existing FRS with a new MK selection.
// Called from the Detail FRS edit mode (simpanEditFRS in JS).
//
// Expects JSON body: { "id_frs": 5, "matkul": ["300", "301", "303"] }
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesi tidak ditemukan."]);
    exit;
}

$data        = json_decode(file_get_contents("php://input"), true);
$npm         = $_SESSION["npm"];
$id_frs      = $data["id_frs"]  ?? null;
$matkul_list = $data["matkul"]  ?? [];

if (!$id_frs || empty($matkul_list)) {
    http_response_code(400);
    echo json_encode(["error" => "Data tidak lengkap."]);
    exit;
}

try {
    // Verify this FRS belongs to the logged-in student and get its semester
    $stmtCheck = $pdo->prepare("
        SELECT f.Id_FRS, f.Id_Semester
        FROM FRS f
        WHERE f.Id_FRS = ? AND f.NPM = ?
    ");
    $stmtCheck->execute([$id_frs, $npm]);
    $frs = $stmtCheck->fetch();

    if (!$frs) {
        http_response_code(403);
        echo json_encode(["error" => "FRS tidak ditemukan atau bukan milik kamu."]);
        exit;
    }

    $id_semester = $frs["Id_Semester"];

    $pdo->beginTransaction();

    // Clear existing enrollments
    $del = $pdo->prepare("DELETE FROM Enroll WHERE Id_FRS = ?");
    $del->execute([$id_frs]);

    // Insert new selection
    $ins = $pdo->prepare("
        INSERT INTO Enroll (Tanggal_Ambil, Kode_Matkul, Id_Semester, Id_FRS)
        VALUES (GETDATE(), ?, ?, ?)
    ");
    foreach ($matkul_list as $kode) {
        $ins->execute([$kode, $id_semester, $id_frs]);
    }

    $pdo->commit();
    echo json_encode(["success" => true, "pesan" => "FRS berhasil diperbarui!"]);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
