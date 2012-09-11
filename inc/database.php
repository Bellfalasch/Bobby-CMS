<?php
// Database setup (MySQL)
// ****************************************************************************	
	
	// Set constants for db-access after environment
	if ($_SERVER['SERVER_NAME'] == 'localhost')
	{	// LOCAL
		DEFINE('DB_USER', 'root');				// Username for database
		DEFINE('DB_PASS', '');					// Password for database
		DEFINE('DB_HOST', 'localhost');			// Server for database
		DEFINE('DB_NAME', 'test');				// Select database on server
	} else {
		// LIVE (change to your settings)
		DEFINE('DB_USER', 'xxx');
		DEFINE('DB_PASS', 'xxx');
		DEFINE('DB_HOST', 'xxx');
		DEFINE('DB_NAME', 'xxx');
	}
	
	// Set up database class
	global $mysqli;
	$mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME );
	if (mysqli_connect_errno()) { die("<p>" . mysqli_connect_errno() . " - Can't connect to this database or server =/</p>"); }
	$mysqli->set_charset('utf8');




// Prepared SQL-functions extracting data from db.
// ****************************************************************************

	// Find via string (url) the campaign
	function db_campaignFind($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `start`, `stop`
			FROM `mb_campaigns`
			WHERE
				url LIKE {$in['url']}
		");
	}

	// Get first page info about campaign ("verv", step 1 - your sign up).
	function db_campaignInfoStep1($in) { cleanup($in);
		return db_MAIN("
			SELECT `image`, `shortinfo`, `verv_step1`, `verv_step2`
			FROM `mb_campaigns`
			WHERE
				id = {$in['id']}
		");
	}

	// --
	function db_campaignInfoStep2($in) { cleanup($in);
		return db_MAIN("
			SELECT `image`, `shortinfo`, `verv_step2`, `give_step1`
			FROM `mb_campaigns`
			WHERE
				id = {$in['id']}
		");
	}

	// --
	function db_campaignInfoStep3($in) { cleanup($in);
		return db_MAIN("
			SELECT `image`, `shortinfo`, `verv_takk`
			FROM `mb_campaigns`
			WHERE
				id = {$in['id']}
		");
	}

	// --
	function db_campaignInfoStep4($in) { cleanup($in);
		return db_MAIN("
			SELECT `image`, `shortinfo`, `give_step1`, `address_lookup`
			FROM `mb_campaigns`
			WHERE
				id = {$in['id']}
		");
	}

	// --
	function db_campaignInfoStep5($in) { cleanup($in);
		return db_MAIN("
			SELECT `image`, `shortinfo`, `give_takk`
			FROM `mb_campaigns`
			WHERE
				id = {$in['id']}
		");
	}

	// Via the e-mail try and see if this user already exists, in that case return the name
	function db_userFind($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `firstname`, `lastname`, `mail`, `hash`
			FROM `mb_recruiter`
			WHERE
				mail LIKE {$in['mail']} AND
				campaigns_id = {$in['campaign']}
		");
	}

	// Create "user" (one who will participate in the contest by recommending friends)
	function db_userCreate($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `mb_recruiter`
				(`sessionid`, `firstname`, `lastname`, `mail`, `campaigns_id`, `orders_id`)
			VALUES(
				{$in['sessionid']},
				{$in['firstname']},
				{$in['lastname']},
				{$in['mail']},
				{$in['campaign']},
				NULL
			)
		");
	}

	// 
	function db_userCreateWithOrder($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `mb_recruiter`
				(`sessionid`, `firstname`, `lastname`, `mail`, `campaigns_id`, `orders_id`)
			VALUES(
				{$in['sessionid']},
				{$in['firstname']},
				{$in['lastname']},
				{$in['mail']},
				{$in['campaign']},
				{$in['order']}
			)
		");
	}

	// --
	function db_friendCreate($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `mb_friends`
				(`firstname`, `lastname`, `mail`, `recruiter_id`, `ip`)
			VALUES(
				{$in['firstname']},
				{$in['lastname']},
				{$in['mail']},
				{$in['recruiter']},
				{$in['ip']}
			)
		");
	}

	// --
	function db_friendGetAll($in) { cleanup($in);
		return db_MAIN("
			SELECT `firstname`, `lastname`, `mail`
			FROM `mb_friends`
			WHERE `recruiter_id` = {$in['recruiter']}
			ORDER BY id ASC
		");
	}

	// --
	function db_friendFindMail($in) { cleanup($in);
		return db_MAIN("
			SELECT `mail`
			FROM `mb_friends`
			WHERE
				`recruiter_id` = {$in['recruiter']} AND 
				`mail` = {$in['mail']}
		");
	}

	// --
	function db_recruiterFind($in) { cleanup($in);
		return db_MAIN("
			SELECT r.`id`, r.`firstname`, r.`lastname`, r.`mail`, f.firstname AS vervare
			FROM `mb_recruiter` r
			LEFT OUTER JOIN `mb_friends` f
			ON f.recruiter_id = r.id
			WHERE r.`mail` = {$in['mail']}
			ORDER BY r.`id` DESC
			LIMIT 1
		");
	}

	// --
	function db_recruitFind($in) { cleanup($in);
		return db_MAIN("
			SELECT f.`id` AS fid, r.id AS rid, f.`firstname`, f.`lastname`, f.`mail`, r.firstname AS vervare, r.campaigns_id, c.url
			FROM `mb_friends` f
			LEFT OUTER JOIN `mb_recruiter` r
			ON f.recruiter_id = r.id
			LEFT OUTER JOIN mb_campaigns c
			ON c.id = r.campaigns_id
			WHERE f.hash = {$in['hash']}
			ORDER BY f.`id` DESC
			LIMIT 1
		");
	}

	// --
	function db_userSetHash($in) { cleanup($in);
		return db_MAIN("
			UPDATE mb_recruiter
			SET hash = {$in['hash']}
			WHERE id = {$in['id']}
		");
	}

	// --
	function db_friendSetHash($in) { cleanup($in);
		return db_MAIN("
			UPDATE mb_friends
			SET hash = {$in['hash']}
			WHERE recruiter_id = {$in['recruiter']}
			  AND mail = {$in['mail']}
		");
	}

	// --
	function db_userGetHash($in) { cleanup($in);
		return db_MAIN("
			SELECT id, campaigns_id, firstname, lastname, mail
			FROM mb_recruiter
			WHERE hash = {$in['hash']}
		");
	}
/*
 	function db_userFind($in) { cleanup($in);
 		return db_MAIN("
 			SELECT id
 			FROM recruiter
 			WHERE mail = {$in['mail']}
 			  AND campaigns_id = {$in['campaign']}
		");
	}
*/
	// --
	function db_orderCreate($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `mb_orders`
				(`postnr`, `co`, `poststed`, `gate`, `husnummer`, `oppgang`, `etasje`, `leilighet`, `mobil`, `telefon`, `agreement`, `campaigns_id`, `ip`)
			VALUES(
				{$in['postnr']},
				{$in['co']},
				{$in['poststed']},
				{$in['gate']},
				{$in['husnummer']},
				{$in['oppgang']},
				{$in['etasje']},
				{$in['leilighet']},
				{$in['mobil']},
				{$in['telefon']},
				{$in['agreement']},
				{$in['campaigns_id']},
				{$in['ip']}
			)
		");
	}

	function db_connectRecruitOrder($in) { cleanup($in);
		return db_MAIN("
			UPDATE `mb_friends`
			SET `orders_id` = {$in['order_id']}
			WHERE `id` = {$in['recruit_id']}
		");
	}

	function db_orderFind($in) { cleanup($in);
		return db_MAIN("
			SELECT o.*, f.firstname, f.lastname, f.mail, r.firstname AS fname2, r.lastname AS lname2, r.mail AS mail2
			FROM `mb_orders` o
			LEFT OUTER JOIN `mb_friends` f
			ON o.id = f.orders_id
			LEFT OUTER JOIN `mb_recruiter` r
			ON o.id = r.orders_id
			WHERE o.id = {$in['order']}
		");
	}

	function db_confirmOrder($in) { cleanup($in);
		return db_MAIN("
			UPDATE `mb_orders`
			SET `mc_id` = {$in['mediaconnect']}
			WHERE `id` = {$in['order']}
		");
	}

	function db_recruitUpdateInfo($in) { cleanup($in);
		return db_MAIN("
			UPDATE `mb_friends`
			SET
				`firstname` = {$in['firstname']},
				`lastname` = {$in['lastname']},
				`mail` = {$in['mail']}
			WHERE `id` = {$in['recruit_id']}
		");
	}






// Database main functions (does all the talking to the database class and handling of errors)
// ****************************************************************************	

	function db_FIND($sql)
	{
		global $mysqli;
		$result = $mysqli->query( $sql );
		if ( $result )
		{
			if ($result->num_rows > 0) {
				return $result;
			} else {
				return null;
			}
		} else {
			db_printError($mysqli->error, $mysqli->errno, $sql);
			return null;
		}
	}

	// Run SQL that doesn't return a dataset, and return inserted id
	function db_INSERT($sql)
	{
		global $mysqli;
		$result = $mysqli->query($sql);
		if ( $result )
			return $mysqli->insert_id;
		else {
			db_printError($mysqli->error, $mysqli->errno, $sql);
			return -1;
		}
	}

	function db_EXEC($sql)
	{
		global $mysqli;
		$result = $mysqli->query($sql);
		if ( $result )
			return $mysqli->affected_rows;
		else {
			db_printError($mysqli->error, $mysqli->errno, $sql);
			return -1;
		}
	}

	// Enklare db-brygga som væljer SQL-funktion baserat på typ av SQL man skickar in (førutsætter att man ej blandar typer).
	// Anledningen till de olika versionerna ær att de returnerar olika saker och datatyper.
	function db_MAIN($sql)
	{
		switch(substr(trim($sql),0,6))
		{
			case "SELECT":
				return db_FIND($sql);
				break;

			case "UPDATE":
				return db_EXEC($sql);
				break;

			case "DELETE":
				return db_EXEC($sql);
				break;

			case "INSERT": 
				return db_INSERT($sql);
				break;

			// Skulle mot førmodan någon annan SQL-typ skickas in så chansa på att det ær någon form av SQL som returnerar ett dataset (som SELECT).
			default:
				return db_FIND($sql);
				break;
		}
	}

	// Enkel felhantering
	function db_printError($error_msg, $error_no, $sql)
	{
		echo "
		<div class='errors'>
			<p>
				There has been an error from MySQL: (", $error_no, ") ", $error_msg, ".
			</p>
			<code>", nl2br($sql), "</code>
		</div>";
	}



// Helper functions for SQL
// ****************************************************************************	

	// Aktivera transaction-hantering
	function db_doBeginTran()
	{
		global $mysqli;
		$mysqli->autocommit(false);

		$_SESSION['ERRORS_TRAN'] = array();
	}

	// Gør commit eller rollback, och stæng av transaction-hantering
	function db_doEndTran()
	{
		global $mysqli;

		if (!empty($_SESSION['ERRORS_TRAN'])) {
			$mysqli->rollback();
		}
		$mysqli->commit();
		
		// Reset autocommit to true (only the SQL's just before needed transaction support)
		$mysqli->autocommit(true);

		$_SESSION['ERRORS_TRAN'] = null;
	}

	// db_EXEC-funktioner skickar in en array som "tvættas" med quote_smart i denna funktion
	function cleanup(&$in)
	{
		foreach($in as $key => $value) {
			$in[$key] = quote_smart($value);
		}
	}

	// Smart stræng/int-hantering før SQL:er
	// http://norskwebforum.no/viewtopic.php?p=243716
	function quote_smart($value)
	{
		global $mysqli;
		
		if ( get_magic_quotes_gpc() && !is_null($value) ) {
			$value = stripslashes($value);
		}
		if ( is_numeric($value) && strpos($value,',') !== false ) {
			$value = str_replace(',', '.', $value);
		}
		if ( is_null($value) ) {
			$value = 'NULL';
		}
		elseif ( !is_numeric($value) ) {
			$value = "'" . $mysqli->real_escape_string($value) . "'";
		}
		return $value;
	}
?>