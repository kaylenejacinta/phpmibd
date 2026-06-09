<?php
session_start();
include "koneksidb.php";

$data = json_decode(file_get_contents("php://input"), true);

$email = $data["email"] ?? "";
$password = $data["password"] ?? "";

/* ===== DOSEN ===== */
if (str_ends_with($email, "@dosen.id")) {

    $sql = "SELECT * FROM Dosen WHERE Email = ? AND Password = ?";
    $params = array($email, $password);
    $stmt = sqlsrv_query($conn, $sql, $params);

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($row) {
        $_SESSION["id_dosen"] = $row["Id_Dosen"];
        $_SESSION["role"] = "DOSEN";

        echo json_encode([
            "status" => "OK",
            "role" => "DOSEN",
            "id" => $row["Id_Dosen"]
        ]);
    } else {
        echo json_encode(["status" => "FAIL"]);
    }

    exit;
}

/* ===== MAHASISWA ===== */
if (str_ends_with($email, "@student.id")) {

    $sql = "SELECT * FROM Mahasiswa WHERE Email = ? AND Password = ?";
    $params = array($email, $password);
    $stmt = sqlsrv_query($conn, $sql, $params);

    $row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC);

    if ($row) {
        $_SESSION["npm"] = $row["NPM"];
        $_SESSION["role"] = "MAHASISWA";

        echo json_encode([
            "status" => "OK",
            "role" => "MAHASISWA",
            "id" => $row["NPM"]
        ]);
    } else {
        echo json_encode(["status" => "FAIL"]);
    }

    exit;
}

echo json_encode(["status" => "INVALID"]);
?>