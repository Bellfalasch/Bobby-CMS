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
		
		outputErrors($_SESSION['ERRORS']);

		echo $errorMsg;
	?>

<form class="well form-inline" action="" method="post" enctype="multipart/form-data">

	<div class="row">
		<div class="span12">

			<input size="25" name="file" type="file" />

			<p>&nbsp;</p>

			<ul>
				<li>Supports JPG, PNG, GIF</li>
				<li>Maximum filesize: <strong><?= round(IMG_MAX_SIZE / 1024) ?> MB</strong></li>
				<li>To replace a file, upload a file with the same filename as an existing one.</li>
				<hr />
				<li>Frontpage image: will be shrunk to <strong><?= IMG_BIG_WIDTH ?></strong> pixels wide
					<?php if (IMG_BIG_HEIGHT > 0) { ?>
					and <strong><?= IMG_BIG_HEIGHT ?></strong> pixels high.
					<?php } ?>
				</li>
				<li>Standard size: will be shrunk to <strong><?= IMG_MAX_WIDTH ?></strong> pixels wide
					<?php if (IMG_MAX_HEIGHT > 0) { ?>
					and <strong><?= IMG_MAX_HEIGHT ?></strong> pixels high.
					<?php } ?>
				</li>
				<li>Thumbnail: will be shrunk to <strong><?= IMG_THUMB_WIDTH ?></strong> pixels wide
					<?php if (IMG_THUMB_HEIGHT > 0) { ?>
					and <strong><?= IMG_THUMB_HEIGHT ?></strong> pixels high.
					<?php } ?>
				</li>
			</ul>

			<hr />
			
			<button type="submit" id="spara" name="spara" class="btn btn-primary">Upload</button>

		</div>
	</div>

</form>


<?php require('_footer.php'); ?>