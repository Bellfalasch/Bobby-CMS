<?php
	/* Set up template variables */
	$PAGE_name  = 'Image';
	$PAGE_title = 'Admin/' . $PAGE_name;
?>
<?php require('_global.php'); ?>
<?php require('_header.php'); ?>


<?php

	$errorMsg = "";

	define("IMG_MAX_SIZE", 5 * 1024); // Filesize (in kB)
	define("IMG_QUALITY","100");

	define("IMG_MAX_WIDTH","615"); // Width to shrink image to
	define("IMG_MAX_HEIGHT","344"); // Height to shrink image to (0 if not used)
	define("IMG_THUMB_WIDTH","200");
	define("IMG_THUMB_HEIGHT","115");
	define("IMG_BIG_WIDTH","940");
	define("IMG_BIG_HEIGHT","536");

	if ($_SERVER["REQUEST_METHOD"] == "POST")
	{
		# Directory for image
		$dir_blog_org = 'uploads/';
		//$dir_blog_630 = 'images/blog/630/';
		
		# Info about the file
		$filename = $_FILES['file']['name'];
		$path_info = pathinfo($filename);
		$file_ext = strtolower($path_info['extension']);
		$file_ext_whitelist = array('jpg', 'jpeg', 'png', 'gif');
		
		# Se if the file has a valid file extension
		if ( !in_array($file_ext, $file_ext_whitelist) ) {
			//$msg = 'Kun JPG, PNG og GIF filer er tilatt.';
			//$msg_class = 'error';
			$errorMsg = '<div class="alert alert-error"><h4>Warning</h4><p>Unknown Image extension</p></div>';
		} else {
			$time = time();
			//$image = Strip_filename($filename) . $time . '.' . $file_ext;
			$filename = str_replace('.' . $file_ext,'',$filename);
			$image = Strip_filename($filename) . '.' . $file_ext;

			# Upload the image
			if ( move_uploaded_file($_FILES['file']['tmp_name'], $dir_blog_org . $image) ) {
				# Create 630px picture
				createPic($dir_blog_org . $image, "../img/covers/" . $image, IMG_MAX_WIDTH);
				createPic($dir_blog_org . $image, "../img/covers/thumbs/" . $image, IMG_THUMB_WIDTH);
				createPic($dir_blog_org . $image, "../img/covers/big/" . $image, IMG_BIG_WIDTH);

				$errorMsg = '<div class="alert alert-success"><h4>Image Uploaded Successfully</h4><p>' . $image . '</p></div>';

			} else {
				$errorMsg = '<div class="alert alert-error"><h4>Warning</h4><p>Image failed to upload</p></div>';
			}
		}

	}

?>

	<div class="page-header">
		<h1>
			Images
			<small>Upload and manage images</small>
		</h1>
	</div>

	<?php
		if (!empty($_SESSION['ERRORS']))
		{
			outputErrors($_SESSION['ERRORS']);
		}

		echo $errorMsg;
	?>

	<div class="row">
		<div class="span12">

			<?php
				if (isset($_SESSION['id'])) {
			?>
			
			<form class="well form-inline" action="" method="post" enctype="multipart/form-data">
				
				<input size="25" name="file" type="file" />

				<p>&nbsp;</p>

				<ul>
					<li>Støtter JPG, PNG, GIF</li>
					<li>Maksimal filstørrelse: <strong><?= round(IMG_MAX_SIZE / 1024) ?> MB</strong></li>
					<li>Last opp en fil med samme filnavn som en eksisterende, hvis du ønsker å erstatte en fil.</li>
					<hr />
					<li>Forsidebilde: vil krympes ned til <strong><?= IMG_BIG_WIDTH ?></strong> piksle-bredde
						<?php if (IMG_BIG_HEIGHT > 0) { ?>
						og <strong><?= IMG_BIG_HEIGHT ?></strong> piksel-høyde.
						<?php } ?>
					</li>
					<li>Normal bilde: vil krympes ned til <strong><?= IMG_MAX_WIDTH ?></strong> piksle-bredde
						<?php if (IMG_MAX_HEIGHT > 0) { ?>
						og <strong><?= IMG_MAX_HEIGHT ?></strong> piksel-høyde.
						<?php } ?>
					</li>
					<li>Thumbnail: vil krympes ned til <strong><?= IMG_THUMB_WIDTH ?></strong> piksle-bredde
						<?php if (IMG_THUMB_HEIGHT > 0) { ?>
						og <strong><?= IMG_THUMB_HEIGHT ?></strong> piksel-høyde.
						<?php } ?>
					</li>
				</ul>

				<hr />
				
				<button type="submit" id="spara" name="spara" class="btn btn-primary">Last opp</button>

			</form>

			<?php
				} else {
			?>

				<p><a href="<?= $SYS_folder ?>/_admin/login.php">Sign in</a></p>

			<?php
				}
			?>

		</div>
	</div>


<?php require('_footer.php'); ?>