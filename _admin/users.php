<?php
	/* Set up template variables */
	$pagetitle = 'Admin/Users';
	ob_start();
?>
<?php require('_header.php'); ?>


	<?php

		$user_id = -1;
		$formMail = '';
		$formPassword = '';
		$formLevel = '';

		if (isset($_GET['id']))
			$user_id = qsGet('id');

		
		if (isset($_GET['del']) && !ISPOST)
		{
			$del_id = trim( $_GET['del'] );

			$del = db_delUser( array(
						'id' => $del_id
					) );

			if ($del >= 0)
				echo '<div class="alert alert-success"><h4>Delete successful</h4><p>The User is now deleted</p></div>';
			else
				pushError('Delete of User failed, please try again.');
		}
		

		if (ISPOST)
		{
			$formMail = strtolower(formGet('mail'));
			$formPassword = formGet('password');
			$formLevel = formGet('level');

			if ($formMail != '') {
				if (!isValidLength($formMail,5,100)) {
					pushError('Teksten i "E-mail" er for lang. Legg inn tekst med maks 100 tegn.');
				}
			} else {
				pushError('Du har ikke lagt inn tekst i "E-mail".');
			}

			if ($formPassword != '') {
				if (!isValidLength($formPassword,3,45)) {
					pushError('Teksten i "Password" er for lang, eller kort. Legg inn tekst med minimum 3 tegn og maks 45 tegn.');
				}
			} else {
				pushError('Du har ikke lagt inn tekst i "Password".');
			}

			if ($formLevel === '0' || $formLevel === '1' || $formLevel === '2') {
				// Helt ok
			} else {
				pushError('Choose between the three different access levels.');
			}

			if (empty($_SESSION['ERRORS'])) {
				// UPDATE
				if ( $user_id > 0 )
				{
					$result = db_setUpdateUser( array(
								'mail' => $formMail,
								'password' => $formPassword,
								'level' => $formLevel,
								'id' => $user_id
							) );

					if ($result >= 0) {
						//echo "Sparat!";
						echo '<div class="alert alert-success"><h4>Save successful</h4><p>User updated</p></div>';
					} else {
						pushError("IKKE sparat");
					}

				// CREATE
				} else {

					$result = db_setUser( array(
								'mail' => $formMail,
								'password' => $formPassword,
								'level' => $formLevel
							) );

					if ($result > 0) {
						//echo "Nytt med id $result";
						echo '<div class="alert alert-success"><h4>Save successful</h4><p>New User saved, id: ' . $result . '</p></div>';

						$user_id = -1;
						$formMail = '';
						$formPassword = '';
						$formLevel = '';

					} else {
						pushError("IKKE sparat");
					}
				}
			}

		} else {

			if ( $user_id > 0 )
			{
				$result = db_getUser( array('id' => $user_id) );

				if (!is_null($result))
				{
					$row = $result->fetch_object();
					$formMail = $row->mail;
					$formPassword = $row->password;
					$formLevel = $row->level;
				} else {
					pushError("Couldn't find the requested User");
				}
			}
		}

	?>

	<div class="page-header">
		<h1>
			Users
			<small>Manage users</small>
		</h1>
	</div>

	<?php
		if (!empty($_SESSION['ERRORS']))
		{
			outputErrors($_SESSION['ERRORS']);
		}
	?>

	<div class="row">
		<div class="span7">

			<form class="form-horizontal" action="" method="post">
				
				<div class="control-group">
					<label class="control-label" for="inputTitle">E-mail</label>
					<div class="controls">
						<input type="text" name="mail" class="input-xlarge" id="inputTitle" value="<?= htmlspecialchars($formMail, ENT_QUOTES) ?>" maxlength="50" />
						<p class="help-block">The users e-mail address is used as a Username to sign in to the system</p>
					</div>
				</div>

				<div class="control-group">
					<label class="control-label" for="inputTitle">Password</label>
					<div class="controls">
						<input type="text" name="password" class="input-xlarge" id="inputTitle" value="<?= htmlspecialchars($formPassword, ENT_QUOTES) ?>" maxlength="50" />
						<p class="help-block">Create a unique and good password with 4 to 45 characters</p>
					</div>
				</div>

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
						<p class="help-block">Asign a access level to the user, you need to sign out and then in again to activate new access level on yourself</p>
					</div>
				</div>
				
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary">Save</button>

						<?php if ($user_id > 0) { ?>
						<a href="?del=<?= $user_id ?>" class="btn btn-mini btn-danger">Delete</a>
						<?php } ?>
					</div>
				</div>

			</form>

		</div>



		<div class="span4 offset1">

			<a class="btn btn-success" href="?"><i class="icon-plus-sign icon-white"></i> Add new User</a>

			<hr />

			<h4>User list</h4>
			<?php
				$result = db_getUsers();

				if (!is_null($result))
				{
					while ( $row = $result->fetch_object() )
					{
						echo "<a href='?id=" . $row->id . "'>" . $row->mail . "</a><br />";
					}
				}
				else
				{
					echo "<p>No Users found</p>";
				}
			?>

		</div>
	</div>


<?php require('_footer.php'); ?>