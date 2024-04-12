<?php include 'header.php'; ?>

<div id="articleCarousel" class="carousel slide" data-ride="carousel" data-interval="3000">
  <!-- Indicateurs -->
  <ol class="carousel-indicators">
    <li data-target="#articleCarousel" data-slide-to="0" class="active"></li>
    <li data-target="#articleCarousel" data-slide-to="1"></li>
    <li data-target="#articleCarousel" data-slide-to="2"></li>
    <!-- Ajoutez autant d'indicateurs que vous avez de .carousel-item -->
  </ol>

  <!-- Wrapper pour les slides -->
  <div class="carousel-inner">
    <div class="carousel-item active">
      <div class="d-flex justify-content-start">
        <!-- Première slide avec vos articles -->
        <!-- Assurez-vous que vos cartes correspondent à la largeur du carrousel et sont scrollable horizontalement -->
        <div class="card mr-2" >
          <img src="../images/logo.png" class="card-img-top" alt="Styled Bandanas">
          <div class="card-body">
            <h5 class="card-title">Article</h5>
            <p class="card-text">Description</p>
            <a href="#" class="btn btn-primary">Read More</a>
          </div>
        </div>
        <div class="card mr-2" >
          <img src="../images/logo.png" class="card-img-top" alt="Styled Bandanas">
          <div class="card-body">
            <h5 class="card-title">Article</h5>
            <p class="card-text">Description</p>
            <a href="#" class="btn btn-primary">Read More</a>
          </div>
        </div>
        
        <!-- Répétez pour autant d'articles que vous voulez dans la première slide -->
        <!-- ... -->
      </div>
    </div>
    <!-- Répétez pour les autres slides -->
    <div class="carousel-item">
      <div class="d-flex justify-content-start">
      <div class="card mr-2">
          <img src="../images/logo.png" class="card-img-top" alt="Styled Bandanas">
          <div class="card-body">
            <h5 class="card-title">Article</h5>
            <p class="card-text">Description</p>
            <a href="#" class="btn btn-primary">Read More</a>
          </div>
        </div>
        <div class="card mr-2">
          <img src="../images/logo.png" class="card-img-top" alt="Styled Bandanas">
          <div class="card-body">
            <h5 class="card-title">Article</h5>
            <p class="card-text">Description</p>
            <a href="#" class="btn btn-primary">Read More</a>
          </div>
        </div>
      </div>
    </div>
    <!-- ... -->
  </div>

  <!-- Contrôles -->
  <a class="carousel-control-next" href="#articleCarousel" role="button" data-slide="next">
    <span class="carousel-control-next-icon" aria-hidden="true"></span>
    <span class="sr-only">Suivant</span>
  </a>
</div>

<?php include 'footer.php'; ?>
