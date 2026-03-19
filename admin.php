<?php
session_start();

define('ADMIN_USER', 'oblock');
define('ADMIN_PASS', 'oblock2026');

if (isset($_POST['login'])) {
    if ($_POST['username'] === ADMIN_USER && $_POST['password'] === ADMIN_PASS) {
        $_SESSION['admin'] = true;
    } else {
        $erreur = 'Identifiants incorrects';
    }
}

if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: admin.php');
    exit;
}

if (isset($_POST['statut']) && isset($_POST['id']) && isset($_SESSION['admin'])) {
    require_once 'connexion.php';
    $stmt = $pdo->prepare("UPDATE commandes SET statut = :statut WHERE id = :id");
    $stmt->execute([':statut' => $_POST['statut'], ':id' => intval($_POST['id'])]);
    header('Location: admin.php');
    exit;
}

if (!isset($_SESSION['admin'])) {
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OBLOCK — Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{background:#0a0a0a;font-family:'Outfit',sans-serif;min-height:100vh;display:flex;align-items:center;justify-content:center;}
    .login-box{background:#111;border-radius:24px;padding:48px 40px;width:100%;max-width:380px;border:1px solid rgba(255,255,255,0.06);}
    .login-logo{font-family:'Bebas Neue',sans-serif;font-size:48px;letter-spacing:0.15em;color:#fff;text-align:center;margin-bottom:8px;}
    .login-sub{text-align:center;font-size:12px;color:#666;letter-spacing:0.15em;text-transform:uppercase;margin-bottom:36px;}
    .login-group{display:flex;flex-direction:column;gap:6px;margin-bottom:16px;}
    .login-group label{font-size:11px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#666;}
    .login-group input{background:rgba(255,255,255,0.05);border:1.5px solid rgba(255,255,255,0.1);border-radius:12px;padding:12px 16px;font-family:'Outfit',sans-serif;font-size:14px;color:#fff;outline:none;transition:border-color 0.2s;}
    .login-group input:focus{border-color:#fff;}
    .login-btn{width:100%;padding:15px;background:#fff;color:#0a0a0a;border:none;border-radius:12px;font-family:'Outfit',sans-serif;font-size:15px;font-weight:600;cursor:pointer;margin-top:8px;transition:opacity 0.2s;}
    .login-btn:hover{opacity:0.85;}
    .login-err{background:rgba(220,38,38,0.1);border:1px solid rgba(220,38,38,0.3);color:#f87171;font-size:13px;padding:10px 14px;border-radius:10px;margin-bottom:16px;text-align:center;}
  </style>
</head>
<body>
  <div class="login-box">
    <div class="login-logo">OBLOCK</div>
    <div class="login-sub">— Panel Admin</div>
    <?php if (isset($erreur)): ?>
      <div class="login-err">⚠ <?= htmlspecialchars($erreur) ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="login-group">
        <label>Identifiant</label>
        <input type="text" name="username" placeholder="oblock" required>
      </div>
      <div class="login-group">
        <label>Mot de passe</label>
        <input type="password" name="password" placeholder="••••••••" required>
      </div>
      <button type="submit" name="login" class="login-btn">Connexion →</button>
    </form>
  </div>
</body>
</html>
<?php
    exit;
}

require_once 'connexion.php';

$filtre = $_GET['statut'] ?? 'tous';
$search = $_GET['search'] ?? '';

$sql = "SELECT * FROM commandes WHERE 1=1";
$params = [];

if ($filtre !== 'tous') {
    $sql .= " AND statut = :statut";
    $params[':statut'] = $filtre;
}
if ($search) {
    $sql .= " AND (nom LIKE :s OR prenom LIKE :s2 OR telephone LIKE :s3 OR ville LIKE :s4)";
    $params[':s'] = '%'.$search.'%';
    $params[':s2'] = '%'.$search.'%';
    $params[':s3'] = '%'.$search.'%';
    $params[':s4'] = '%'.$search.'%';
}

$sql .= " ORDER BY date_commande DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$commandes = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stats = $pdo->query("SELECT statut, COUNT(*) as nb, SUM(total) as total FROM commandes GROUP BY statut")->fetchAll(PDO::FETCH_ASSOC);
$totalCommandes = $pdo->query("SELECT COUNT(*) FROM commandes")->fetchColumn();
$totalRevenu = $pdo->query("SELECT SUM(total) FROM commandes WHERE statut != 'annulée'")->fetchColumn();

$couleurStatut = [
    'en attente' => '#F59E0B',
    'confirmée'  => '#3B82F6',
    'expédiée'   => '#10B981',
    'annulée'    => '#EF4444'
];
?>
<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>OBLOCK — Admin</title>
  <link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    *{box-sizing:border-box;margin:0;padding:0}
    body{background:#0a0a0a;color:#fff;font-family:'Outfit',sans-serif;min-height:100vh;}
    .topbar{background:#111;border-bottom:1px solid rgba(255,255,255,0.06);padding:0 32px;height:64px;display:flex;align-items:center;justify-content:space-between;}
    .topbar-logo{font-family:'Bebas Neue',sans-serif;font-size:28px;letter-spacing:0.15em;}
    .topbar-right{display:flex;align-items:center;gap:16px;}
    .topbar-right span{font-size:13px;color:#666;}
    .logout-btn{background:rgba(255,255,255,0.06);border:1px solid rgba(255,255,255,0.1);color:#fff;padding:8px 18px;border-radius:8px;font-family:'Outfit',sans-serif;font-size:13px;cursor:pointer;text-decoration:none;transition:background 0.2s;}
    .logout-btn:hover{background:rgba(255,255,255,0.12);}
    .main{padding:32px;}
    .stats-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:32px;}
    .stat-card{background:#111;border:1px solid rgba(255,255,255,0.06);border-radius:16px;padding:20px 24px;}
    .stat-card .label{font-size:11px;color:#666;text-transform:uppercase;letter-spacing:0.1em;margin-bottom:8px;}
    .stat-card .value{font-family:'Bebas Neue',sans-serif;font-size:36px;letter-spacing:0.05em;}
    .filters{display:flex;gap:10px;margin-bottom:24px;flex-wrap:wrap;align-items:center;}
    .filter-btn{padding:8px 18px;border-radius:8px;border:1px solid rgba(255,255,255,0.1);background:transparent;color:#888;font-family:'Outfit',sans-serif;font-size:13px;cursor:pointer;transition:all 0.2s;text-decoration:none;}
    .filter-btn.active,.filter-btn:hover{background:#fff;color:#0a0a0a;border-color:#fff;}
    .search-input{margin-left:auto;background:rgba(255,255,255,0.05);border:1px solid rgba(255,255,255,0.1);border-radius:8px;padding:8px 16px;color:#fff;font-family:'Outfit',sans-serif;font-size:13px;outline:none;width:220px;}
    .search-input::placeholder{color:#555;}
    .table-wrap{background:#111;border:1px solid rgba(255,255,255,0.06);border-radius:16px;overflow:hidden;}
    table{width:100%;border-collapse:collapse;}
    thead{background:rgba(255,255,255,0.03);}
    th{padding:14px 20px;text-align:left;font-size:11px;font-weight:600;letter-spacing:0.1em;text-transform:uppercase;color:#555;border-bottom:1px solid rgba(255,255,255,0.06);}
    td{padding:16px 20px;font-size:14px;border-bottom:1px solid rgba(255,255,255,0.04);vertical-align:top;}
    tr:last-child td{border-bottom:none;}
    tr:hover td{background:rgba(255,255,255,0.02);}
    .badge{display:inline-block;padding:4px 12px;border-radius:60px;font-size:11px;font-weight:600;letter-spacing:0.08em;}
    .articles-list{font-size:12px;color:#888;line-height:1.6;}
    .statut-form select{background:#1a1a1a;border:1px solid rgba(255,255,255,0.1);color:#fff;padding:6px 10px;border-radius:8px;font-family:'Outfit',sans-serif;font-size:12px;outline:none;cursor:pointer;}
    .statut-form button{background:rgba(255,255,255,0.08);border:none;color:#fff;padding:6px 12px;border-radius:8px;font-family:'Outfit',sans-serif;font-size:12px;cursor:pointer;margin-top:6px;transition:background 0.2s;}
    .statut-form button:hover{background:rgba(255,255,255,0.15);}
    .empty{text-align:center;padding:60px;color:#444;}
    .empty p{font-family:'Bebas Neue',sans-serif;font-size:24px;letter-spacing:0.08em;margin-bottom:8px;}
    @media(max-width:900px){.stats-grid{grid-template-columns:repeat(2,1fr)}.main{padding:16px}}
    @media(max-width:600px){.stats-grid{grid-template-columns:1fr}.topbar{padding:0 16px}.search-input{width:100%;margin-left:0}}
  </style>
</head>
<body>

<div class="topbar">
  <div class="topbar-logo">OBLOCK</div>
  <div class="topbar-right">
    <span>Panel Admin</span>
    <a href="?logout=1" class="logout-btn">Déconnexion</a>
  </div>
</div>

<div class="main">

  <div class="stats-grid">
    <div class="stat-card">
      <div class="label">Total commandes</div>
      <div class="value"><?= $totalCommandes ?></div>
    </div>
    <div class="stat-card">
      <div class="label">Revenu total</div>
      <div class="value"><?= number_format($totalRevenu ?? 0, 0, ',', ' ') ?> DH</div>
    </div>
    <?php foreach($stats as $s): ?>
    <div class="stat-card">
      <div class="label"><?= htmlspecialchars($s['statut']) ?></div>
      <div class="value" style="color:<?= $couleurStatut[$s['statut']] ?? '#fff' ?>"><?= $s['nb'] ?></div>
    </div>
    <?php endforeach; ?>
  </div>

  <form method="GET" style="display:contents">
    <div class="filters">
      <?php foreach(['tous','en attente','confirmée','expédiée','annulée'] as $f): ?>
        <a href="?statut=<?= $f ?>&search=<?= htmlspecialchars($search) ?>"
           class="filter-btn <?= $filtre===$f?'active':'' ?>">
          <?= ucfirst($f) ?>
        </a>
      <?php endforeach; ?>
      <input type="text" name="search" class="search-input"
             placeholder="Rechercher..." value="<?= htmlspecialchars($search) ?>"
             onchange="this.form.submit()">
      <input type="hidden" name="statut" value="<?= htmlspecialchars($filtre) ?>">
    </div>
  </form>

  <div class="table-wrap">
    <?php if (empty($commandes)): ?>
      <div class="empty">
        <p>Aucune commande</p>
        <span>Les commandes apparaîtront ici</span>
      </div>
    <?php else: ?>
    <table>
      <thead>
        <tr>
          <th>#</th>
          <th>Client</th>
          <th>Contact</th>
          <th>Adresse</th>
          <th>Articles</th>
          <th>Total</th>
          <th>Date</th>
          <th>Statut</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($commandes as $c): ?>
        <?php $articles = json_decode($c['articles'], true); ?>
        <tr>
          <td style="color:#555;font-size:13px;">#<?= $c['id'] ?></td>
          <td>
            <strong><?= htmlspecialchars($c['prenom'].' '.$c['nom']) ?></strong>
          </td>
          <td style="color:#888;font-size:13px;">
            <?= htmlspecialchars($c['telephone']) ?>
          </td>
          <td style="font-size:13px;color:#888;">
            <?= htmlspecialchars($c['ville']) ?><br>
            <span style="color:#555;font-size:12px;"><?= htmlspecialchars($c['adresse']) ?></span>
          </td>
          <td>
            <div class="articles-list">
              <?php if($articles): foreach($articles as $a): ?>
                • <?= htmlspecialchars($a['produit']) ?> — <?= htmlspecialchars($a['couleur']) ?> / <?= htmlspecialchars($a['taille']) ?> × <?= $a['qte'] ?><br>
              <?php endforeach; endif; ?>
            </div>
          </td>
          <td>
            <strong style="font-family:'Bebas Neue',sans-serif;font-size:18px;"><?= $c['total'] ?> DH</strong>
          </td>
          <td style="font-size:12px;color:#666;">
            <?= date('d/m/Y H:i', strtotime($c['date_commande'])) ?>
          </td>
          <td>
            <span class="badge" style="background:<?= $couleurStatut[$c['statut']] ?>22;color:<?= $couleurStatut[$c['statut']] ?>">
              <?= htmlspecialchars($c['statut']) ?>
            </span>
            <form method="POST" class="statut-form" style="margin-top:8px;">
              <input type="hidden" name="id" value="<?= $c['id'] ?>">
              <select name="statut">
                <?php foreach(['en attente','confirmée','expédiée','annulée'] as $s): ?>
                  <option value="<?= $s ?>" <?= $c['statut']===$s?'selected':'' ?>><?= ucfirst($s) ?></option>
                <?php endforeach; ?>
              </select><br>
              <button type="submit">Mettre à jour</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </div>

</div>
</body>
</html>
<?php ?>