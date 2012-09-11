<?php
	/* Set up template variables */
	$pagetitle = 'Admin/Campaigns';
?>
<?php require('_header.php'); ?>


	<?php

		// Prepare all the variables. If no form is sent the variables will be empty =)
		$this_id = -1;
		$this_name = "Campaign";

		$formTitle = formGet('title');
		$formUrl = formGet('url');
		$formStart = formGet('start');
		$formStop = formGet('stop');
		$formShortInfo = formGet('short_info');
		$formVervStep1 = formGet('verv_step1');
		$formVervStep2 = formGet('verv_step2');
		$formVervTakk = formGet('verv_takk');
		$formGiveStep1 = formGet('give_step1');
		$formGiveTakk = formGet('give_takk');
		$formImage = formGet('image');

		if (isset($_GET['id']))
			$this_id = qsGet('id');

/*
		if (isset($_GET['del']) && $_SERVER['REQUEST_METHOD'] !== 'POST')
		{
			$del_id = trim( $_GET['del'] );

			$del = db2_delDiscount( array(
						'id' => $del_id
					) );

			if ($del >= 0)
				echo "<div class='alert alert-success'><h4>Delete successful</h4><p>The $this_name is now deleted</p></div>";
			else
				pushError("Delete of $this_name failed, please try again.");
		}
*/		

		// User has posted (trying to save changes)
		if ($_SERVER['REQUEST_METHOD'] === 'POST')
		{

			// Begin validation and manipulation of data
			if ($formStart == '') {
				$formStart = date('Y-m-d');
			}
			if ($formStop == '') {
				$purchase_date = date('Y-m-d');
				$purchase_date_timestamp = strtotime($purchase_date);
				$purchase_date_3months = strtotime("+3 months", $purchase_date_timestamp);

				$formStop = date("Y-m-d", $purchase_date_3months);
			}

			if ($formTitle == '')
				pushError('No "Title" entered!');
			if ($formUrl == '')
				pushError('No "Url" entered!');
			if ($formShortInfo == '')
				pushError('No "Short info" entered!');
			if ($formVervStep1 == '')
				pushError('No "Verv step 1" entered!');
			if ($formVervStep2 == '')
				pushError('No "Verv step 2" entered!');
			if ($formVervTakk == '')
				pushError('No "Verv takk" entered!');
			if ($formGiveStep1 == '')
				pushError('No "Give step 1" entered!');
			if ($formGiveTakk == '')
				pushError('No "Give takk" entered!');

			// Null-handling
			if ($formImage == '')
				$formImage = null;

			// If no errors:
			if (empty($_SESSION['ERRORS'])) {
				
				// UPDATE
				if ( $this_id > 0 )
				{
					$result = db2_updateCampaign( array(
								'title' => $formTitle,
								'url' => $formUrl,
								'start' => $formStart . ' 00:00:00',
								'stop' => $formStop . ' 23:59:59',
								'short_info' => $formShortInfo,
								'verv_step1' => $formVervStep1,
								'verv_step2' => $formVervStep2,
								'verv_takk' => $formVervTakk,
								'give_step1' => $formGiveStep1,
								'give_takk' => $formGiveTakk,
								'image' => $formImage,
								'id' => $this_id
							) );

					if ($result >= 0) {
						echo "<div class='alert alert-success'><h4>Save successful</h4><p>$this_name oppdatert</p></div>";
					} else {
						pushError("IKKE sparat");
					}

				// CREATE
				} else {

					$result = db2_createCampaign( array(
								'title' => $formTitle,
								'url' => $formUrl,
								'start' => $formStart . ' 00:00:00',
								'stop' => $formStop . ' 23:59:59',
								'short_info' => $formShortInfo,
								'verv_step1' => $formVervStep1,
								'verv_step2' => $formVervStep2,
								'verv_takk' => $formVervTakk,
								'give_step1' => $formGiveStep1,
								'give_takk' => $formGiveTakk,
								'image' => $formImage
							) );

					if ($result > 0) {
						
						echo "<div class='alert alert-success'><h4>Save successful</h4><p>New $this_name lagrat, id: $result</p></div>";

						// After save we have to reset all variabels so that we get a new clean form
						$this_id = -1;
						$formTitle = '';
						$formUrl = '';
						$formStart = '';
						$formStop = '';
						$formShortInfo = '';
						$formVervStep1 = '';
						$formVervStep2 = '';
						$formVervTakk = '';
						$formGiveStep1 = '';
						$formGiveTakk = '';
						$formImage = '';

						// If you don't wanna show the message, you could just redirect back to this page instead of "cleaning" all the variables.
						//ob_clean();
						//header('Location: ' . $SYS_folder . '/campaign.php');

					} else {
						pushError("IKKE sparat");
					}
				}
			}

		}


		// If we have a given id, fetch form data from database.
		if ( $this_id > 0 )
		{
			$result = db2_getCampaign( array('id' => $this_id) );

			if (!is_null($result))
			{
				$row = $result->fetch_object();

				$formTitle = $row->title;
				$formUrl = $row->url;
				$formStart = substr($row->start,0,10);
				$formStop = substr($row->stop,0,10);
				$formShortInfo = $row->shortinfo;
				$formVervStep1 = $row->verv_step1;
				$formVervStep2 = $row->verv_step2;
				$formVervTakk = $row->verv_takk;
				$formGiveStep1 = $row->give_step1;
				$formGiveTakk = $row->give_takk;
				$formImage = $row->image;

			} else {
				pushError("Couldn't find the requested $this_name");
			}
		}

	?>

	<div class="page-header">
		<h1>
			<?= $this_name ?>s
			<small>create and manage <?= $this_name ?>s</small>
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
						$thisDesc = "-";
						$thisLength = 45;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-xlarge" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Url:";
						$thisId = "Url";
						$thisVar = $formUrl;
						$thisDesc = "<a href='../?ca=$thisVar' target='_blank'>View the saved data live</a>";
						$thisLength = 45;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<input type="text" name="<?= strtolower($thisId) ?>" class="input-xlarge" id="input<?= $thisId ?>" value="<?= htmlspecialchars($thisVar, ENT_QUOTES) ?>" maxlength="<?= $thisLength ?>" />
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Start date:";
						$thisId = "Start";
						$thisVar = $formStart;
						$thisDesc = "(YYYY-MM-DD) The date from which the campaign will be active. Leave blank if you want to use today's date. The entire date will be valid for this campaign (starting from 00:00:00).";
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
						$thisDesc = "(YYYY-MM-DD) The date to which the campaign will be active. Leave blank if you want to use the start date + 3 months. The entire date will be valid for this discount (stopping just before midnight, at 23:59:59).";
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
						$thisLabel = "Short info:";
						$thisId = "short_info";
						$thisVar = $formShortInfo;
						$thisDesc = "-";
						$thisLength = null;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<textarea name="<?= strtolower($thisId) ?>" rows="7" class="span5" id="input<?= $thisId ?>"><?= htmlspecialchars($thisVar, ENT_QUOTES) ?></textarea>
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>



				<div class="control-group">
					<label class="control-label" for="inputImage">Image</label>
					<div class="controls">
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

						<p class="help-block">A cover image for this campaign.</p>
					</div>
				</div>



				<div class="control-group">
					<?php
						$thisLabel = "Verv step 1:";
						$thisId = "verv_step1";
						$thisVar = $formVervStep1;
						$thisDesc = "-";
						$thisLength = null;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<textarea name="<?= strtolower($thisId) ?>" rows="8" class="span5" id="input<?= $thisId ?>"><?= $thisVar ?></textarea>
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Verv step 2:";
						$thisId = "verv_step2";
						$thisVar = $formVervStep2;
						$thisDesc = "-";
						$thisLength = null;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<textarea name="<?= strtolower($thisId) ?>" rows="8" class="span5" id="input<?= $thisId ?>"><?= $thisVar ?></textarea>
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Verv takk:";
						$thisId = "verv_takk";
						$thisVar = $formVervTakk;
						$thisDesc = "-";
						$thisLength = null;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<textarea name="<?= strtolower($thisId) ?>" rows="8" class="span5" id="input<?= $thisId ?>"><?= $thisVar ?></textarea>
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Give step 1:";
						$thisId = "give_step1";
						$thisVar = $formGiveStep1;
						$thisDesc = "-";
						$thisLength = null;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<textarea name="<?= strtolower($thisId) ?>" rows="8" class="span5" id="input<?= $thisId ?>"><?= $thisVar ?></textarea>
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>

				<div class="control-group">
					<?php
						$thisLabel = "Give takk:";
						$thisId = "give_takk";
						$thisVar = $formGiveTakk;
						$thisDesc = "-";
						$thisLength = null;
					?>
					<label class="control-label" for="input<?= $thisId ?>"><?= $thisLabel ?></label>
					<div class="controls">
						<textarea name="<?= strtolower($thisId) ?>" rows="8" class="span5" id="input<?= $thisId ?>"><?= $thisVar ?></textarea>
						<p class="help-block"><?= $thisDesc ?></p>
					</div>
				</div>



				
				<div class="control-group">
					<div class="controls">
						<button type="submit" class="btn btn-primary">Save</button>

						<?php if ($this_id > 0 && 1 == 2) { ?>
						<a href="?del=<?= $this_id ?>" class="btn btn-mini btn-danger">Delete</a>
						<?php } ?>
					</div>
				</div>

			</form>

		</div>



		<div class="span4 offset1">

			<a class="btn btn-success" href="?"><i class="icon-plus-sign icon-white"></i> Add new <?= $this_name ?></a>

			<hr />

			<h4>Active <?= $this_name ?>s</h4>
			<?php
				$result = db2_getCampaignsActive();

				if (!is_null($result))
				{
					while ( $row = $result->fetch_object() )
					{
						echo "<a href='?id=" . $row->id . "'>" . $row->title . "</a><br />";
					}
				}
				else
				{
					echo "<p>No active $this_name found</p>";
				}
			?>

			<hr />

			<div style="opacity:0.3;">
				<h4>Inactive <?= $this_name ?>s</h4>
				<?php
					$result = db2_getCampaignsInactive();

					if (!is_null($result))
					{
						while ( $row = $result->fetch_object() )
						{
							echo "<a href='?id=" . $row->id . "'>" . $row->title . "</a><br />";
						}
					}
					else
					{
						echo "<p>No inactive $this_name found</p>";
					}
				?>
			</div>

			<hr />

			<h4>Help</h4>
			<p>
				<strong>Short info</strong> is the BLUE area on this picture. The FIRST line of this text will
				be the heading. IF any image is selected (just under "Short info") that image will be shown
				between the heading and the text.
			</p>
			<p>
				<strong>Verv step 1</strong> is the RED area. The first in the text in the box to the left will be
				the heading automatically.
			</p>
			<a href="../images/verv_2-page1.jpg" target="_blank"><img src="../images/verv_2-page1.jpg" alt="" style="width:298px; border:1px solid gray;" /></a>
			
			<p>
				<strong>Verv step 2</strong> is the RED area. The first in the text in the box to the left will be
				the heading automatically.
			</p>
			<a href="../images/verv_2-page2.jpg" target="_blank"><img src="../images/verv_2-page2.jpg" alt="" style="width:300px;" /></a>

			<p>
				<strong>Verv takk</strong> is the RED area. The first in the text in the box to the left will be
				the heading automatically.
			</p>
			<a href="../images/verv_2-page3.jpg" target="_blank"><img src="../images/verv_2-page3.jpg" alt="" style="width:300px;" /></a>

			<p>
				<strong>Give step 1</strong> is the RED area. The first in the text in the box to the left will be
				the heading automatically.
			</p>
			<a href="../images/verv_2-page4.jpg" target="_blank"><img src="../images/verv_2-page4.jpg" alt="" style="width:300px;" /></a>

			<p>
				<strong>Give takk</strong> is the RED area. The first in the text in the box to the left will be
				the heading automatically.
			</p>
			<a href="../images/verv_2-page5.jpg" target="_blank"><img src="../images/verv_2-page5.jpg" alt="" style="width:300px;" /></a>


		</div>
	</div>


<?php require('_footer.php'); ?>