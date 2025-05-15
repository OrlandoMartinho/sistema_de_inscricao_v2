<?php
$host = "localhost";     // Endereço do servidor
$usuario = "root";        
$senha = "";            
$banco = "sistema_de_inscricao_v2"; 

// Criando a conexão
$conn = new mysqli($host, $usuario, $senha, $banco);

// Verificando a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
} else {
    echo "Conexão bem-sucedida!";
}
?>
