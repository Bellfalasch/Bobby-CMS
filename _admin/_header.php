<!DOCTYPE html>
<?php

	// Push some debugging data to the footer:
	pushDebug("
			SYS_folder: $SYS_folder -
			SYS_root: $SYS_root -
			SYS_incroot: $SYS_incroot -
			SYS_script: $SYS_script
			");

	if (isset($_SESSION['username'])) {
		
		pushDebug("
				[SESSION]
				username: " . $_SESSION['username'] . "
				mail: " . $_SESSION['mail'] . "
				level: " . $_SESSION['level'] . "
				id: " . $_SESSION['id'] . "
				sessionID: " . session_id() . "
				");
	}

?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf8" />
	<title><?= $PAGE_title ?> - [SITENAME]</title>
	<link rel="shortcut icon" href="<?= $SYS_root ?>/favicon.ico">
	<link rel="stylesheet" href="<?= $SYS_root . $SYS_folder ?>/assets/bootstrap.min.css" />
	<link rel="stylesheet" href="<?= $SYS_root . $SYS_folder ?>/assets/admin.css?v=<?php if (DEV_ENV) echo rand(); ?>" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
</head>
<body>

	<div class="navbar navbar-fixed-top">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".nav-collapse">
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
					<span class="icon-bar"></span>
				</a>
				
				<a class="brand" href="http://www.nxt.no/">nxt cms</a>
				
				<div class="nav-collapse">

					<ul class="nav">
						<li<?php flagAsActiveOn("index") ?>><a href="<?= $SYS_root . $SYS_folder ?>/index.php">Start</a></li>
						<?php if ($SYS_adminlvl > 0) { ?>
							<li<?php flagAsActiveOn("examples") ?>><a href="<?= $SYS_root . $SYS_folder ?>/examples.php">Examples</a></li>
							<?php if ($SYS_adminlvl == 2) { ?>
							<li<?php flagAsActiveOn("users") ?>><a href="<?= $SYS_root . $SYS_folder ?>/users.php">Users</a></li>
							<?php } ?>
						<?php } ?>
					</ul>

				</div>
			</div>
		</div>
	</div>
	
	<div class="subnav subnav-fixed">
		<ul class="nav nav-pills">
			<?php if (isActiveOn("index")) { ?>

				<li<?php flagAsActiveOn("index") ?>><a href="<?= $SYS_root . $SYS_folder ?>/index.php">Login</a></li>
				<?php if ($SYS_adminlvl > 0) { ?>
				<li><a href="<?= $SYS_root . $SYS_folder ?>/index.php?do=logout">Sign out</a></li>
				<?php } ?>


			<?php } else if (isActiveOn("users")) { ?>

				<li<?php flagAsActiveOn("users") ?>><a href="<?= $SYS_root . $SYS_folder ?>/users.php">Users</a></li>


			<?php } else if (isActiveOn("examples")) { ?>

				<?php if ($SYS_adminlvl > 0) { ?>
					<li<?php flagAsActiveOn("example1") ?>><a href="<?= $SYS_root . $SYS_folder ?>/examples-example1.php">Example1</a></li>
					<li<?php flagAsActiveOn("image") ?>><a href="<?= $SYS_root . $SYS_folder ?>/examples-image.php">Image</a></li>
					<li<?php flagAsActiveOn("upload") ?>><a href="<?= $SYS_root . $SYS_folder ?>/examples-upload.php">Upload</a></li>
				<?php } ?>

			<?php } ?>
		</ul>
	</div>

	<div id="container">
<!-- /header -->