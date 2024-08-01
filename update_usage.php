<?php
header('Content-Type: application/json');
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "water_usage";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die("Conexão falhou: " . $conn->connect_error);
}

// Receber dados JSON
$data = json_decode(file_get_contents('php://input'), true);

$id = $data['id'];
$description = $data['description'];
$volume = $data['volume'];

// Atualizar dados
$sql = "UPDATE usage SET description = ?, volume = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sdi', $description, $volume, $id);

$response = array();
if ($stmt->execute()) {
    $response['status'] = 'success';
} else {
    $response['status'] = 'error';
}

echo json_encode($response);

$stmt->close();
$conn->close();
?>
