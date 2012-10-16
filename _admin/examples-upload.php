<?php
	/* Set up template variables */
	$PAGE_name  = 'Upload';
	$PAGE_title = 'Admin/' . $PAGE_name;
?>
<?php include('_header.php'); ?>


<?php

	if (ISPOST)
	{
		
		if (isset($HTTP_POST_FILES)) {
			$path = "uploads/" . $HTTP_POST_FILES['ufile']['name'];

			if ( copy($HTTP_POST_FILES['ufile']['tmp_name'], $path) ) {
				
				echo '<div class="alert alert-success"><h4>Upload successful</h4>';
				echo "File Name: ".$HTTP_POST_FILES['ufile']['name']."<br />"; 
				echo "File Size: ".$HTTP_POST_FILES['ufile']['size']."<br />"; 
				echo "File Type: ".$HTTP_POST_FILES['ufile']['type']."<br />"; 
				echo "</div>";

			}
		} else {
			pushError("Can't find required support on the server for file uploading =/");
		}

	}

?>

	<div class="page-header">
		<h1>
			Upload
			<small>Upload new file</small>
		</h1>
	</div>

	<?php
		if (!empty($_SESSION['ERRORS']))
		{
			outputErrors($_SESSION['ERRORS']);
		}
	?>

	<div class="row">
		<div class="span12">

			<form class="well form-inline" action="" method="post" enctype="multipart/form-data">
				
				<p>
					Select a file to upload (one at-a-time).
				</p>

				<input type="file" name="ufile" id="ufile" />

				<button type="submit" class="btn">Upload</button>

			</form>

		</div>
	</div>


<?php require('_footer.php'); ?>