<?php
session_start();
header("Content-Type: application/json");

include "koneksidb.php";

// 1. Cek Session (Pastikan menggunakan id_dosen)
$id_dosen = $_SESSION["id_dosen"] ?? null;

if (!$id_dosen) {
    echo json_encode(["status" => "error", "message" => "SESSION ERROR: Anda belum login."]);
    exit;
}

// 2. Ambil ID dari body fetch JavaScript (JSON format)
$data = json_decode(file_get_contents("php://input"), true);
$id_jadwal = $data["id"] ?? null;

if (!$id_jadwal) {
    echo json_encode(["status" => "error", "message" => "ID KOSONG: Data ID tidak terkirim dari sistem."]);
    exit;
}

// 3. Eksekusi Query (Gunakan MySQLi sesuai dengan koneksidb.php Anda)
$sql = "DELETE FROM Jadwal_Matkul WHERE Id_Jadwal = ? AND Id_Dosen = ?";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    echo json_encode(["status" => "error", "message" => "Query Error: " . $conn->error]);
    exit;
}

// "ii" berarti dua parameter bertipe Integer (id_jadwal dan id_dosen)
$stmt->bind_param("ii", $id_jadwal, $id_dosen);
$stmt->execute();

// 4. Cek hasil eksekusi penghapusan
if ($stmt->affected_rows > 0) {
    echo json_encode(["status" => "success", "message" => "Jadwal berhasil dihapus."]);
} else {
    echo json_encode(["status" => "error", "message" => "Gagal: Jadwal tidak ditemukan atau bukan milik Anda."]);
}
?>