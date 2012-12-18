<!-- footer -->
<?php

	// Close database (from /inc/_database.php)
	$mysqli->close();

?>

	</div>

	<?php
		if (DEV_ENV) {
			printDebugger();
		}
	?>

</body>
</html>