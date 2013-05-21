<?php
	/* Set up template variables */
	$PAGE_name  = 'Users';
	$PAGE_title = 'Admin/' . $PAGE_name;
?>
<?php require('_global.php'); ?>

<?php

	// See README.md in root for more information about how to set up and use the form-generator!

	addField( array(
		"label" => "Name:",
		"type" => "text(4)",
		"max" => "45",
		"null" => true,
		"errors" => array(
						"max" => "Please keep number of character's to [MAX] at most."
					)
	) );
	
	addField( array(
		"label" => "Email:",
		"type" => "text(5)",
		"min" => "1",
		"max" => "255",
		"errors" => array(
						"min" => "Please submit your e-mail address.",
						"max" => "Please keep number of character's to [MAX] at most.",
						"mail" => "Please use a valid e-mail, [CONTENT] is not valid."
					)
	) );
	
	addField( array(
		"label" => "Username:",
		"type" => "text(3)",
		"min" => "3",
		"max" => "45",
		"errors" => array(
						"min" => "Please set a username.",
						"max" => "Please keep number of characters to [MAX] at most."
					)
	) );
	
	addField( array(
		"label" => "Password:",
		"type" => "text(3)",
		"min" => "5",
		"max" => "45",
		"errors" => array(
						"min" => "Please set a password with at least [MIN] characters.",
						"max" => "Please keep number of character's to [MAX] at most."
					)
	) );
/*
	// EDITION: Instead of adding a field with addField to the PAGE_form, we can do it manually. Just follow the "EDITION"-steps in this file!

	addField( array(
		"label" => "Level:",
		"type" => "text(1)",
		"min" => "1",
		"max" => "1",
		"errors" => array(
						"min" => "We need the admin level!",
						"exact" => "Not valid format - Please submit exactly one character in this field.",
						"numeric" => "This field needs to contain only numbers (no letters, no special characters, no spaces, etc)!"
					)
	) );
*/
?>
<?php require('_header.php'); ?>


	<?php

		////////////////////////////////////////////////////////
		// DELETE DATA-SUPPORT

		// Deletion of content (comment out if not to be allowed)
		if (isset($_GET['del']) && !ISPOST)
		{
			$del_id = trim( $_GET['del'] );

			$del = db2_delDiscount( array(
						'id' => $del_id
					) );

			if ($del >= 0)
				echo "<div class='alert alert-success'><h4>Delete successful</h4><p>The data is now deleted</p></div>";
			else
				pushError("Delete of data failed, please try again.");
		}
		

		////////////////////////////////////////////////////////
		// HANDLE POST AND SAVE CHANGES

		// EDITION: Custom field (variable needs to be set up before ISPOST, and because of formGet we can make that work easier)
		$formLevel    = formGet("level");

		// User has posted (trying to save changes)
		if (ISPOST)
		{
			// This line is needed to call the validation-process of your form!
			validateForm();

			var_dump($PAGE_form); // For debugging

			// Stupid way of getting all the form data into variables for use to save the data.
			$formName     = $PAGE_form[0]["content"];
			$formMail     = $PAGE_form[1]["content"];
			$formUsername = $PAGE_form[2]["content"];
			$formPassword = $PAGE_form[3]["content"];
			
			// EDITION: Custom validation on custom field:
			if (! in_array($formLevel,array('0','1','2','3')) ) {
				// User pushError, but insert custom html:
				pushError('<strong>Admin level</strong>: Choose between the different access levels presented.');
			}

			// If no errors:
			if (empty($SYS_errors)) {
				
				echo "<div class='alert alert-block alert-success'><h4>Success</h4><p><strong>Your posted data validated!</strong> (we have not set this up yet to save to your database =/)</p></div>";

				// UPDATE
				if ( $PAGE_dbid > 0 )
				{
					// Call function in "_database.php" that does the db-handling, send in an array with data
					$result = db2_setUpdateUser( array(
								'name' => $formName,
								'mail' => $formMail,
								'username' => $formUsername,
								'password' => $formPassword,
								'level' => $formLevel,
								'id' => $PAGE_dbid
							) );

					// This is the result from the db-handling in my files.
					// (On update they return -1 on error, and 0 on "no new text added, but the SQL worked", and > 0 for the updated posts id.)
					if ($result >= 0) {
						echo '<div class="alert alert-success"><h4>Save successful</h4><p>Data updated</p></div>';
					} else {
						pushError("Data could not be saved, do retry.");
					}

				// INSERT
				} else {

					// Call insert-function from our database-file for admin.
					$result = db2_setUser( array(
								'name' => $formName,
								'mail' => $formMail,
								'username' => $formUsername,
								'password' => $formPassword,
								'level' => $formLevel
							) );

					// If the insert worked we will now have the created id in this variable, otherwhise we will have 0 or -1.
					if ($result > 0) {
						
						echo '<div class="alert alert-success"><h4>Save successful</h4><p>New data saved, id: ' . $result . '</p></div>';

						// Reset all the data so we get a clean form after an insert.
						$PAGE_dbid = -1;

						// Stupid way of reseting the PAGE_form
						$PAGE_form[0]["content"] = '';
						$PAGE_form[1]["content"] = '';
						$PAGE_form[2]["content"] = '';
						$PAGE_form[3]["content"] = '';
						
						// EDITION: Reset custom field
						$formLevel = '';

					} else {
						pushError("Data could not be saved, do retry.");
					}

				}
			}

		}


		////////////////////////////////////////////////////////
		// HANDLE FILLING THE FORM WITH DATA FROM THE DATABASE

		if ( $PAGE_dbid > 0 && !ISPOST )
		{
			// Pseudo: Run SQL, get result, loop through it and put each data in the correct "content" of all the arrays.
			// 		   Maybe time for that setting in the arrays with name of field in database? Hmm ... TODO =)
			
			
			// Call _database.php function for getting any selected data.
			$result = db2_getUser( array('id' => $PAGE_dbid) );

			// If anything was found, put it into pur PAGE_form
			if (!is_null($result))
			{
				$row = $result->fetch_object();

				// Stupid way of doing it ... no function yet to bind database table to the form, sorry =P
				$PAGE_form[0]["content"] = $row->name;
				$PAGE_form[1]["content"] = $row->mail;
				$PAGE_form[2]["content"] = $row->username;
				$PAGE_form[3]["content"] = $row->password;
				
				// EDITION: Setting data from database to custom field
				$formLevel = $row->level;

			} else {
				pushError("Couldn't find the requested data");
			}
			
		}

	?>

	<div class="page-header">
		<h1>
			<?= $PAGE_name ?>
			<small>create and manage <?= $PAGE_name ?></small>
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

				<!-- EDITION: Adding a fully custom field to the form -->
				<div class="control-group">
					<label class="control-label">Admin level</label>
					<div class="controls">
						<label class="radio">
							<input type="radio" name="level" id="inputLevel0" value="0"<?php if ($formLevel == 0) echo 'checked="checked"' ?> />
							No access (user can no longer sign in)
						</label>
						<label class="radio">
							<input type="radio" name="level" id="inputLevel1" value="1"<?php if ($formLevel == 1) echo 'checked="checked"' ?> />
							Basic access
						</label>
						<label class="radio">
							<input type="radio" name="level" id="inputLevel2" value="2"<?php if ($formLevel == 2) echo 'checked="checked"' ?> />
							Full access
						</label>
						<label class="radio">
							<input type="radio" name="level" id="inputLevel3" value="3"<?php if ($formLevel == 3) echo 'checked="checked"' ?> />
							Super admin
						</label>
						<p class="help-block">Assign a access level to the user, you need to sign out and then in again to activate new access level on yourself</p>
					</div>
				</div>


			</div>


			<div class="span4 offset1">

				<a class="btn btn-success" href="?"><i class="icon-plus-sign icon-white"></i> Add new User</a>

				<hr />

				<h4>User list</h4>
				<?php
					$result = db2_getUsers();

					if (!is_null($result))
					{
						while ( $row = $result->fetch_object() )
						{
							echo "<a href='?id=" . $row->id . "'>" . $row->username . "</a><br />";
						}
					}
					else
					{
						echo "<p>No Users found</p>";
					}
				?>

			</div>
		</div>

		<div class="form-actions">
			<button type="submit" class="btn btn-primary">Save</button>

			<?php if ($PAGE_dbid > 0) { ?>
			<a href="?del=<?= $PAGE_dbid ?>" class="btn btn-mini btn-danger">Delete this</a>
			<?php } ?>
		</div>

	</form>


<?php require('_footer.php'); ?>