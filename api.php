<?php
// Define as credenciais e informações de conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "water_usage";

// Cria uma nova conexão com o banco de dados MySQL usando as credenciais 
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica se tiver erro na conexão com o banco de dados
if ($conn->connect_error) {
    // Se houver um erro, exibe a mensagem e encerra a execução do script
    die("Connection failed: " . $conn->connect_error);
}
// Verifica se a requisição HTTP é do tipo POST

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Obtém os dados enviados pelo método POST
    $usage_date = $_POST['usage_date'];
    $liters_used = $_POST['liters_used'];

    $stmt = $conn->prepare("INSERT INTO usage (usage_date, liters_used) VALUES (?, ?)");
    $stmt->bind_param("si", $usage_date, $liters_used);

    if ($stmt->execute()) {
        echo json_encode(["message" => "Data saved successfully"]);
    } else {
        echo json_encode(["message" => "Error saving data"]);
    }
    // Fecha a instrução preparada

    $stmt->close();
} else {
    $sql = "SELECT usage_date, liters_used FROM usage";
    $result = $conn->query($sql);
// Inicializa um array para armazenar os dados
    $data = [];
    if ($result->num_rows > 0) {
         // Percorre os resultados e adiciona cada linha ao array
        while ($row = $result->fetch_assoc()) {
            $data[] = $row;
        }
    }
    echo json_encode($data);
}
// Fecha a conexão com o banco de dados

$conn->close();
?>
