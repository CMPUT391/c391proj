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
		<a href ='LogoutModule.php'>
			<button>Logout</button>
		</a> 

		<a href ='UpdateInfoPW.html'>
			<button>Update Info & Password</button>
		</a> 		
	
		<a href="sensorModule.php">
			<button>Go to Sensor module</button>
		</a>

		<a href="userModule.php">
			<button>Go to User module</button>
		</a>
		
		<a href ='help.php'>
			<button>Help Documentation</button>
		</a> 


		<?php
		} else if ($_SESSION['status'] == 's') {
			echo "Welcome Scientist";
		?>
		<a href ='LogoutModule.php'>
			<button>Logout</button>
		</a>

		<a href ='UpdateInfoPW.html'>
			<button>Update Info & Password</button>
		</a> 		
	
		<a href="subscribeModule.php">
			<button>Go to subscribe module</button>
		</a>

		<a href="searchModule1.php">
			<button>Go to Search module</button>
		</a>

		<a href="dataAnalysisModule.php">
			<button>Go to Data Analysis module</button>
		</a>

		<a href ='help.php'>
			<button>Help Documentation</button>
		</a> 

		<?php	
		} else {
			echo "Welcome Data Curator";
		?>
		<a href ='LogoutModule.php'>
			<button>Logout</button>
		</a>
	
		<a href ='UpdateInfoPW.html'>
			<button>Update Info & Password</button>
		</a> 		

		<a href="UploadModule.html">
			<button>Go to upload module</button>
		</a>

		<a href ='help.php'>
			<button>Help Documentation</button>
		</a> 

		<?php
		}
		?>
    </body>
</html>
