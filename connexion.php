<?php
$host     = 'sql113.infinityfree.com';
$dbname   = 'if0_41424629_oblock';
$user     = 'if0_41424629';
$password = 'dq5ibJKZx0l8';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Connexion échouée']);
    exit;
}
?>