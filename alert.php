<?php
if (!empty($_SESSION['error'])) {
	?>
	<div class="alert alert-error">
		<a class="close" data-dismiss="alert" href="#">×</a>
		<strong><?php echo $_SESSION['error']['title']; ?></strong>
		<?php echo $_SESSION['error']['content']; ?>
	</div>
	<?php
	$_SESSION['error'] = null;
};
