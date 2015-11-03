<html>
    <body>
	<?php
		session_start();
		//echo $_SESSION['status'];
		// $_SESSION['status'] is the data passed from Login Module which will contain the type of user 
	?>
	<a href="sensorModule.php">
		<button>Go to Sensor module</button>
	</a>

	<a href="subscribeModule.php">
		<button>Go to subscribe module</button>
	</a>

    </body>
</html>
