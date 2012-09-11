<?php
	/* Set up template variables */
	$pagetitle = 'Admin/Date span';
?>
<?php require('_header.php'); ?>


	<div class="page-header">
		<h1>
			Date span
			<small>Get a summary of all the sales between two set dates</small>
		</h1>
	</div>

	<?php

		$formDate1 = qsGet('date1');
		$formDate2 = qsGet('date2');

		if (qsGet('search') === 'search')
		{

			if ($formDate1 == '') {
				$formDate1 = '2012-06-27';
			}

			if ($formDate2 == '') {
				$formDate2 = date('Y-m-d');
			}

		}

	?>

	<p>
		Every invoice from the given dates will also be included. So, to get all the invoices from only one day you would 
		search for: from 2012-07-26 to 2012-07-26. You could also leave the "From"-field empty to automatically use the first
		invoice date. And if you leave the "To"-field blank you will use today's date =)
	</p>
	<p>&nbsp;</p>

	<form class="well form-horizontal" action="" method="get" style="overflow:hidden;">

		<div class="span4">

			<div class="control-group">
				<?php
					$thisLabel = "From this date:";
					$thisId = "Date1";
					$thisVar = $formDate1;
					$thisDesc = "YYYY-MM-DD";
					$thisLength = 10;
				?>
				<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
				<div class="controls">
					<input type="text" name="<?= strtolower($thisId) ?>" class="input-medium" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
					<p class="help-block"><?= $thisDesc ?></p>
				</div>
			</div>

		</div>
		<div class="span4">

			<div class="control-group">
				<?php
					$thisLabel = "To this date";
					$thisId = "Date2";
					$thisVar = $formDate2;
					$thisDesc = "YYYY-MM-DD";
					$thisLength = 10;
				?>
				<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
				<div class="controls">
					<input type="text" name="<?= strtolower($thisId) ?>" class="input-medium" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
					<p class="help-block"><?= $thisDesc ?></p>
				</div>
			</div>

		</div>
		<div class="span2">

			<div class="control-group">
				<div class="controls">
					<button type="submit" name="search" value="search" class="btn btn-primary">Search</button>
				</div>
			</div>

		</div>

	</form>

	<?php

		//if ($_SERVER['REQUEST_METHOD'] === 'POST')
		if (qsGet('search') === 'search')
		{

			if ( strtotime($formDate1) && strtotime($formDate2) )
			{
				$result = db2_getInvoicesFromSpan( array(
							'from' => $formDate1 . ' 00:00:00',
							'to' => $formDate2 . ' 23:59:59'
						) );

				if (!is_null($result))
				{

					$tot_sales = 0;
					$tot_mva = 0;
					$tot_sum = 0;

					echo "
					<table>
						<caption>Invoices from your query</caption>
						<thead>
							<tr>
								<th>Date and time</th>
								<th>Invoice_no</th>
								<th>Dibs transID</th>
								<th>Value</th>
								<th>Mva</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>
					";

					while ( $row = $result->fetch_object() )
					{
						$tot_sales += ($row->sum - $row->mva);
						$tot_mva += $row->mva;
						$tot_sum += $row->sum;

						echo "
						<tr>
							<th>$row->dibs_date</th>
							<td><a href=\"$SYS_folder/_admin/invoice.php?id=$row->invoice_no\">$row->invoice_no</a></td>
							<td>$row->dibs_transid</td>
							<td><span class='pull-right'>" . formatMoney($row->sum - $row->mva) . "</span></td>
							<td><span class='pull-right'>" . formatMoney($row->mva) . "</span></td>
							<td><span class='pull-right'>" . formatMoney($row->sum) . "</span></td>
						</tr>
						";
					}

					echo "
						</tbody>
						<tfoot>
							<tr>
								<th>-</th>
								<th>$result->num_rows</th>
								<th>-</th>
								<th><span class='pull-right'>" . formatMoney($tot_sales) . "</span></th>
								<th><span class='pull-right'>" . formatMoney($tot_mva) . "</span></th>
								<th><span class='pull-right'>" . formatMoney($tot_sum) . "</span></th>
							</tr>
						</tfoot>
					</table>
					";

				} else {

					echo '<div class="alert alert-error"><h4>Failure</h4><p>No invoices found between the two date spans you searched for.</p></div>';

				}


				////////////////////////////////////////////////////////////
				// Credit notes

				//c.id, c.`date`, c.sum, c.mva, c.note, c.order_id, c.credit_no, o.invoice_no
				$result = db2_getCreditsFromSpan( array(
							'from' => $formDate1 . ' 00:00:00',
							'to' => $formDate2 . ' 23:59:59'
						) );

				if (!is_null($result))
				{

					$tot_sales = 0;
					$tot_mva = 0;
					$tot_sum = 0;

					echo "
					<table>
						<caption>Credit notes from your query</caption>
						<thead>
							<tr>
								<th>Date and time</th>
								<th>For Invoice_no</th>
								<th>Credit no</th>
								<th>Value</th>
								<th>Mva</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>
					";

					while ( $row = $result->fetch_object() )
					{
						$tot_sales += ($row->sum - $row->mva);
						$tot_mva += $row->mva;
						$tot_sum += $row->sum;

						echo "
						<tr>
							<th>$row->date</th>
							<td><a href=\"$SYS_folder/_admin/invoice.php?id=$row->invoice_no\">$row->invoice_no</a></td>
							<td>K$row->credit_no</td>
							<td><span class='pull-right'>" . formatMoney($row->sum - $row->mva) . "</span></td>
							<td><span class='pull-right'>" . formatMoney($row->mva) . "</span></td>
							<td><span class='pull-right'>" . formatMoney($row->sum) . "</span></td>
						</tr>
						";
					}

					echo "
						</tbody>
						<tfoot>
							<tr>
								<th>-</th>
								<th>$result->num_rows</th>
								<th>-</th>
								<th><span class='pull-right'>" . formatMoney($tot_sales) . "</span></th>
								<th><span class='pull-right'>" . formatMoney($tot_mva) . "</span></th>
								<th><span class='pull-right'>" . formatMoney($tot_sum) . "</span></th>
							</tr>
						</tfoot>
					</table>
					";

				} else {

					echo '<div class="alert alert-error"><h4>Nothing found</h4><p>No credit notes found between the two date spans you searched for.</p></div>';

				}
				////////////////////////////////////////////////////////////
			}
		}

	?>


<?php require('_footer.php'); ?>