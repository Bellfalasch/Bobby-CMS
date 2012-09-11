<?php
	/* Set up template variables */
	$pagetitle = 'Admin/Discounts';
?>
<?php require('_header.php'); ?>


	<?php

		$discount_id = -1;

		$formTitle = '';
		$formCode = '';
		$formStart = '';
		$formStop = '';
		$formPercent = '';
		$formInfo = '';

		if (isset($_GET['id']))
			$discount_id = trim( $_GET['id'] );

		
		if (isset($_GET['del']) && $_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			$del_id = trim( $_GET['del'] );

			$del = db2_delDiscount( array(
						'id' => $del_id
					) );

			if ($del >= 0)
				echo '<div class="alert alert-success"><h4>Delete successful</h4><p>The User is now deleted</p></div>';
			else
				pushError('Delete of User failed, please try again.');
		}
		

		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{
			$formTitle = formGet('title');
			$formCode = strtoupper(formGet('code'));
			$formStart = formGet('start');
			$formStop = formGet('stop');
			$formPercent = formGet('percent');
			$formInfo = formGet('info');

			if ($formTitle == '') {
				pushError('No "Title" entered!');
			}
			if ($formCode == '') {
				pushError('No "Code" entered!');
			}
			if ($formStart == '') {
				$formStart = date('Y-m-d');
			}
			if ($formStop == '') {
				$purchase_date = date('Y-m-d');
				$purchase_date_timestamp = strtotime($purchase_date);
				$purchase_date_3months = strtotime("+3 months", $purchase_date_timestamp);

				$formStop = date("Y-m-d", $purchase_date_3months);
			}
			if ($formPercent == '' && $discount_id === -1) {
				pushError('No "Percentage" entered!');
			}
			if ($formInfo == '') {
				$formInfo = null;
			}

			if (empty($_SESSION['ERRORS'])) {
				
				// UPDATE
//echo $discount_id;

				if ( $discount_id > 0 )
				{
					$result = db2_updateDiscount( array(
								'title' => $formTitle,
								'code' => $formCode,
								'start' => $formStart . ' 00:00:00',
								'stop' => $formStop . ' 23:59:59',
								'info' => $formInfo,
								'id' => $discount_id
							) );

					if ($result >= 0) {
						//echo "Sparat!";
						echo '<div class="alert alert-success"><h4>Save successful</h4><p>Discount oppdatert</p></div>';
					} else {
						pushError("IKKE sparat");
					}

				// CREATE
				} else {

					$result = db2_createDiscount( array(
								'title' => $formTitle,
								'code' => $formCode,
								'start' => $formStart . ' 00:00:00',
								'stop' => $formStop . ' 23:59:59',
								'percentage' => $formPercent,
								'info' => $formInfo
							) );

					if ($result > 0) {
						//echo "Nytt med id $result";
						echo '<div class="alert alert-success"><h4>Save successful</h4><p>Ny Discount lagrat, id: ' . $result . '</p></div>';

						$discount_id = -1;
						$formTitle = '';
						$formCode = '';
						$formStart = '';
						$formStop = '';
						$formPercent = '';
						$formInfo = '';

					} else {
						pushError("IKKE sparat");
					}
				}
			}

		} //else {

			if ( $discount_id > 0 )
			{
				$result = db2_getDiscount( array('id' => $discount_id) );

				if (!is_null($result))
				{
					$row = $result->fetch_object();

					$formTitle = $row->title;
					$formCode = $row->code;
					$formStart = substr($row->start,0,10);
					$formStop = substr($row->stop,0,10);
					$formPercent = $row->percentage;
					$formInfo = $row->info;

				} else {
					pushError("Couldn't find the requested Discount");
				}
			}
		//}

	?>

	<div class="page-header">
		<h1>
			Discounts
			<small>create and manage discounts</small>
		</h1>
	</div>

	<?php outputErrors($_SESSION['ERRORS']); ?>

	<div class="row">
		<div class="span7">

			<form class="form-horizontal" action="" method="post">
				
				<div class="control-group">
					<?php
						$thisLabel = "Title:";
						$thisId = "Title";
						$thisVar = $formTitle;
						$thisDesc = "This is the text that will be displayed to the buyer in the shopping cart, after payment is complete, and in their reciept (the PDF). Keep it short and simple =)";
						$thisLength = 50;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-xlarge" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Code:";
						$thisId = "Code";
						$thisVar = $formCode;
						$thisDesc = "Use no more than 12 characters! The short code to be used to activate this discount in the shopping cart. Keep it simple. It will automatically be converted to UPPERCASE.";
						$thisLength = 12;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-medium" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Start date:";
						$thisId = "Start";
						$thisVar = $formStart;
						$thisDesc = "(YYYY-MM-DD) The date from which the discount will be active. Leave blank if you want to use today's date. The entire date will be valid for this discount (starting from 00:00:00).";
						$thisLength = 10;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-medium" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Stop date:";
						$thisId = "Stop";
						$thisVar = $formStop;
						$thisDesc = "(YYYY-MM-DD) The date to which the discount will be active. Leave blank if you want to use the start date + 3 months. The entire date will be valid for this discount (stopping just before midnight, at 23:59:59).";
						$thisLength = 10;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-medium" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Percentage:";
						$thisId = "Percent";
						$thisVar = $formPercent;
						$thisDesc = "Percentage in whole numbers without the %-sign. Correct: '20'. Wrong: '20%', '0.20', '0,2'!";
						$thisLength = 2;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-xsmall<?php if ($discount_id > 0) { echo ' disabled'; } ?>" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>"<?php if ($discount_id > 0) { echo ' disabled="disabled"'; } ?> />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Information:";
						$thisId = "Info";
						$thisVar = $formInfo;
						$thisDesc = "Any extra information you want to add to this discount?";
						$thisLength = 255;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="span5" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>
				
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary">Save</button>

						<?php if ($discount_id > 0 && 1 == 2) { ?>
						<a href="?del=<?= $discount_id ?>" class="btn btn-mini btn-danger">Delete</a>
						<?php } ?>
					</div>
				</div>

			</form>

		</div>



		<div class="span4 offset1">

			<a class="btn btn-success" href="?"><i class="icon-plus-sign icon-white"></i> Add new Discount</a>

			<hr />

			<h4>Active discounts</h4>
			<?php
				$result = db2_getDiscountsActive();

				if (!is_null($result))
				{
					while ( $row = $result->fetch_object() )
					{
						echo "<a href='?id=" . $row->id . "'>" . $row->code . "</a> - " . $row->title . "<br />";
					}
				}
				else
				{
					echo "<p>No active Discounts found</p>";
				}
			?>

			<hr />

		<div style="opacity:0.3;">
			<h4>Inactive discounts</h4>
			<?php
				$result = db2_getDiscountsInactive();

				if (!is_null($result))
				{
					while ( $row = $result->fetch_object() )
					{
						echo "<a href='?id=" . $row->id . "'>" . $row->code . "</a> - " . $row->title . "<br />";
					}
				}
				else
				{
					echo "<p>No inactive Discounts found</p>";
				}
			?>
		</div>


		</div>
	</div>


<?php require('_footer.php'); ?>