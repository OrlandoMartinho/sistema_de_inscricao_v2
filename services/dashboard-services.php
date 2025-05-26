<?php

// Buscar estatísticas
$stats = [
    'eventos' => 0,
    'contactos' => 0,
    'inscricoes' => 0,
    'usuarios' => 0,
    'atividades' => []
];

// Contar eventos recentes (últimos 7 dias)
$stmt = $conn->prepare("SELECT COUNT(*) FROM eventos WHERE data_criacao >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmt->execute();
$stmt->bind_result($stats['eventos']);
$stmt->fetch();
$stmt->close();

// Contar contactos não lidos
$stmt = $conn->prepare("SELECT COUNT(*) FROM contactos WHERE status = 'não lido'");
$stmt->execute();
$stmt->bind_result($stats['contactos']);
$stmt->fetch();
$stmt->close();

// Contar inscrições recentes (últimos 7 dias)
$stmt = $conn->prepare("SELECT COUNT(*) FROM inscricoes WHERE data_inscricao >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
$stmt->execute();
$stmt->bind_result($stats['inscricoes']);
$stmt->fetch();
$stmt->close();

// Contar usuários ativos
$stmt = $conn->prepare("SELECT COUNT(*) FROM usuarios WHERE status = 'ativo'");
$stmt->execute();
$stmt->bind_result($stats['usuarios']);
$stmt->fetch();
$stmt->close();

// Buscar atividades recentes
$stmt = $conn->prepare("SELECT acao, usuario, data_acao FROM atividades ORDER BY data_acao DESC LIMIT 5");
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $stats['atividades'][] = $row;
}
$stmt->close();

$conn->close();

?>