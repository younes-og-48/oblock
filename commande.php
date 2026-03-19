<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Méthode non autorisée']);
    exit;
}

require_once 'connexion.php';

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Données invalides']);
    exit;
}

$prenom  = trim($data['prenom'] ?? '');
$nom     = trim($data['nom'] ?? '');
$tel     = trim($data['telephone'] ?? '');
$ville   = trim($data['ville'] ?? '');
$adresse = trim($data['adresse'] ?? '');
$articles= $data['articles'] ?? [];
$total   = intval($data['total'] ?? 0);

if (!$prenom || !$nom || !$tel || !$ville || !$adresse || empty($articles)) {
    echo json_encode(['success' => false, 'message' => 'Champs manquants']);
    exit;
}

$articlesJson = json_encode($articles, JSON_UNESCAPED_UNICODE);

try {
    $stmt = $pdo->prepare("
        INSERT INTO commandes (prenom, nom, telephone, ville, adresse, articles, total)
        VALUES (:prenom, :nom, :telephone, :ville, :adresse, :articles, :total)
    ");
    $stmt->execute([
        ':prenom'    => $prenom,
        ':nom'       => $nom,
        ':telephone' => $tel,
        ':ville'     => $ville,
        ':adresse'   => $adresse,
        ':articles'  => $articlesJson,
        ':total'     => $total
    ]);

    $id = $pdo->lastInsertId();
    echo json_encode(['success' => true, 'id' => $id]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Erreur base de données']);
}
?>