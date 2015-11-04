<html>
    <body>
	<?php
		session_start();
		//echo $_SESSION['status'];
		// $_SESSION['status'] is the data passed from Login Module which will contain the type of user 
	?>
	
	<a href ='LoginModule.html'>
		<button>Login</button>
	</a>
	
	<a href="sensorUserManagementPage.html">
		<button>Go to Sensor & User Management module</button>
	</a>

	<a href="subscribeModule.php">
		<button>Go to subscribe module</button>
	</a>

    </body>
</html>
