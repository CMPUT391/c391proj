<html>
    <body>
	<?php
		session_start();
		// $_SESSION['status'] is the data passed from Login Module which will contain the type of user 
		// $_SESSION['personid'] is the person id of the user
		// END SESSION WHEN LOGOUT
		// echo $_SESSION['personid'];
		if ($_SESSION['status'] != 'a' && $_SESSION['status'] != 'd' && $_SESSION['status'] != 's') { ?>
			Please Log in
			<a href = 'LoginModule.html'>
				<button>Login</button>
			</a>
		<?php
		} else if ($_SESSION['status'] == 'a'){
			echo "Welcome Admin";
		?>
		<a href ='LoginModule.html'>
			<button>Logout</button>
			<?php session_destroy(); session_commit(); ?>
		</a> 
	
		<a href="sensorUserManagementPage.html">
			<button>Go to Sensor & User Management module</button>
		</a>

		<a href="subscribeModule.php">
			<button>Go to subscribe module</button>
		</a>

		<?php
		} else if ($_SESSION['status'] == 's') {
			echo "Welcome Scientist";
				?>
		<a href ='LoginModule.html'>
			<button>Logout</button>
			<?php session_destroy(); session_commit();?>
		</a>
	
		<a href="sensorUserManagementPage.html">
			<button>Go to Sensor & User Management module</button>
		</a>

		<a href="subscribeModule.php">
			<button>Go to subscribe module</button>
		</a>

		<?php	
		} else {
			echo "Welcome Data Curator";
		?>
		<a href ='LoginModule.html'>
			<button>Logout</button>
			<?php session_destroy(); session_commit();?>
		</a>
	
		<a href="sensorUserManagementPage.html">
			<button>Go to Sensor & User Management module</button>
		</a>

		<a href="subscribeModule.php">
			<button>Go to subscribe module</button>
		</a>

		<?php
		}
		?>
    </body>
</html>
