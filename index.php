<?php include 'header.php'; ?>

<div class="container">
  <div class="row">
    <!-- Contenu principal -->
    <div class="col-lg-8">
    <h1> Selection de la redaction <h1>  
      <div class="card mb-3 main-content-card">
        <img src="../images/Vultures_logo.png" class="card-img-top" alt="Main Content Image">
        <div class="card-body">
          <h5 class="card-title">Baisse du Bitcoin (BTC)</h5>
          <p class="card-text">Le marché des cryptomonnaies a subi une chute généralisée, avec le Bitcoin en tête...</p>
          <p class="card-text"><small class="text-muted">Dernière mise à jour il y a 3 minutes</small></p>
        </div>
      </div>
    </div>
    <!-- Nouvelles secondaires -->
    <div class="col-lg-4">
      <div class="card secondary-news-card">
        <div class="card-body">
          <h6 class="card-subtitle mb-2 text-muted">Nouvelles secondaires</h6>
          <!-- Item de nouvelle secondaire -->
          <div class="media mb-3">
            <img src="../images/Vultures_logo.png" class="mr-3" alt="...">
            <div class="media-body">
              <h5 class="mt-0">Venezuela : l'ancien ministre de l'économie...</h5>
              <p class="card-text"><small class="text-muted">Hier à 15h00</small></p>
            </div>
          </div>
          <!-- Répétez pour chaque nouvelle secondaire -->
        </div>
      </div>
    </div>
  </div>
  <div class="newsletter-content">
    <h2>Newsletter</h2>
    <p>Inscrivez-vous pour recevoir les dernières informations.</p>
    <form class="subscription-form" action="add_email_process.php" method="post">
        <input type="email" name="email" placeholder="Adresse e-mail" required>
        <button type="submit">S'inscrire</button>
    </form>
  </div>
</div>

<?php include 'footer.php'; ?>
