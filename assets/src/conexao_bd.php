<?php


//criar conexão com a base de dados 

$name_host="localhost";
$name_root="root";
$name_senha="";
$name_bd="sistema_inscricao";

$conn= new mysqli($name_host,$name_root,$name_senha,$name_bd);

//verificar se a conexão foi feita com sucesso

if($conn->connect_errno)
{
   // echo "erro na conexão com o banco de dados";
}
else
{
  //  echo "conexão efectuda com sucesso";
}

?>