<?php
    require_once "header.php";
    if(isset($_COOKIE["name"])){
?>
<div class="container">
<div class="alert alert-primary alert-dismissible fade show" role="alert">
  <?php echo "Welcome back, " . ucfirst($_COOKIE["name"]);?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
    <span aria-hidden="true"><i class="fa fa-times"></i></span>
  </button>
</div>
</div>
<?php } ?>
    <h1 class="h1 text-primary text-center  m-5">Check out our new and improved blog!</h1>
<?php
    require_once "footer.php";
?>