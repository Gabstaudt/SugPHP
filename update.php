<?php
// Define o tipo de conteúdo da resposta como JSON
header('Content-Type: application/json');

// Obtém o conteúdo da requisição HTTP no formato JSON e o decodifica para um array associativo
$data = json_decode(file_get_contents('php://input'), true);

// Define as credenciais e informações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "water_usage";

// Cria uma nova conexão com o banco de dados MySQL usando as credenciais fornecidas
$conn = new mysqli($servername, $username, $password, $dbname);

// Checa se houve um erro na conexão com o banco de dados
if ($conn->connect_error) {
    // Se houver um erro, exibe uma mensagem de erro e encerra a execução do script
    die("Connection failed: " . $conn->connect_error);
}

// Obtém os valores do array associativo decodificado e atribui às variáveis
$id = $data['id'];
$description = $data['description'];
$volume = $data['volume'];

// Prepara uma instrução SQL para atualizar o registro com o ID fornecido na tabela 'usage'
$sql = "UPDATE usage SET description='$description', volume='$volume' WHERE id=$id";

// Executa a instrução SQL
if ($conn->query($sql) === TRUE) {
    // Se a execução for bem-sucedida, retorna um status de sucesso em formato JSON
    echo json_encode(["status" => "success"]);
} else {
    // Se houver um erro na execução, retorna um status de erro e a mensagem de erro em formato JSON
    echo json_encode(["status" => "error", "message" => $conn->error]);
}

// Fecha a conexão com o banco de dados
$conn->close();
?>
