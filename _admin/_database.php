<?php

	// All these SQLs can be deleted. They are just listed here as examples of structure and usage, also they are used in the
	// example admin templates.

	// However, the last few functions headlined "Users" is used for the login-system and they are critical for the admin.




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
	function db_getInvoicesStatsMonth($in) { cleanup($in);
		return db_FIND("
			SELECT
				SUM(o.`sum`) AS totalt,
				SUM(o.`mva`) AS mvan,
				SUM(o.`sum`) - SUM(o.`mva`) AS sale,
				DATE_FORMAT(o.`dibs_date`,'%Y') AS ar,
				DATE_FORMAT(o.`dibs_date`,'%m') AS manad,
				DATE_FORMAT(o.`dibs_date`,'%d') AS dag,
				COUNT(*) AS antal
			FROM `orders` o
			WHERE (o.`invoice_no` IS NOT NULL)
			AND DATE_FORMAT(o.`dibs_date`,'%Y') = {$in['year']}
			AND DATE_FORMAT(o.`dibs_date`,'%m') = {$in['month']}
			GROUP BY
				DATE_FORMAT(o.`dibs_date`,'%Y-%m-%d')
			ORDER BY
				DAY(o.`dibs_date`) ASC
		");
	}
	function db_getInvoicesStatsDay($in) { cleanup($in);
		return db_MAIN("
			SELECT o.`sum` AS totalt, o.`mva` AS mvan, o.`sum` - o.`mva` AS sale,
			o.`invoice_no`, o.`dibs_date`, o.`dibs_transid`, o.`id` AS cart_id
			FROM `orders` o
			WHERE (o.`invoice_no` IS NOT NULL)
			AND DATE_FORMAT(o.`dibs_date`,'%Y') = {$in['year']}
			AND DATE_FORMAT(o.`dibs_date`,'%m') = {$in['month']}
			AND DATE_FORMAT(o.`dibs_date`,'%d') = {$in['day']}
			ORDER BY o.`dibs_date` ASC
		");
	}
	function db_getInvoice($in) { cleanup($in);
		return db_MAIN("
			SELECT o.`sum`, o.`mva`, o.cart_id, o.`invoice_no`, o.`dibs_date`, o.`dibs_transid`, o.`id` AS order_id, c.*
			FROM `orders` o
			LEFT OUTER JOIN `customers` c
			ON c.id = o.customer_id
			WHERE o.`invoice_no` = {$in['invoice']}
		");
	}

	// Get every credit note for a selected invoice
	function db2_getInvoiceCredits($in) { cleanup($in);
		return db_MAIN("
			SELECT c.id, c.`date`, c.sum, c.mva, c.note, c.order_id, c.credit_no, u.mail
			FROM `order_credit` c
			LEFT OUTER JOIN users u
			ON u.id = c.user_id
			WHERE c.`order_id` = {$in['order']}
		");
	}
	function db2_createInvoiceCredits($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `order_credit`(`order_id`,`sum`,`mva`,`note`,`credit_no`,`user_id`)
			VALUES(
				{$in['order']},
				{$in['sum']},
				{$in['mva']},
				{$in['note']},
				{$in['credit_no']},
				{$in['user_id']}
			)
		");
	}
	// Each credit note needs a unique id-number that doesn't contain holes, find the current highest value.
	function db2_getHighestCreditNo() {
		return db_MAIN("
			SELECT MAX(`credit_no`) AS `creditno`
			FROM `order_credit`
		");
	}
	function db2_getInvoicesFromSpan($in) { cleanup($in);
		return db_MAIN("
			SELECT o.`sum`, o.`mva`, o.`invoice_no`, o.`dibs_date`, o.`dibs_transid`, o.`id`
			FROM `orders` o
			WHERE (o.`invoice_no` IS NOT NULL)
			AND o.`dibs_date` BETWEEN {$in['from']} AND {$in['to']}
			ORDER BY o.`dibs_date` ASC
		");
	}
	function db2_getCreditsFromSpan($in) { cleanup($in);
		return db_MAIN("
			SELECT c.id, c.`date`, c.sum, c.mva, c.note, c.order_id, c.credit_no, o.invoice_no
			FROM order_credit c
			LEFT OUTER JOIN `orders` o
			ON o.id = c.order_id
			WHERE (o.`invoice_no` IS NOT NULL)
			AND c.`date` BETWEEN {$in['from']} AND {$in['to']}
			ORDER BY c.`date` ASC
		");
	}

	////////////////// CAMPAIGNS //////////////////////

	function db2_getCampaignsActive() {
		return db_MAIN("
			SELECT `id`, `title`, `start`, `stop`
			FROM `campaigns`
			WHERE NOW() BETWEEN `start` AND `stop`
			ORDER BY `id` DESC
		");
	}
	function db2_getCampaignsInactive() {
		return db_MAIN("
			SELECT `id`, `title`, `start`, `stop`
			FROM `campaigns`
			WHERE NOW() NOT BETWEEN `start` AND `stop`
			ORDER BY `id` DESC
		");
	}
	function db2_getCampaign($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `title`, `url`, `start`, `stop`, `shortinfo`, `verv_step1`, `verv_step2`, `verv_takk`, `give_step1`, `give_takk`, `image`
			FROM `campaigns`
			WHERE id = {$in['id']}
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



	////////////////// USERS //////////////////////

	function db_getUserLoginInfo($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `password`, `mail`, `level`
			FROM `admins`
			WHERE `mail` LIKE {$in['username']}
			LIMIT 1
		;");
	}
	function db_getUsers() {
		return db_MAIN("
			SELECT `id`, `password`, `mail`, `level`
			FROM `admins`
			ORDER BY `id` DESC
		");
	}
	function db_getUser($in) { cleanup($in);
		return db_MAIN("
			SELECT `id`, `password`, `mail`, `level`
			FROM `admins`
			WHERE id = {$in['id']}
		");
	}
	function db_setUpdateUser($in) { cleanup($in);
		return db_MAIN("
			UPDATE `admins`
			SET
				`mail` = {$in['mail']},
				`password` = {$in['password']},
				`level` = {$in['level']}
			WHERE `id` = {$in['id']}
		");
	}
	function db_setUser($in) { cleanup($in);
		return db_MAIN("
			INSERT INTO `admins`
				(`mail`,`password`,`level`)
			VALUES(
				{$in['mail']},
				{$in['password']},
				{$in['level']}
			)
		");
	}
	function db_delUser($in) { cleanup($in);
		return db_MAIN("
			DELETE FROM `admins`
			WHERE `id` = {$in['id']}
		");
	}

?>