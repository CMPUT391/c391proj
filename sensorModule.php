<html>

<head>
<title>Sensor Mangagement</title>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap-theme.min.css">
</head>

<body>
	<div class='container'>

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
		exit; }
		else if ($_SESSION['status'] != 'a') {
			echo '<ul class="list-group">
					<li class="list-group-item list-group-item-danger">Access Denied. This page is only accessible by admins.</li>
				 </ul> <br>';
			echo '<a href="MainPage.php"><button class="btn btn-default" name="homeBtn"> Home </button></a>';
		}
		else {
	?>


	<a href="MainPage.php"><button class='btn btn-default' name='homeBtn'> Home </button></a>

	<h1> Sensors Module </h1>
	<br>
	<br>
		<?php 
		include ("PHPconnectionDB.php");

		//establish connection
		$conn=connect();

		// Check if sensorID exists in the database
		function validateSensor($sensor_id, $conn) {
			$sql = 'SELECT * FROM sensors WHERE sensor_id='.$sensor_id;
			// echo $sql.'<br>';
			//Prepare sql using conn and returns the statement identifier
			$stid = oci_parse($conn, $sql);
			//Execute a statement returned from oci_parse()
			$res=oci_execute($stid);
			//if error, retrieve the error using the oci_error() function & output an error message
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}

			$results = oci_fetch_array($stid, OCI_ASSOC);
			
			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

			return $results;
		}

		// remove all rows in subscriptions with this sensor id if necessary
		function removeSubscriptionsFromSensorID($sensor_id, $conn) {
			$sql = 'DELETE FROM subscriptions WHERE sensor_id = '.(int)$sensor_id;
			// echo $sql.'<br>';
			//Prepare sql using conn and returns the statement identifier
			$stid = oci_parse($conn, $sql);
			//Execute a statement returned from oci_parse()
			$res=oci_execute($stid);
			//if error, retrieve the error using the oci_error() function & output an error message
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}
			// Free the statement identifier when closing the connection
			oci_free_statement($stid);
		}

		// delete sensor
		function removeSensor($sensor_id, $conn) {
			$sql = 'DELETE FROM sensors WHERE ( sensor_id = '.(int)$sensor_id.')';
			// echo $sql.'<br>';
			//Prepare sql using conn and returns the statement identifier
			$stid = oci_parse($conn, $sql);
			//Execute a statement returned from oci_parse()
			$res=oci_execute($stid);
			//if error, retrieve the error using the oci_error() function & output an error message
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}
			else{
				echo '<ul class="list-group">
					  	<li class="list-group-item list-group-item-success">Sensor deleted!</li>
					  </ul>';
			}
			// Free the statement identifier when closing the connection
			oci_free_statement($stid);
		}

		// CREATE / ADD SENSOR
		if(isset($_POST['createSensorBtn'])){        	
			$location=$_POST['sensor_location'];            		
			$type=$_POST['sensor_type'];
			$description = $_POST['sensor_description'];

			$sql = 'INSERT INTO sensors VALUES ( sensor_id.nextval, \''.$location.'\', \''.$type.'\', \''.$description.'\')';
			// echo $sql.'<br>';
			//Prepare sql using conn and returns the statement identifier
			$stid = oci_parse($conn, $sql);
			//Execute a statement returned from oci_parse()
			$res = oci_execute($stid);
			//if error, retrieve the error using the oci_error() function & output an error message
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}
			else{
				echo '<ul class="list-group">
						<li class="list-group-item list-group-item-success"> <strong> Sensor Inserted! </strong><br>
						Sensor Location: '.$location.'.<br/> Sensor Type: '.$type.'.<br/>Sensor Description: '.$description.'<br> </li>
					 </ul> <br>';
			}

			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

		}

		// REMOVE SENSORS
		if(isset($_POST['removeSensorBtn'])){
			$sensorID = $_POST['sensor_id'];
			if (empty($sensorID)) {
				echo '<ul class="list-group">
					 	<li class="list-group-item list-group-item-danger">No sensor id was entered.</li>
					 </ul>';	
			} 
			else {
				// Check if sensorID is in the database
				$results = validateSensor($sensorID, $conn);
				if (empty($results)) {
					echo '<ul class="list-group">
						 	<li class="list-group-item list-group-item-danger">SensorID : '.$sensorID.' does not exist in the database. </li>
						 </ul>';
				} 

				else {
					removeSubscriptionsFromSensorID($sensorID, $conn);
					removeSensor($sensorID, $conn);
				}
			}
		}

	    function get_all_sensors($conn){
	        $arr = array();
	        $sql = 'SELECT * FROM sensors s';
	        $stid = oci_parse($conn,$sql);
	        $res = oci_execute($stid);
	        
	        if (!$res) {
		        $err = oci_error($stid);
		        echo htmlentities($err['message']);
            }
	        
	        while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
		        array_push($arr,$row);
	        }
	        oci_free_statement($stid);
	        return $arr;
	    }
	   	$rows = get_all_sensors($conn);
	   	oci_close($conn);
		?>
	<table class="table">
		<thead>
			<tr>
			<th>SensorID</th>
			<th>Location</th>
			<th>Sensor Type</th>
			<th>Description</th>
			</tr>
		</thead>
		<tbody>
		<?php foreach($rows as $row) { ?>
			<tr>
				<td> <?php echo $row["SENSOR_ID"]; ?> </td>
				<td> <?php echo $row["LOCATION"]; ?> </td>
				<td> <?php echo $row["SENSOR_TYPE"]; ?> </td>
				<td> <?php echo $row['DESCRIPTION']; ?> </td>
			</tr>
		<?php }; ?>
		</tbody>
	</table>

		

	<br>
	<br>
	<legend> Create Sensor </legend>
		<form name="createSensor" class="form-group" method="post" action="sensorModule.php">
			<label for="sensorLocation">Sensor Location</label>
			<input type='text' name='sensor_location' class="form-control" placeholder="Sensor Location" maxlength="64"><br>
			<label for="sensorType">Sensor Type</label> 
			<select name='sensor_type' class='form-control'>
					<option disabled selected> --- Select a type --- </option>
					<option value="a">Audio (a) </option>
					<option value="i">Image (i) </option>
					<option value="s">Scalar (s) </option>
				</select> <br>
			<label for="sensor_description">Description</label><br>
			<input type='text' name='sensor_description' class='form-control' placeholder='Sensor Description' maxlength="128"> <br>
			<button type='submit' name='createSensorBtn' class="btn btn-default">Add Sensor</button>
		</form>

		<br><br><br><br><br><br>

		<legend> Remove Sensor </legend>
		<form name="removeSensor" class="form-group" method="post" action="sensorModule.php">
				Sensor ID : <input type='number' name='sensor_id' class="form-control"> <br>
				<button type='submit' name='removeSensorBtn' class="btn btn-default">Remove</button>
		</form>
	</div>

	<?php } ?>

</body>

</html>
