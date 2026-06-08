<?php
session_start();
header("Content-Type: application/json");
require 'db.php';

$data     = json_decode(file_get_contents("php://input"), true);
$email    = trim($data["email"]    ?? "");
$password = trim($data["password"] ?? "");

if (!$email || !$password) {
    http_response_code(400);
    echo json_encode(["error" => "Email dan password harus diisi."]);
    exit;
}

// Check Mahasiswa
$stmt = $pdo->prepare("SELECT * FROM Mahasiswa WHERE Email = ? AND Password = ?");
$stmt->execute([$email, $password]);
$mahasiswa = $stmt->fetch();

if ($mahasiswa) {
    $_SESSION["user"] = $mahasiswa;
    $_SESSION["role"] = "mahasiswa";
    $_SESSION["npm"]  = $mahasiswa["NPM"];
    echo json_encode([
        "role" => "mahasiswa",
        "nama" => $mahasiswa["Nama"],
        "npm"  => $mahasiswa["NPM"]
    ]);
    exit;
}

// Cek Dosen
$stmt = $pdo->prepare("SELECT * FROM Dosen WHERE Email = ? AND Password = ?");
$stmt->execute([$email, $password]);
$dosen = $stmt->fetch();

if ($dosen) {
    $_SESSION["user"]     = $dosen;
    $_SESSION["role"]     = "dosen";
    $_SESSION["id_dosen"] = $dosen["Id_Dosen"];
    echo json_encode([
        "role"     => "dosen",
        "nama"     => $dosen["Nama"],
        "id_dosen" => $dosen["Id_Dosen"]
    ]);
    exit;
}

http_response_code(401);
echo json_encode(["error" => "Email atau Password salah!"]);
?>
