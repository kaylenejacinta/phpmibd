<?php
// final_submit_frs.php
// Marks an FRS as final-submitted for a given semester.
// Currently validates the FRS exists and has at least 1 enrolled MK.
// (No Status column in schema yet — add one if you want to lock editing post-submit.)
//
// Expects JSON body: { "id_semester": 3 }
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
$id_semester = $data["id_semester"] ?? null;

if (!$id_semester) {
    http_response_code(400);
    echo json_encode(["error" => "id_semester tidak ditemukan."]);
    exit;
}

try {
    // Verify FRS exists with at least 1 enrolled MK
    $stmt = $pdo->prepare("
        SELECT COUNT(e.Id_Enroll) AS jumlah
        FROM FRS f
        JOIN Enroll e ON f.Id_FRS = e.Id_FRS
        WHERE f.NPM = ? AND f.Id_Semester = ?
    ");
    $stmt->execute([$npm, $id_semester]);
    $row = $stmt->fetch();

    if (!$row || $row["jumlah"] == 0) {
        http_response_code(400);
        echo json_encode(["error" => "FRS kosong. Pilih mata kuliah terlebih dahulu."]);
        exit;
    }

    // If you add a Status column to FRS later, update it here:
    // $pdo->prepare("UPDATE FRS SET Status = 'Disetujui' WHERE NPM = ? AND Id_Semester = ?")
    //     ->execute([$npm, $id_semester]);

    echo json_encode(["success" => true, "pesan" => "FRS berhasil di-submit!"]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
