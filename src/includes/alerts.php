<?php if(isset($_SESSION['success'])){ ?>
<div class="alert alert-success alert-dismissible fade show" role="alert">
  <strong>Success!</strong> <?php echo $_SESSION['success'];?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	<span aria-hidden="true">&times;</span>
  </button>
</div>
<?php unset($_SESSION['success']); } ?>
<?php if(isset($_SESSION['error'])){ ?>
<div class="alert alert-danger alert-dismissible fade show" role="alert">
  <strong>Error!</strong> <?php echo $_SESSION['error'];?>
  <button type="button" class="close" data-dismiss="alert" aria-label="Close">
	<span aria-hidden="true">&times;</span>
  </button>
</div>
<?php unset($_SESSION['error']); } ?>