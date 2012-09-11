<!-- footer -->
<?php
// Close database (from _database.php)
// **************************************************************************** 

	$mysqli->close();



// END FILE
// ****************************************************************************
?>

	</div>

	<?php
		if (DEV_ENV) {
			printDebugger();
		}
	?>

</body>
</html>