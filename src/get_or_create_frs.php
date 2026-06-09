<?php
// get_or_create_frs.php
// Returns the Id_FRS for the logged-in student + given semester.
// Creates a new FRS record if one doesn't exist yet.
// Called by simpanFRS() in the frontend before calling simpan_frs.php
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
    // Check if FRS already exists for this student + semester
    $stmt = $pdo->prepare("
        SELECT Id_FRS FROM FRS
        WHERE NPM = ? AND Id_Semester = ?
    ");
    $stmt->execute([$npm, $id_semester]);
    $frs = $stmt->fetch();

    if ($frs) {
        echo json_encode(["id_frs" => $frs["Id_FRS"]]);
        exit;
    }

    // Create new FRS — Id_FRS has no IDENTITY, so generate next value manually
    $maxStmt = $pdo->query("SELECT ISNULL(MAX(Id_FRS), 0) + 1 AS next_id FROM FRS");
    $next    = $maxStmt->fetch();
    $newId   = $next["next_id"];

    $ins = $pdo->prepare("
        INSERT INTO FRS (Id_FRS, Id_Semester, NPM)
        VALUES (?, ?, ?)
    ");
    $ins->execute([$newId, $id_semester, $npm]);

    echo json_encode(["id_frs" => $newId]);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
