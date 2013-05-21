<?php
	/* Set up template variables */
	$PAGE_name = 'Login';
	$PAGE_title = 'Admin/' . $PAGE_name;
?>
<?php require('_global.php'); ?>
<?php require('_header.php'); ?>


	<?php
		
		if (ISPOST)
		{
			$formMail = formGet('cms_mail');
			$formPass = formGet('cms_pass');

			$result = db2_getUserLoginInfo( array('mail' => $formMail) );

			if (!is_null($result))
			{

				$row = $result->fetch_object();
				$dbPass = $row->password;
				//$dbPass = 'hej';
				
				if ($formPass === $dbPass )
				{
					$loggedin = true;
					
					$_SESSION['username'] = $row->username;
					$_SESSION['password'] = $row->password;
					$_SESSION['mail'] = $row->mail;
					$_SESSION['level'] = $row->level;
					$_SESSION['id'] = $row->id;

					ob_clean();
					header('Location: ' . $SYS_root . $SYS_folder . '/');

				} else {

					$loggedin = false;

				}
			}
			else
			{
				$loggedin = false;
			}

			if (!$loggedin) {
				pushError("Login failed. Wrong Email-address or wrong password maybe? Remember that the password-field is case-sensitive!");
			}
		}
		else if ( isset($_GET['do']))
		{
			if ($_GET['do'] === 'logout') {

				unset( $_SESSION['username'] );
				unset( $_SESSION['password'] );
				unset( $_SESSION['mail'] );
				unset( $_SESSION['level'] );
				unset( $_SESSION['id'] );

				ob_clean();
				header('Location: ' . $SYS_root . $SYS_folder . '/');
			}
		}
	?>

	<div class="page-header">
		<h1>
			"Bobby CMS"
			<small>Sign in to your account</small>
		</h1>
	</div>

	<?php
		outputErrors($SYS_errors);
	?>

	<div class="row">
		<div class="span12">

			<?php
				if (!isset($_SESSION['id'])) {
			?>
			
			<form class="well form-inline" action="" method="post">
				
				<input type="text" name="cms_mail" class="input-large" placeholder="E-mail" autocomplete="false" />
				<input type="password" name="cms_pass" class="input-medium" placeholder="Password" autocomplete="false" />

				<button type="submit" class="btn">Sign in</button>

			</form>

			<?php
				} else {
			?>

			<p>Signed in: <?= $_SESSION['mail'] ?> - <a href="?do=logout">Sign out</a></p>

			<?php
				}
			?>

		</div>
	</div>


<?php require('_footer.php'); ?>