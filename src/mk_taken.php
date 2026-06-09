<?php
// mk_taken.php
// Returns Kode_Matkul values already enrolled in another FRS (not the target one).
// Used to disable/lock those MK in the Pengisian FRS and Edit FRS UIs.
//
// Two modes (pass one GET param):
//   ?id_semester=X    → exclude MK from the FRS belonging to this student+semester
//   ?exclude_frs=X    → exclude MK from the FRS with this specific Id_FRS
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesi tidak ditemukan."]);
    exit;
}

$npm = $_SESSION["npm"];

try {
    if (isset($_GET["id_semester"])) {
        $id_semester = (int)$_GET["id_semester"];

        // Find the FRS id for this student+semester so we can exclude it
        $stmtFRS = $pdo->prepare("
            SELECT Id_FRS FROM FRS WHERE NPM = ? AND Id_Semester = ?
        ");
        $stmtFRS->execute([$npm, $id_semester]);
        $frs = $stmtFRS->fetch();
        $exclude_frs_id = $frs ? (int)$frs["Id_FRS"] : -1;

    } elseif (isset($_GET["exclude_frs"])) {
        $exclude_frs_id = (int)$_GET["exclude_frs"];

    } else {
        echo json_encode([]);
        exit;
    }

    // Return MK enrolled in any of this student's OTHER FRS records
    $stmt = $pdo->prepare("
        SELECT DISTINCT e.Kode_Matkul
        FROM Enroll e
        JOIN FRS f ON e.Id_FRS = f.Id_FRS
        WHERE f.NPM = ? AND e.Id_FRS <> ?
    ");
    $stmt->execute([$npm, $exclude_frs_id]);
    echo json_encode($stmt->fetchAll());

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
