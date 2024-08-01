<?php
// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Define as credenciais e informações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "water_usage";

// Cria uma nova conexão com o banco de dados MySQL usando as credenciais fornecidas
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se houve um erro na conexão com o banco de dados
if ($conn->connect_error) {
    // Se houver um erro, exibe uma mensagem de erro e encerra a execução do script
    die("Conexão falhou: " . $conn->connect_error);
}

// Prepara e executa uma instrução SQL para consultar todos os registros da tabela 'usage'
$sql = "SELECT id, description, volume FROM usage";
$result = $conn->query($sql);

// Cria um array para armazenar os resultados
$data = array();

// Verifica se há registros retornados pela consulta
if ($result->num_rows > 0) {
    // Itera sobre os registros e adiciona cada um ao array de resultados
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Converte o array de resultados para JSON e exibe
echo json_encode($data);

// Fecha a conexão com o banco de dados
$conn->close();
?>
