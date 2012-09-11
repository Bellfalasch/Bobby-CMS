<?php
	/* Set up template variables */
	$pagetitle = 'Admin/Overview';
?>
<?php require('_header.php'); ?>


	<div class="page-header">
		<h1>
			Overview
			<small>Summary of all the sales</small>
		</h1>
	</div>

	<?php
		// Get date from QS and make sure it's safe and put into seperate variables (y,m,d).
		if ( isset($_GET['date']) ) {
			if ( strtotime($_GET['date']) )
			{
				$qsFull = $_GET['date'];

				if (mb_strlen($qsFull) === 7) // Padda datum utan dag
					$qsFull .= "-00";

				if (mb_strlen($qsFull) === 10)
				{
					$qsYear  = mb_substr($qsFull,0,4);
					$qsMonth = mb_substr($qsFull,5,2);
					$qsDay   = mb_substr($qsFull,8,2);
					
					if (!is_numeric($qsYear))  $qsYear  = "2000";
					if (!is_numeric($qsMonth)) $qsMonth = "01";
					if (!is_numeric($qsDay))   $qsDay   = "01";

					//$qsFull = $qsYear . '-' . $qsMonth . '-' . $qsDay; // Lægg tillbaks tvættade variabler i stora variabeln (vi gør lænkar av den sen).
				
				} else {
					unset($qsFull); // Førstør variabeln om den inte innehåller exakt 10 tecken (før då ær den inte riktig).
				}
			}
		}

		// Get invoice selected (if any)
		$qsInvoice = 0;
		if ( isset($_GET['invoice']) ) {
			if ( is_numeric($_GET['invoice']) )
			{
				$qsInvoice = $_GET['invoice'];
			}
		}
	?>

	<?php
		// Vald dag (samt månad och år), lista alla fakturor
		if (isset($qsDay))
		{
			$result = db_getInvoicesStatsDay( array(
						'year' => $qsYear,
						'month' => $qsMonth,
						'day' => $qsDay
					) );
			if (!is_null($result)) {

				//echo "<h2>Dagsvisning $qsYear-$qsMonth-$qsDay</h2>";

				//echo "<p><strong>$result->num_rows</strong> olika fakturor</p>";

				echo "
				<table>
					<caption>Dygnsöversikt: " . strDateToNorwegian('D', date($qsYear . '-' . $qsMonth . '-' . $qsDay) ) . " $qsDay " . strDateToNorwegian('M', date($qsYear . '-' . $qsMonth) ) . " $qsYear</caption>
					<thead>
						<tr>
							<th>Fakturanummer</th>
							<th>Tidpunkt</th>
							<th>Försäljning</th>
							<th>Moms</th>
							<th>Summa</th>
							<th>DIBS-ID</th>
						</tr>
					</thead>
					<tbody>
				";

				$totAnt = 0;
				$totSale = 0;
				$totMva = 0;
				$totTot = 0;

				while ( $row = $result->fetch_object() )
				{
					$totAnt += $row->antal;
					$totSale += $row->sale;
					$totMva += $row->mvan;
					$totTot += $row->totalt;

					echo "
					<tr>
						<th><a href=\"$SYS_folder/_admin/invoice.php?id=$row->invoice_no\">$row->invoice_no</a></th>
						<td>" . date("H:i:s", strtotime($row->dibs_date)) . "</td>
						<td>$row->sale</td>
						<td>$row->mvan</td>
						<td>$row->totalt</td>
						<td>$row->dibs_transid</td>
					</tr>
					";
				}

				echo "
					</tbody>
					<tfoot>
						<tr>
							<td>$result->num_rows st. fakturor</td>
							<td>-</td>
							<td>" . formatMoney($totSale) . " NOK</td>
							<td>" . formatMoney($totMva) . " NOK</td>
							<td>" . formatMoney($totTot) . " NOK</td>
						</tr>
					</tfoot>
				</table>
				";
			}
		}


		echo "<hr />";

		// Vald månad (och år), lista alla dagar
		if (isset($qsMonth))
		{
			$result = db_getInvoicesStatsMonth( array(
						'year' => $qsYear,
						'month' => $qsMonth
					) );
			if (!is_null($result)) {

				//echo "<h2>Månadsvisning $qsYear-$qsMonth</h2>";

				//echo "<p><strong>$result->num_rows</strong> olika dagar</p>";

				echo "
				<table>
					<caption>Månadsöversikt: " . strDateToNorwegian('M', date($qsYear . '-' . $qsMonth) ) . " $qsYear</caption>
					<thead>
						<tr>
							<th>Dag</th>
							<th>Fakturor</th>
							<th>Försäljning</th>
							<th>Moms</th>
							<th>Summa</th>
						</tr>
					</thead>
					<tbody>
				";

				$totAnt = 0;
				$totSale = 0;
				$totMva = 0;
				$totTot = 0;

				while ( $row = $result->fetch_object() )
				{
					$totAnt += $row->antal;
					$totSale += $row->sale;
					$totMva += $row->mvan;
					$totTot += $row->totalt;

					echo "
					<tr>
						<th><a href=\"?date=$qsYear-$qsMonth-$row->dag\">$row->dag " . strDateToNorwegian('D', date($qsYear . '-' . $qsMonth . '-' . $row->dag) ) . "</a></th>
						<td>$row->antal stycken</td>
						<td>$row->sale</td>
						<td>$row->mvan</td>
						<td>$row->totalt</td>
					</tr>
					";
				}

				echo "
					</tbody>
					<tfoot>
						<tr>
							<td>$result->num_rows dagar</td>
							<td>$totAnt stycken</td>
							<td>" . formatMoney($totSale) . " NOK</td>
							<td>" . formatMoney($totMva) . " NOK</td>
							<td>" . formatMoney($totTot) . " NOK</td>
						</tr>
					</tfoot>
				</table>
				";
			}
		}
	?>


	<hr />
<!--	<h2>Full øversikt</h2>-->
	<?php

		$result = db_getInvoicesStatsAll();
		if (!is_null($result)) {

			//echo "<p><strong>$result->num_rows</strong> olika månader</p>";

			$thisYear = 2013;
			$endTable = false;

			$totAnt = 0;
			$totSale = 0;
			$totMva = 0;
			$totTot = 0;

			while ( $row = $result->fetch_object() )
			{
				$totAnt += $row->antal;
				$totSale += $row->sale;
				$totMva += $row->mvan;
				$totTot += $row->totalt;

				// Output headertable on new year
				if ($row->ar < $thisYear) {
					
					$thisYear = $row->ar;

					// Avsluta table innan nytt påbørjas (variabel sætts først efter att det allra førsta tablet lagats).
					if ($endTable)
					{
						echo "
							</tbody>
							<tfoot>
								<tr>
									<td>$result->num_rows st. fakturor</td>
									<td>-</td>
									<td>" . formatMoney($totSale) . " NOK</td>
									<td>" . formatMoney($totMva) . " NOK</td>
									<td>" . formatMoney($totTot) . " NOK</td>
								</tr>
							</tfoot>
						</table>
						";

						$totAnt = 0;
						$totSale = 0;
						$totMva = 0;
						$totTot = 0;
					}

					echo "
					<table>
						<caption>Årsöversikt: $thisYear</caption>
						<thead>
							<tr>
								<th>Månad</th>
								<th>Fakturor</th>
								<th>Försäljning</th>
								<th>Moms</th>
								<th>Summa</th>
							</tr>
						</thead>
						<tbody>
					";

					$endTable = true;
				}

				echo "
					<tr>
						<th><a href=\"?date=$row->ar-$row->manad\">" . strDateToNorwegian('M', date($row->ar . '-' . $row->manad) ) . "</a></th>
						<td>$row->antal stycken</td>
						<td>$row->sale</td>
						<td>$row->mvan</td>
						<td>$row->totalt</td>
					</tr>
				";
			}

			echo "
				</tbody>
				<tfoot>
					<tr>
						<td>-</td>
						<td>$totAnt</td>
						<td>" . formatMoney($totSale) . " NOK</td>
						<td>" . formatMoney($totMva) . " NOK</td>
						<td>" . formatMoney($totTot) . " NOK</td>
					</tr>
				</tfoot>
			</table>
			";
		}

	?>


<?php require('_footer.php'); ?>