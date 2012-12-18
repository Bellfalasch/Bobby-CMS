<?php

	// All these SQLs can be deleted. They are just listed here as examples of structure and usage, also they are used in the
	// example admin templates.

	// However, the last few functions headlined "Users" is used for the login-system and they are critical for the admin.

	//////////////////////////////////////////////////////////////////////////////////
	//////////////////////////////////////////////////////////////////////////////////

	function db2_searchInvoice($in) { cleanup($in);
		return db_MAIN("
			SELECT o.*, c.*
			FROM `orders` o
			LEFT OUTER JOIN `customers` c
			ON c.id = o.customer_id
			WHERE
			o.invoice_no <> '' AND
			(
				c.`firstname` LIKE {$in['name']}
			OR	c.`lastname` LIKE {$in['name']}
			OR	c.`street1` LIKE {$in['address']}
			OR	c.`street2` LIKE {$in['address']}
			OR	c.`mail` LIKE {$in['mail']}
			OR	LEFT(o.`dibs_date`,10) LIKE {$in['date']}
			OR	o.`invoice_no` LIKE {$in['invoice']}
			OR	o.`dibs_transid` LIKE {$in['transaction']}
			)
			ORDER BY o.`dibs_date` DESC
		;");
	}

	/**
	 * Hæmta precis alla fakturor (som betalats) och sammanstæll dem per månad før øverblick och revisorn.
	 * @return mysqli->query			fields: xxx
	 */
	function db_getInvoicesStatsAll() {
		return db_FIND("
			SELECT
				SUM(o.`sum`) AS totalt,
				SUM(o.`mva`) AS mvan,
				SUM(o.`sum`) - SUM(o.`mva`) AS sale,
				DATE_FORMAT(o.`dibs_date`,'%Y') AS ar,
				DATE_FORMAT(o.`dibs_date`,'%m') AS manad,
				COUNT(*) AS antal
			FROM `orders` o
			WHERE o.`invoice_no` IS NOT NULL
			GROUP BY
				DATE_FORMAT(o.`dibs_date`,'%Y-%m')
			ORDER BY
				YEAR(o.`dibs_date`) DESC,
				MONTH(o.`dibs_date`) ASC
		");
	}
	function db2_getCampaign($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `title`, `url`, `start`, `stop`, `shortinfo`, `verv_step1`, `verv_step2`, `verv_takk`, `give_step1`, `give_takk`, `image`
			FROM `campaigns`
			WHERE id = {$in['id']}
		");
	}
	function db2_getCampaignsActive() {
		return db_MAIN("
			SELECT `id`, `title`, `url`, `start`, `stop`, `shortinfo`, `verv_step1`, `verv_step2`, `verv_takk`, `give_step1`, `give_takk`, `image`
			FROM `campaigns`
			WHERE NOW() BETWEEN start AND stop
		");
	}
	function db2_updateCampaign($in) { cleanup($in);
		return db_MAIN("
			UPDATE `campaigns`
			SET
				`title` = {$in['title']},
				`url` = {$in['url']},
				`start` = {$in['start']},
				`stop` = {$in['stop']},
				`shortinfo` = {$in['short_info']},
				`verv_step1` = {$in['verv_step1']},
				`verv_step2` = {$in['verv_step2']},
				`verv_takk` = {$in['verv_takk']},
				`give_step1` = {$in['give_step1']},
				`give_takk` = {$in['give_takk']},
				`image` = {$in['image']}
			WHERE `id` = {$in['id']}
		");
	}
	function db2_createCampaign($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `campaigns`
				(`title`, `url`, `start`, `stop`, `shortinfo`, `verv_step1`, `verv_step2`, `verv_takk`, `give_step1`, `give_takk`, `image`)
			VALUES(
				{$in['title']},
				{$in['url']},
				{$in['start']},
				{$in['stop']},
				{$in['short_info']},
				{$in['verv_step1']},
				{$in['verv_step2']},
				{$in['verv_takk']},
				{$in['give_step1']},
				{$in['give_takk']},
				{$in['image']}
			)
		");
	}


	//////////////////////////////////////////////////////////////////////////////////
	// USERS
	//////////////////////////////////////////////////////////////////////////////////

	function db2_getUserLoginInfo($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `name`, `username`, `password`, `mail`, `level`
			FROM `nxtcms_users`
			WHERE `username` LIKE {$in['username']}
			LIMIT 1
		;");
	}
	function db2_getUsers() {
		return db_MAIN("
			SELECT `id`, `name`, `username`, `password`, `mail`, `level`
			FROM `nxtcms_users`
			ORDER BY `id` DESC
		");
	}
	function db2_getUser($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `name`, `username`, `password`, `mail`, `level`
			FROM `nxtcms_users`
			WHERE id = {$in['id']}
		");
	}
	function db2_setUpdateUser($in) { cleanup($in);
		return db_MAIN("
			UPDATE `nxtcms_users`
			SET
				`name` = {$in['name']},
				`username` = {$in['username']},
				`mail` = {$in['mail']},
				`password` = {$in['password']},
				`level` = {$in['level']}
			WHERE `id` = {$in['id']}
		");
	}
	function db2_setUser($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `nxtcms_users`
				(`name`,`username`,`mail`,`password`,`level`)
			VALUES(
				{$in['name']},
				{$in['username']},
				{$in['mail']},
				{$in['password']},
				{$in['level']}
			)
		");
	}
	function db2_delUser($in) { cleanup($in);
		return db_MAIN("
			DELETE FROM `nxtcms_users`
			WHERE `id` = {$in['id']}
		");
	}

?>