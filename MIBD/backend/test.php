<?php

if (extension_loaded('sqlsrv')) {
    echo "SQLSRV SUDAH KEDETEKSI";
} else {
    echo "SQLSRV BELUM KEDETEKSI";
}

echo "<br>";

if (extension_loaded('pdo_sqlsrv')) {
    echo "PDO_SQLSRV SUDAH KEDETEKSI";
} else {
    echo "PDO_SQLSRV BELUM KEDETEKSI";
}

?>