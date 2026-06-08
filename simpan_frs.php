<?php
// simpan_frs.php
// Menyimpan mata kuliah yang diselect ke dalam tabel Enroll 
// Digunakan pengisian_frs.html (Simpan FRS button)

session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) {
    http_response_code(401);
    echo json_encode(["error" => "Sesi tidak ditemukan. Silakan login ulang."]);
    exit;
}

$data        = json_decode(file_get_contents("php://input"), true);
$npm         = $_SESSION["npm"];
$id_frs      = $data["id_frs"]      ?? null;
$id_semester = $data["id_semester"] ?? null;
$matkul_list = $data["matkul"]      ?? [];

if (!$id_frs || !$id_semester || empty($matkul_list)) {
    http_response_code(400);
    echo json_encode(["error" => "Data tidak lengkap."]);
    exit;
}

try {
    $pdo->beginTransaction();

    // Remove enrollment yang sudah ada di FRS (re-saving diperbolehkan)
    $del = $pdo->prepare("DELETE FROM Enroll WHERE Id_FRS = ?");
    $del->execute([$id_frs]);

    // Insert setiap matkul yang terselect
    $ins = $pdo->prepare("
        INSERT INTO Enroll (Tanggal_Ambil, Kode_Matkul, Id_Semester, Id_FRS)
        VALUES (GETDATE(), ?, ?, ?)
    ");

    foreach ($matkul_list as $kode) {
        $ins->execute([$kode, $id_semester, $id_frs]);
    }

    $pdo->commit();
    echo json_encode(["success" => true, "pesan" => "FRS berhasil disimpan!"]);

} catch (PDOException $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo json_encode(["error" => $e->getMessage()]);
}
?>
