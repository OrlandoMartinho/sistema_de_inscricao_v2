<?php


$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistema_de_inscricao_v2";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}



?>