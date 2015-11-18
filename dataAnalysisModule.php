<html>
	<head>
	<title>Subscription Module</title>
	<link rel="stylesheet" href="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/css/bootstrap.css">
	</head>
	<body>
		<?php
		include ("PHPconnectionDB.php");
		$conn=connect();
		$pid = 1;


		// select all subscribed sensors
		// from those sensors select all sensors that fall within the date range
		// do Avg/Min/Max calc from the remaning sensors 

		function getSubscribedSensors($conn,$person_id){

		}

		function getDailyStats($conn,$person_id,$week){

		}

		function getWeeklyStats($conn,$person_id,$week){

		}

		function getMonthlyStats($conn,$person_id,$month){

		}

		function getQuarterStats($conn,$person_id,$quarter){


		}


		?>
	</body>
</html>