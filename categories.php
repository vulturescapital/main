<?php include 'header.php'; ?>
<?php
$stmt = $pdo->query("SELECT * FROM categories");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<div class="container">
  <div class="row">
    <?php foreach ($categories as $category): ?>
      <div class="col-xs-12 col-sm-6 col-md-4 col-lg-3">
        <div class="category-card">
          <img src="<?php echo $category['image']; ?>" class="img-responsive" alt="<?php echo $category['name']; ?>">
          <h3><?php echo $category['name']; ?></h3>
          <p><?php echo $category['description']; ?></p>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</div>
<?php include 'footer.php'; ?>