var PANIER = {
  items: [],

  init: function() {
    try {
      var saved = localStorage.getItem('oblock_panier');
      if (saved) this.items = JSON.parse(saved);
    } catch(e) { this.items = []; }
    this.updateBadge();
  },

  save: function() {
    localStorage.setItem('oblock_panier', JSON.stringify(this.items));
    this.updateBadge();
  },

  add: function(produit, prix, couleur, taille, qte, image) {
    var id = produit + '-' + couleur + '-' + taille;
    var found = false;
    for (var i = 0; i < this.items.length; i++) {
      if (this.items[i].id === id) {
        this.items[i].qte += qte;
        found = true; break;
      }
    }
    if (!found) this.items.push({ id:id, produit:produit, prix:prix, couleur:couleur, taille:taille, qte:qte, image:image });
    this.save();
    this.showToast();
  },

  remove: function(id) {
    var n = [];
    for (var i = 0; i < this.items.length; i++) if (this.items[i].id !== id) n.push(this.items[i]);
    this.items = n;
    this.save();
    this.renderPanel();
  },

  changeQte: function(id, delta) {
    for (var i = 0; i < this.items.length; i++) {
      if (this.items[i].id === id) {
        this.items[i].qte += delta;
        if (this.items[i].qte <= 0) { this.remove(id); return; }
        break;
      }
    }
    this.save();
    this.renderPanel();
  },

  total: function() {
    var s = 0;
    for (var i = 0; i < this.items.length; i++) s += this.items[i].prix * this.items[i].qte;
    return s;
  },

  count: function() {
    var s = 0;
    for (var i = 0; i < this.items.length; i++) s += this.items[i].qte;
    return s;
  },

  updateBadge: function() {
    var badges = document.querySelectorAll('.cart-badge');
    var count = this.count();
    for (var i = 0; i < badges.length; i++) {
      badges[i].textContent = count;
      badges[i].style.display = count > 0 ? 'flex' : 'none';
    }
  },

  showToast: function() {
    var t = document.getElementById('cartToast');
    if (!t) return;
    t.classList.add('show');
    setTimeout(function(){ t.classList.remove('show'); }, 2500);
  },

  renderPanel: function() {
    var body = document.getElementById('panierBody');
    var footer = document.getElementById('panierFooter');
    if (!body) return;

    if (this.items.length === 0) {
      body.innerHTML = ''
        + '<div class="panier-empty">'
        + '<div class="panier-empty-icon">🛒</div>'
        + '<p>Ton panier est vide</p>'
        + '<span>Ajoute des articles pour commander</span>'
        + '</div>';
      if (footer) footer.style.display = 'none';
      return;
    }

    if (footer) footer.style.display = 'flex';

    var html = '';
    for (var i = 0; i < this.items.length; i++) {
      var item = this.items[i];
      html += '<div class="panier-item">'
        + '<div class="panier-item-img" style="background-image:url(' + item.image + ');"></div>'
        + '<div class="panier-item-info">'
        + '<p class="panier-item-name">' + item.produit + '</p>'
        + '<div class="panier-item-meta">'
        + '<span class="panier-tag">' + item.couleur + '</span>'
        + '<span class="panier-tag">' + item.taille + '</span>'
        + '</div>'
        + '<div class="panier-item-bottom">'
        + '<span class="panier-item-prix">' + (item.prix * item.qte) + ' DH</span>'
        + '<div class="panier-item-qty">'
        + '<button onclick="PANIER.changeQte(\'' + item.id + '\',-1)" class="panier-qty-btn">−</button>'
        + '<span>' + item.qte + '</span>'
        + '<button onclick="PANIER.changeQte(\'' + item.id + '\',1)" class="panier-qty-btn">+</button>'
        + '</div>'
        + '<button onclick="PANIER.remove(\'' + item.id + '\')" class="panier-remove">✕</button>'
        + '</div>'
        + '</div>'
        + '</div>';
    }
    body.innerHTML = html;
    var t = document.getElementById('panierTotal');
    if (t) t.textContent = this.total() + ' DH';
  },

  goStep: function(n) {
    document.getElementById('panierStep1').style.display = n === 1 ? 'flex' : 'none';
    document.getElementById('panierStep2').style.display = n === 2 ? 'flex' : 'none';
    document.getElementById('panierStep3').style.display = n === 3 ? 'flex' : 'none';
  },

  confirmer: function() {
  var prenom  = document.getElementById('pPrenom').value.trim();
  var nom     = document.getElementById('pNom').value.trim();
  var tel     = document.getElementById('pTel').value.trim();
  var ville   = document.getElementById('pVille').value.trim();
  var adresse = document.getElementById('pAdresse').value.trim();
  var err     = document.getElementById('panierFormError');

  if (!prenom || !nom || !tel || !ville || !adresse) {
    if (err) err.style.display = 'block';
    return;
  }
  if (err) err.style.display = 'none';

  var self = this;
  var data = {
    prenom:    prenom,
    nom:       nom,
    telephone: tel,
    ville:     ville,
    adresse:   adresse,
    articles:  this.items,
    total:     this.total()
  };

  fetch('commande.php', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(data)
  })
  .then(function(r){ return r.json(); })
  .then(function(res){
    if (res.success) {
      self.goStep(3);
      self.items = [];
      self.save();
    } else {
      alert('Erreur : ' + res.message);
    }
  })
  .catch(function(){
    alert('Erreur de connexion. Réessaie.');
  });
},

  vider: function() {
    this.items = [];
    this.save();
    this.renderPanel();
  },

  open: function() {
    this.goStep(1);
    this.renderPanel();
    document.getElementById('panierOverlay').classList.add('open');
    document.body.style.overflow = 'hidden';
  },

  close: function() {
    document.getElementById('panierOverlay').classList.remove('open');
    document.body.style.overflow = '';
  }
};

document.addEventListener('DOMContentLoaded', function() {
  PANIER.init();

  var cartIcon = document.querySelector('.cart-container');
  if (cartIcon) cartIcon.addEventListener('click', function(){ PANIER.open(); });

  var closeBtn = document.getElementById('panierClose');
  if (closeBtn) closeBtn.addEventListener('click', function(){ PANIER.close(); });

  var overlay = document.getElementById('panierOverlay');
  if (overlay) overlay.addEventListener('click', function(e){ if (e.target === overlay) PANIER.close(); });
});