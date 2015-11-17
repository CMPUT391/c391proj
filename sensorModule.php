<html>
    <body>
        <?php 
	   	include ("PHPconnectionDB.php");
	   	include ("generateID.php");

		//establish connection
		$conn=connect();

		// CREATE / ADD SENSOR
		if(isset($_POST['createSensorBtn'])){        	
			$location=$_POST['sensor_location'];            		
			$type=$_POST['sensor_type'];
			$description = $_POST['sensor_description'];

			$sql = 'INSERT INTO sensors VALUES ( sensor_id.nextval, \''.$location.'\', \''.$type.'\', \''.$description.'\')';

			echo $sql;
			echo '<br>';

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
				echo 'Sensor inserted <br/>';
				echo 'Sensor Location: '.$location.'.<br/> Sensor Type: '.$type.'.<br/>Sensor Description: '.$description.'. <br/><br/>';
			}

			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

		}




		// REMOVE SENSORS
		if(isset($_POST['removeSensorBtn'])){
			$sensorID = $_POST['sensor_id'];

			// Check if sensorID is in the database
			$sql = 'SELECT * FROM sensors WHERE sensor_id='.$sensorID.'';
			echo 'SELECT * FROM sensors WHERE sensor_id='.$sensorID.'<br>';

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
			if (empty($results)) {
				echo 'SensorID : '.$sensorID.' does not exist in the database. <br/>';
				return;
			}


			$sql = 'DELETE FROM sensors WHERE ( sensor_id = '.(int)$sensorID.')';

			echo $sql;
			echo '<br>';

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
			echo 'Sensor deleted <br/>';
			}

			// Free the statement identifier when closing the connection
			oci_free_statement($stid);
		}


	    oci_close($conn);
	
		?>

		<button><a href="sensorModule.html"> Go Back </a></button>

    </body>
</html>