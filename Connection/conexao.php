<?php 
// Credenciais de acesso ao BD
// define('HOST', 'mysql.prizor.com.br');
// define('USER', 'prizor02');
// define('PASS', 'brasil2009');
// define('DBNAME', 'prizor02');

// $conn = new PDO('mysql:host='.HOST.';dbname='.DBNAME.';', USER, PASS);
//session_start();

$hostname_conexao = "localhost";
$database_conexao = "am_bf_venancio";
$username_conexao = "root";
$password_conexao = "";

global $conn;

try {
    $conn = new PDO("mysql:host=$hostname_conexao;dbname=$database_conexao", $username_conexao, $password_conexao);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "ERRO: ".$e->getMessage();
}
?>