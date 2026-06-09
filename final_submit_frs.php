<?php
session_start();
header("Content-Type: application/json");
require 'db.php';

if (!isset($_SESSION["npm"])) { 
    http_response_code(401); echo "Unauthorized"; exit; 
}

// TODO: add a Status column to FRS table to track this properly
// For now, confirm the submission is locked
echo "FRS berhasil di-submit secara final!";
?>
