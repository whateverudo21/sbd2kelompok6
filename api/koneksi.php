<?php
header('Content-Type: application/json');

$host     = getenv('bammaxdiye7l1w3j4xvz-mysql.services.clever-cloud.com');
$user     = getenv('uvxhattlrwupu6gj');
$password = getenv('7khOYF1SeR5ZTM8UY9VB');
$database = getenv('bammaxdiye7l1w3j4xvz');
$port     = getenv('3306') ?: 3306;

$koneksi = mysqli_connect($host, $user, $password, $database, $port);

if (!$koneksi) {
    die(json_encode(["status" => "gagal", "pesan" => "Koneksi database Clever Cloud gagal: " . mysqli_connect_error()]));
}
?>
