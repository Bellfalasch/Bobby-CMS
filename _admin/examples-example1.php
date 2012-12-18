<?php
	/* Set up template variables */
	$PAGE_name  = 'Example 1';
	$PAGE_title = 'Admin/' . $PAGE_name;
?>
<?php require('_global.php'); ?>

<?php

	// See README.md in root for more information about how to set up and use the form-generator!

	addField( array(
		"label" => "Title:",
		"id" => "Title",
		"type" => "text(3)",
		"description" => "Write a good descriptive title for this post in between [MIN] and [MAX] characters.",
		"min" => "2",
		"max" => "45",
		"null" => false,
		"errors" => array(
						"min" => "Please keep number of character's on at least [MIN].",
						"max" => "Please keep number of character's to [MAX] at most."
					)
	) );

	addField( array(
		"label" => "Wysiwyg:",
		"id" => "Wysiwyg",
		"type" => "wysiwyg(5*5)",
		"description" => "Write a novell!",
		"min" => "1",
		"max" => "10240",
		"null" => true,
		"errors" => array(
						"min" => "Please write at least something here ='(",
						"max" => "Please keep number of character's to [MAX] at most."
					)
	) );

	addField( array(
		"label" => "Mail:",
		"id" => "Mail",
		"type" => "text(5)",
		"min" => "1",
		"max" => "255",
		"errors" => array(
						"min" => "Please submit your e-mail address (we hate spam too and will not flood your mailbox).",
						"max" => "Please keep number of character's to [MAX] at most.",
						"mail" => "Please use a valid e-mail, [CONTENT] is not valid."
					)
	) );

	addField( array(
		"label" => "Minimal:",
		"type" => "text"
	) );

	addField( array(
		"label" => "Zip:",
		"type" => "text(2)",
		"min" => "4",
		"errors" => array(
						"min" => "We need your zip to be able to send free things to you!",
						"exact" => "Not valid format - Please submit exactly four characters in this field.",
						"numeric" => "This field needs to contain only numbers (no letters, no special characters, no spaces, etc)!"
					)
	) );

// I commented out this example of how to get EXACT 4 CHARACTERS and NUMERIC-validation on a field but without demanding you to fill it in, it can thus be left blank!
/*
	addField( array(
		"label" => "Zip 2:",
		"type" => "text(2)",
		"min" => "4",
		"errors" => array(
						"exact" => "Not valid format - Please submit exactly four characters in this field.",
						"numeric" => "This field needs to contain only numbers (no letters, no special characters, no spaces, etc)!"
					)
	) );
*/

	addField( array(
		"label" => "Image:",
		"type" => "folder(3)",
		"settings" => "formats:jpg,jpeg,png,gif; unselectable:Use no image; folder:uploads/;"
	) );

// Another folder-example, this one generates the same folder but you must select one file.
/*	
	// Set up to demand choice of image!
	$fieldImage = array(
		"label" => "Image:",
		"type" => "folder",
		"settings" => "formats:jpg,jpeg,png,gif; folder:uploads/;",
		"min" => "1",
		"errors" => array(
						"min" => "We need you to select a Image for this!"
					)
	);
*/
?>
<?php require('_header.php'); ?>


<?php

		// Deletion of content (comment out if not to be allowed)
		if (isset($_GET['del']) && !ISPOST)
		{
			$del_id = trim( $_GET['del'] );

			$del = db2_delDiscount( array(
						'id' => $del_id
					) );

			if ($del >= 0)
				echo "<div class='alert alert-success'><h4>Delete successful</h4><p>The $PAGE_name is now deleted</p></div>";
			else
				pushError("Delete of $PAGE_name failed, please try again.");
		}


		// User has posted (trying to save changes)
		if (ISPOST)
		{
			validateForm();
			
			var_dump($PAGE_form);

			// If no errors:
			if (empty($_SESSION['ERRORS'])) {
				
				echo "<div class='alert alert-block alert-success'><h4>Success</h4><p><strong>Your posted data validated!</strong> (we have not set this up yet to save to your database =/)</p></div>";

				// UPDATE
				if ( $PAGE_dbid > 0 )
				{
					// CALL YOUR DATABASE AND UPDATE WITH THIS NEW DATA ... (TODO)
/*
					$result = db2_updateCampaign( array(
								'title' => $formTitle,
								'url' => $formUrl,
								'start' => $formStart . ' 00:00:00',
								'stop' => $formStop . ' 23:59:59',
								'short_info' => $formShortInfo,
								'verv_step1' => $formVervStep1,
								'verv_step2' => $formVervStep2,
								'verv_takk' => $formVervTakk,
								'give_step1' => $formGiveStep1,
								'give_takk' => $formGiveTakk,
								'image' => $formImage,
								'id' => $PAGE_dbid
							) );

					if ($result >= 0) {
						echo "<div class='alert alert-success'><h4>Save successful</h4><p>$PAGE_name updated</p></div>";
					} else {
						pushError("NOT saved");
					}
*/
				// CREATE
				} else {

					// CALL YOUR DATABASE AND INSERT THIS NEW DATA ... (TODO)
/*
					$result = db2_createCampaign( array(
								'title' => $formTitle,
								'url' => $formUrl,
								'start' => $formStart . ' 00:00:00',
								'stop' => $formStop . ' 23:59:59',
								'short_info' => $formShortInfo,
								'verv_step1' => $formVervStep1,
								'verv_step2' => $formVervStep2,
								'verv_takk' => $formVervTakk,
								'give_step1' => $formGiveStep1,
								'give_takk' => $formGiveTakk,
								'image' => $formImage
							) );

					if ($result > 0) {
						
						echo "<div class='alert alert-success'><h4>Save successful</h4><p>New $PAGE_name saved, id: $result</p></div>";

						// After save we have to reset all variabels so that we get a new clean form
						$PAGE_dbid = -1;

						foreach ($PAGE_form as $field) {
							$field["content"] = '';
						}

						// If you don't wanna show the message, you could just redirect back to this page instead of "cleaning" all the variables.
						//ob_clean();
						//header('Location: ' . $SYS_folder . '/campaign.php');

					} else {
						pushError("NOT saved");
					}
*/
				}
			}

		}


		// TODO: If we have a given id, fetch form data from database.
		if ( $PAGE_dbid > 0 )
		{
			// Pseudo: Run SQL, get result, loop through it and put each data in the correct "content" of all the arrays.
			// 		   Maybe time for that setting in the arrays with name of field in database? Hmm ... TODO =)
			/*
			$result = db2_getCampaign( array('id' => $PAGE_dbid) );

			if (!is_null($result))
			{
				$row = $result->fetch_object();

				$formTitle = $row->title;
				$formUrl = $row->url;
				$formStart = substr($row->start,0,10);
				$formStop = substr($row->stop,0,10);
				$formShortInfo = $row->shortinfo;
				$formVervStep1 = $row->verv_step1;
				$formVervStep2 = $row->verv_step2;
				$formVervTakk = $row->verv_takk;
				$formGiveStep1 = $row->give_step1;
				$formGiveTakk = $row->give_takk;
				$formImage = $row->image;

			} else {
				pushError("Couldn't find the requested $PAGE_name");
			}
			*/
		}

	?>

	<script language="javascript" type="text/javascript" src="<?= $SYS_root . $SYS_folder ?>/assets/tiny_mce/tiny_mce.js"></script>
	<script type="text/javascript">
		tinyMCE.init({
			// General options
			mode : "textareas",
			theme : "advanced",
			plugins : "spellchecker,iespell,inlinepopups,paste,nonbreaking",

			// Theme options
//			theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect",
//			theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
//			theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
			theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,justifyfull,|,undo,redo",
			theme_advanced_buttons2 : "outdent,indent,link,unlink,|,cut,copy,paste,pastetext,pasteword,|,cleanup",
			theme_advanced_buttons3 : "",
			theme_advanced_toolbar_location : "top",
			theme_advanced_toolbar_align : "left",
			theme_advanced_statusbar_location : "bottom",
			theme_advanced_resizing : true,
        	editor_selector : "mceEditor",
        	editor_deselector : "mceNoEditor",
			width: "100%",
			height: "300"
		});
	</script>

	<div class="page-header">
		<h1>
			<?= $PAGE_name ?>s
			<small>create and manage <?= $PAGE_name ?>s</small>
		</h1>
	</div>

<?php

	// Now that we are just before the form starts, we can output any errors we might have pushed into the error-array.
	// Calling this function outputs every error, earlier pushes to the error-array also stops the saving of the form.

	outputErrors($SYS_errors);

?>

<form class="form-horizontal" action="" method="post">

	<div class="row">
		<div class="span7">

	<?php

		// This is the output area, where all the field's html should be generated for empty field's SQL inserts, and already filled in field's SQL updates.
		// The fields data/content is generated in the upper parts of this document. Just call this function to get the html out.

		outputFormFields();

	?>

		</div>


		<div class="span4 offset1">

			<a class="btn btn-success" href="?"><i class="icon-plus-sign icon-white"></i> Add new <?= $PAGE_name ?></a>

			<hr />

			<h4>Select <?= $PAGE_name ?></h4>
			<?php
				$result = db2_getCampaignsActive();

				if (!is_null($result))
				{
					while ( $row = $result->fetch_object() )
					{
						echo "<a href='?id=" . $row->id . "'>" . $row->title . "</a><br />";
					}
				}
				else
				{
					echo "<p>No active $PAGE_name found</p>";
				}
			?>
			<hr />

			<h4>Help</h4>
			<p>
				<strong>Help info</strong> just some random gibberish about this admin page that could be useful for somebody.
				I sometimes use screenshots here to connect these back-end fields to the front-end (clients love this because
				after your first show-and-tell of this amdin with them they WILL forget it, mainly because most clients log in
				maybe 4 times a year, and thos times are right after getting the product).
			</p>

		</div>
	</div>


	<div class="form-actions">
		<button type="submit" class="btn btn-primary">Save</button>

		<?php if ($PAGE_dbid > 0 && 1 == 2) { ?>
		<a href="?del=<?= $PAGE_dbid ?>" class="btn btn-mini btn-danger">Delete</a>
		<?php } ?>
	</div>

</form>


<?php require('_footer.php'); ?>