<!DOCTYPE html>

<?
	// Dynamic links etc based on where we have the code-files
	// Needs to be set here so that require-url matcher.
	if ($_SERVER['SERVER_NAME'] == 'localhost') {
		$SYS_folder = '/nxtcms';
	} else {
		$SYS_folder = '';
	}

	// Dynamic links etc based on where we have the code-files
	if ($_SERVER['SERVER_NAME'] == 'localhost') {
		$SYS_url = "localhost";
	} else {
		$SYS_url = $_SERVER['SERVER_NAME'];
	}

	$SYS_incroot = rtrim($_SERVER['DOCUMENT_ROOT'],"/") . $SYS_folder;

	//$SYS_file = basename($_SERVER['REQUEST_URI'], ".php");
	$currentFile = $_SERVER["SCRIPT_NAME"];
	$parts = explode('/', $currentFile);
	$currentFile = $parts[count($parts) - 1];
	$SYS_script = str_replace('.php','',$currentFile);

	//////////////////////////////////////////////////////////////////////////////////
	// Get the current folder the files are in, account for different servers returning the FILE-var differently.

	$mappar = __FILE__;
	if ( strpos($mappar,'\\') > 0 ) {
		$mapparArr = explode('\\', $mappar); // localhost
	} else {
		$mapparArr = explode('/', $mappar); // dedicated server
	}
	$mapp = $mapparArr[count($mapparArr) - 3];

?>

<?php require( $SYS_incroot . '/inc/database.php'); ?>
<?php require( $SYS_incroot . '/inc/functions.php'); ?>
<?php require('_database.php'); ?>

<?php

	// Set isPost
	if ($_SERVER['REQUEST_METHOD'] === 'POST')
		DEFINE('ISPOST', true);
	else
		DEFINE('ISPOST', false);

	// Define environment; development on or off.
	DEFINE('DEV_ENV', true);

	if (DEV_ENV) {
		error_reporting(E_ALL);
		ini_set('display_errors', '1');
	} else {
		ini_set('session.gc_maxlifetime', '10800');
	}

	session_cache_expire('30'); // default 180 minutes
	date_default_timezone_set('Europe/Oslo');
	setlocale(LC_TIME, 'no_NO.ISO_8859-1', 'norwegian', 'nb_NO.utf8', 'no_NO.utf8');

	header('Content-type: text/html; charset=utf-8');
	header('X-UA-Compatible: IE=edge,chrome=1');

	//////////////////////////////////////////////////////////////////////////////////

	ob_start();
	session_start();
	//ob_clean();

	$_SESSION['ERRORS'] = array(); // Reset the error-session on each page load =)
	$_SESSION['debug'] = array();

	//////////////////////////////////////////////////////////////////////////////////

	// Push important debugging data to the footer:
	pushDebug("
			folder: $SYS_folder -
			script: $SYS_script -
			sessionID: " . session_id() . "
			");

	if (isset($_SESSION['username'])) {
		
		pushDebug("
				[SESSION]
				username: " . $_SESSION['username'] . "
				mail: " . $_SESSION['mail'] . "
				level: " . $_SESSION['level'] . "
				id: " . $_SESSION['id']
				);
	}

	// Get system admin level into a variable.
	if (isset($_SESSION['level'])) {
		$SYS_adminlvl = $_SESSION['level'];
	} else {
		$SYS_adminlvl = 0;
		if ($SYS_script != "login" && $SYS_script != "index" )
		{
			ob_clean();
			header('Location: ' . $SYS_folder . '/_admin/login.php');
		}
	}


	//////////////////////////////////////////////////////////////////////////////////
	// My magic forms:
	//////////////////////////////////////////////////////////////////////////////////

	function generateField($field) {
		
		//var_dump($field);

		if (isset($field["errors"]))
			$errors = $field["errors"];
		else
			$errors = array();

		// Check if this is a demanded field and generate "Required field"-mark.
		$demanded = "";
		if (isset($errors["min"]) && isset($field["min"]))
			$demanded = " <strong>*</strong>";

		// If it's a text-field (support for other fields will be added later) we can add the maxlentgh attribute, if asked for.
		$maxlength = "";
		$areaType = $field["type"];
		
		if (mb_substr($areaType,0,4) == "text") {
			if (isset($field["max"]))
				$maxlength = " maxlength=\"" . $field["max"] . "\"";
			elseif (isset($field["min"]) && isset($errors["exact"]))
				$maxlength = " maxlength=\"" . $field["min"] . "\"";
		}

		$description = "";
		if (isset($field["description"])) {
			$description = $field["description"];
			if ( isset($field["min"]) )
				$description = str_replace("[MIN]",$field["min"],$description);
			
			if ( isset($field["max"]) )
				$description = str_replace("[MAX]",$field["max"],$description);
			
			$description = str_replace("[LABEL]", str_replace(":","",$field["label"]), $description);

			$description = "<p class=\"help-block\">" . $description . "</p>";
		}

		$thisId = "";
		if (isset($field["id"]))
			$thisId = $field["id"];
		else {
			$thisId = $field["label"];
			$thisId = str_replace(' ','',$thisId);
			$thisId = str_replace(':','',$thisId);
		}

		$thisName = strtolower($thisId);
		$thisId = "input" . $thisId;


		$strField = "
				<div class=\"control-group\">
					<label class=\"control-label\" for=\"input" . $thisId . "\">" . $field["label"] . "$demanded</label>
					<div class=\"controls\">
						";

		$thisContent = "";
		if (isset($field["content"]))
			$thisContent = htmlspecialchars($field["content"], ENT_QUOTES);


		// Supporting types to set their sizes via the format "type(WIDTH*HEIGHT)" or "type(WIDTH)"
		// (if only "type" is found, default sizes will be used).
		$areaSizeRows = 0;
		$areaSizeCols = 5;

		if (strpos($areaType,"(") != false) {
			if (strpos($areaType,"*") != false) {
				$areaSize = explode('*',$areaType);
				$tmp = explode('(',$areaSize[0]);
				$areaSizeCols = $tmp[1];
				$tmp = explode(')',$areaSize[1]);
				$areaSizeRows = $tmp[0];
			} else {
				$areaSize = explode('(',$areaType);
				$tmp = explode(')',$areaSize[1]);
				$areaSizeCols = $tmp[0];
			}
		}

		// For type=folder, and many other, we use the settings-value to CSS-style set a lot of things.
		if (isset($field["settings"]) && (mb_substr($areaType,0,6) == "folder")) {
			$thisSettings = explode(";",$field["settings"]);
			$thisSetDir = "";
			$thisSetUnselectable = "";
			$thisSetFormats = "";

			foreach($thisSettings as $settings)
			{
				if (trim($settings) != "") {
				
//					var_dump($settings);

					$settingsPair = explode(":",trim($settings));

//					var_dump($settingsPair);

					switch( strtolower(trim($settingsPair[0])) )
					{
						case "formats":
							$thisSetFormats = trim($settingsPair[1]);
							break;

						case "unselectable":
							$thisSetUnselectable = trim($settingsPair[1]);
							break;

						case "folder":
							$thisSetDir = trim($settingsPair[1]);
							break;
					}
				}
			}

			// Don't allow processing of the folder-type if no folder is set.
			if ($thisSetDir == "")
				$areaType = "text";
		}
/*
		<select id="inputImage" name="image" class="span3">
		<?php
			// * Hæmta alla filer i mappen
			$dir = "../images/campaigns/";
			$files = scandir($dir);
			$strSelected = "";
			$somethingChecked = false;

			foreach($files as $key => $value)
			{
				if ($value != '.' && $value != '..')
				{
					if ( $formImage === $value ) {
						$strSelected = ' selected="selected"';
						$somethingChecked = true;
					} else
						$strSelected = '';
						
					echo '<option value="' . $value . '"' . $strSelected . '>' . $value . '</option>';
				}
			}
		?>
		<?php
			if ($somethingChecked) {
				$strSelected = '';
			} else {
				$strSelected = ' selected="selected"';
			}
			echo "<option disabled='disabled'></option>";
			echo '<option value=""' . $strSelected .'>- Ikke bruk bilde -</option>';
		?>
		</select>
*/

		// Generate the actual form field based on the "type" setting. Currently only text and area(rows*columns) supported.
		switch ( mb_substr($areaType,0,4) ) {
			case "text":
				$strField .= "<input type=\"text\" name=\"" . $thisName . "\" class=\"span" . $areaSizeCols . "\" id=\"". $thisId . "\" value=\"" . $thisContent . "\"" . $maxlength . " />";
				break;

			case "area":
				$strField .= "<textarea rows=\"" . $areaSizeRows . "\" name=\"" . $thisName . "\" class=\"mceNoEditor span" . $areaSizeCols . "\" id=\"" . $thisId . "\">" . $thisContent . "</textarea>";
				break;

			case "wysi":
				$strField .= "<textarea rows=\"" . $areaSizeRows . "\" name=\"" . $thisName . "\" class=\"mceEditor span" . $areaSizeCols . "\" id=\"" . $thisId . "\">" . $thisContent . "</textarea>";
				break;

			case "fold":
				$strField .= "<select name=\"" . $thisName . "\" class=\"span" . $areaSizeCols . "\" id=\"" . $thisId . "\">";
			
				$files = scandir($thisSetDir);
				$strSelected = "";
				$somethingChecked = false;

				foreach($files as $key => $value)
				{
					$write = false;

					if (!is_dir($thisSetDir . $value)) // Exclude all subfolders
					{
						if ($thisSetFormats != '') // Check for file-endings ONLY if settings are active
						{
							if (strpos($value,".") > 0) // Files without file endings shouldn't be written out
							{
								$fileEnding = explode(".",$value);

								//var_dump($thisSetFormats);
								//var_dump($fileEnding);

								if (strpos($thisSetFormats,$fileEnding[1]) > -1 ) {
									$write = true;
//								} else {
//									$write = false;
								}
							}
						} else {
							$write = true; // If the formatsetting isn't set, well then we can output anything (except folders).
						}
					}

					if ($write) {
						if ( $thisContent === $value ) {
							$strSelected = ' selected="selected"';
							$somethingChecked = true;
						} else
							$strSelected = '';
							
						$strField .= '<option value="' . $value . '"' . $strSelected . '>' . $value . '</option>';
					}
				}

				if ($thisSetUnselectable != '') {
					if ($somethingChecked) {
						$strSelected = '';
					} else {
						$strSelected = ' selected="selected"';
					}
					$strField .= "<option disabled='disabled'></option>";
					$strField .= '<option value=""' . $strSelected .'>- ' . $thisSetUnselectable . ' -</option>';
				}
/*
			// * Hæmta alla filer i mappen
			$dir = "../images/campaigns/";
			$files = scandir($dir);
			$strSelected = "";
			$somethingChecked = false;

			foreach($files as $key => $value)
			{
				if ($value != '.' && $value != '..')
				{
					if ( $formImage === $value ) {
						$strSelected = ' selected="selected"';
						$somethingChecked = true;
					} else
						$strSelected = '';
						
					echo '<option value="' . $value . '"' . $strSelected . '>' . $value . '</option>';
				}
			}

			if ($somethingChecked) {
				$strSelected = '';
			} else {
				$strSelected = ' selected="selected"';
			}
			echo "<option disabled='disabled'></option>";
			echo '<option value=""' . $strSelected .'>- Ikke bruk bilde -</option>';
*/
//					" . $thisContent . "

				$strField .= "</select>";
				break;
		}

		$strField .= "
						" . $description . "
					</div>
				</div>";

		echo $strField;
	}

	//////////////////////////////////////////////////////////////////////////////////

	// Start the validation loop
	// ERROR: Troligen skrivs sista datan øver hær inne, før den ær korrekt innan generateField men verkar inte ændras i den funktionen.
	if (ISPOST && isset($PAGE_form)) {
/*
		// ERROR: Denna visar KORREKT data, så det ær inne i næsta loop, garanterat pga byreference och content-sættningen =/
		foreach ($PAGE_form as &$field) {
			var_dump($field);
		}
*/
		foreach ($PAGE_form as &$field) {

			$thisId = "";
			if (isset($field["id"]))
				$thisId = $field["id"];
			else {
				$thisId = $field["label"];
				$thisId = str_replace(' ','',$thisId);
				$thisId = str_replace(':','',$thisId);
			}

			$temp["content"] = formGet(strtolower($thisId));
			$field["content"] = $temp["content"];
			//echo "<p>" . $field["content"] . "</p>";
			
			// If set to use null, set null instead of empty string on this field.
			if (isset($field["null"]))
				if ($field["null"] && $field["content"] == '')
					$field["content"] = null;
			
			if (isset($field["errors"]))
				$errors = $field["errors"];
			else
				$errors = array();

			$content = $field["content"];

			// Support for [MIN], [LABEL], etc inside the error messages.
			foreach ($errors as &$error)
			{
				if (isset($field["min"]))
					$error = str_replace("[MIN]",$field["min"],$error);
				if (isset($field["max"]))
					$error = str_replace("[MAX]",$field["max"],$error);

				$error = str_replace("[LABEL]", str_replace(":","",$field["label"]), $error);
				$error = str_replace("[CONTENT]",$field["content"],$error);
			}
			

			// Check for "empty"-validation and if present push the empty-error.
			if ((isset($errors["min"]) && isset($field["min"]) )) {
				if (mb_strlen($content) < 1) {
					/*
					if (isset($errors["empty"])) {
						if ($errors["empty"] != '')
							pushError("<strong>" . $field["label"] . "</strong> " . $errors["empty"]);
					} elseif (isset($errors["min"]) && isset($field["min"]) ) {
					*/
						if ($errors["min"] != '' && $field["min"] == 1)
							pushError("<strong>" . $field["label"] . "</strong> " . $errors["min"]);
					//}
				} elseif (mb_strlen($content) > 0 && mb_strlen($content) < $field["min"]) {
					//if (mb_strlen($content) < $field["min"]) {
						pushError("<strong>" . $field["label"] . "</strong> " . $errors["min"]);
					//}
				}
			}
			/*
			if (isset($errors["min"]) && isset($field["min"])) {
				if (mb_strlen($content) < $field["min"]) {
					pushError("<strong>" . $field["label"] . "</strong> " . $errors["min"]);
				}
			}
			*/
			if (isset($errors["max"]) && isset($field["max"])) {
				if (mb_strlen($content) > $field["max"]) {
					pushError("<strong>" . $field["label"] . "</strong> " . $errors["max"]);
				}
			}
			if (isset($errors["mail"]) && mb_strlen($content) > 0) {
				if ( !isValidEmail($content) ) {
					pushError("<strong>" . $field["label"] . "</strong> " . $errors["mail"]);
				}
			}
			if (isset($errors["numeric"]) && mb_strlen($content) > 0) {
				if (!preg_match('/^(?:\d+(?:,|$))+$/', $content)) {
					pushError("<strong>" . $field["label"] . "</strong> " . $errors["numeric"]);
				}
			}
			if (isset($errors["exact"]) && isset($field["min"]) && mb_strlen($content) > 0) {
				if (mb_strlen($content) != $field["min"]) {
					pushError("<strong>" . $field["label"] . "</strong> " . $errors["exact"]);
				}
			}

		}

/*
		// ERROR: Hær ær den OCKSÅ korrekt ... kanske blir fel i generateFields ændå =/
		foreach ($PAGE_form as &$field) {
			var_dump($field);
		}
*/

	}

	//////////////////////////////////////////////////////////////////////////////////
?>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf8" />
	<title><?= $PAGE_title ?> - [SITENAME]</title>
	<link rel="shortcut icon" href="<?= $SYS_folder ?>/favicon.ico">
	<link rel="stylesheet" href="<?= $SYS_folder ?>/_admin/assets/bootstrap.min.css" />
	<link rel="stylesheet" href="<?= $SYS_folder ?>/_admin/assets/admin.css?v=<?php if (DEV_ENV) echo rand(); ?>" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.8.1/jquery.min.js"></script>
</head>
<body>

	<?php

		//////////////////////////////////////////////////////////////////////////////////
		// NAVIGATION MENU

		// Return true if we are on a page connected to said menu, or a subpage.
		// Only works when the naming convention "parent.php" + "parent-child.php" is used.
		function isActiveOn($pages) {
			global $SYS_script;
			$arrPages = explode(",",$pages);

			if (in_array($SYS_script,$arrPages))
				return true;
			else {
 				
 				if (strpos($SYS_script,"-") > 0)
 				{
 					$arrThisPart = explode("-",$SYS_script);

 					if ( in_array($arrThisPart[0],$arrPages) OR in_array($arrThisPart[1],$arrPages) )
	 					return true;

 				}

			}

		}

		// If above function says true we will print the active-class on that li.
		function flagAsActiveOn($pages) {
			if ( isActiveOn($pages) )
				echo ' class="active"';
		}

		//////////////////////////////////////////////////////////////////////////////////
	?>

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
						<li<?php flagAsActiveOn("index") ?>><a href="<?= $SYS_folder ?>/_admin/index.php">Start</a></li>
						<?php if ($SYS_adminlvl > 0) { ?>
							<li<?php flagAsActiveOn("examples") ?>><a href="<?= $SYS_folder ?>/_admin/examples.php">Examples</a></li>
							<?php if ($SYS_adminlvl == 2) { ?>
							<li<?php flagAsActiveOn("users") ?>><a href="<?= $SYS_folder ?>/_admin/users.php">Users</a></li>
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

				<li<?php flagAsActiveOn("index") ?>><a href="<?= $SYS_folder ?>/_admin/index.php">Login</a></li>
				<?php if ($SYS_adminlvl > 0) { ?>
				<li><a href="<?= $SYS_folder ?>/_admin/index.php?do=logout">Sign out</a></li>
				<?php } ?>


			<?php } else if (isActiveOn("users")) { ?>

				<li<?php flagAsActiveOn("users") ?>><a href="<?= $SYS_folder ?>/_admin/users.php">Users</a></li>


			<?php } else if (isActiveOn("examples")) { ?>

				<?php if ($SYS_adminlvl > 0) { ?>
					<li<?php flagAsActiveOn("example1") ?>><a href="<?= $SYS_folder ?>/_admin/examples-example1.php">Example1</a></li>
					<li<?php flagAsActiveOn("image") ?>><a href="<?= $SYS_folder ?>/_admin/examples-image.php">Image</a></li>
					<li<?php flagAsActiveOn("upload") ?>><a href="<?= $SYS_folder ?>/_admin/examples-upload.php">Upload</a></li>
				<?php } ?>

			<?php } ?>
		</ul>
	</div>

	<div id="container">
<!-- /header -->