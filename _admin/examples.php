<?php
	/* Set up template variables */
	$PAGE_name  = 'Example';
	$PAGE_title = 'Admin/' . $PAGE_name;
?>
<?php require('_global.php'); ?>
<?php require('_header.php'); ?>


	<div class="page-header">
		<h1>
			<?= $PAGE_name ?>s
			<small>Startpage for <?= $PAGE_name ?>s</small>
		</h1>
	</div>

	<div class="row">
		<div class="span7">

			<p>
				Intro page
			</p>

		</div>


		<div class="span4 offset1">

			...

		</div>
	</div>


<?php require('_footer.php'); ?>