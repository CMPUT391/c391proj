<html>
    <body>
        <?php 
	   	include ("PHPconnectionDB.php");
	   	include ("generateID.php");

		//establish connection
		$conn=connect();

		// CREATE / ADD PERSON
		if(isset($_POST['addNewPersonBtn'])){        	
			$first_name=$_POST['first_name'];            		
			$last_name=$_POST['last_name'];
			$address = $_POST['address'];
			$email = $_POST['email'];
			$phone_number = $_POST['phone_number'];

			// generatePersonID($conn); - NOTE: Must drop sequence then initiate / create sequence once at beginning ex when populate db with admin user.

			//$sql = 'INSERT INTO sensors VALUES ('.(int)$personID.', \''.$location.'\', \''.$type.'\', \''.$description.'\')';
			$sql = 'INSERT INTO persons VALUES (person_id.nextval, \''.$first_name.'\', \''.$last_name.'\', \''.$address.'\', \''.$email.'\', \''.$phone_number.'\')';

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
				echo 'Row inserted <br/>';
				echo 'First Name: '.$first_name.'<br>Last Name: '.$last_name.'<br>Address: '.$address.'<br>Email: '.$email.'<br>Phone Number: '.$phone_number.'<br><br>';
			}

			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

		}


		// UPDATE PERSON
		if(isset($_POST['updatePersonBtn'])){
			$personID = $_POST['person_id'];        	
			$first_name=$_POST['first_name'];            		
			$last_name=$_POST['last_name'];
			$address = $_POST['address'];
			$email = $_POST['email'];
			$phone_number = $_POST['phone_number'];

			echo "Person ID: ";
			echo $personID;
			echo '<br>';

			// Check if username is in the database
			$sql = 'SELECT * FROM persons WHERE person_id ='.$personID.'';
			echo  'SELECT * FROM persons WHERE person_id ='.$personID.'<br>';
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
				echo 'personID: '.$personID.' does not exist in the database. <br/>';
				return;
			}

			$sql = 'UPDATE persons SET first_name =\''.$first_name.'\', last_name=\''.$last_name.'\', address=\''.$address.'\', email=\''.$email.'\', phone=\''.$phone_number.'\' WHERE person_id=\''.$personID.'\'';
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
			} else {
				echo 'Person updated! <br>';
			}

			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

		}


		// REMOVE PERSON
		if(isset($_POST['removePersonBtn'])){
			$personID = $_POST['person_id'];

			// Check if sensorID is in the database
			$sql = 'SELECT * FROM persons WHERE person_id='.$personID.'';
			echo 'SELECT * FROM persons WHERE person_id='.$personID.'<br>';

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
				echo 'PersonID : '.$personID.' does not exist in the database. <br/>';
				return;
			}

			// Check if person has any subscriptons & delete those rows
			$sql = 'SELECT * FROM subscriptions WHERE person_id='.$personID.'';
			echo 'SELECT * FROM subscriptions WHERE person_id='.$personID.'<br>';

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
			if (!empty($results)) {
				$sql = 'DELETE FROM subscriptions WHERE ( person_id = '.(int)$personID.')';

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
				echo 'Subscriptions for person #'.$personID.' deleted <br/>';
				}
			}


			// Check if person has any associated users & delete those rows
			$sql = 'SELECT * FROM users WHERE person_id='.$personID.'';
			echo 'SELECT * FROM users WHERE person_id='.$personID.'<br>';

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
			if (!empty($results)) {
				$sql = 'DELETE FROM users WHERE ( person_id = '.(int)$personID.')';

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
				echo 'Users associated with person #'.$personID.' deleted <br/>';
				}
			}


			$sql = 'DELETE FROM persons WHERE ( person_id = '.(int)$personID.')';

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
			echo 'Person deleted <br/>';
			}

			// Free the statement identifier when closing the connection
			oci_free_statement($stid);
		}


		// CREATE / ADD SENSOR
		if(isset($_POST['createSensorBtn'])){        	
			$location=$_POST['sensor_location'];            		
			$type=$_POST['sensor_type'];
			$description = $_POST['sensor_description'];

			//generateSensorID($conn); //- NOTE: Must drop sequence then initiate / create sequence once at beginning ex when populate db with admin user.

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
				echo 'Row inserted <br/>';
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
			echo 'Row deleted <br/>';
			}

			// Free the statement identifier when closing the connection
			oci_free_statement($stid);
		}


		// ADD / CREATE USER
		if(isset($_POST['createUserBtn'])){        	
			$username = $_POST['username'];
			$password = $_POST['password'];
			$role = $_POST['role'];
			$person_id = $_POST['person_id'];
			$date_registered = $_POST['date_registered'];

			// Checks
			// --- QUESTON : Can sensor type, location & description be empty/blank since it's not a key? --- //
			// Empty fields check
			if (empty($username)) {
				echo 'Username cannot be blank. A username must be selected.';
				return;
			}
			else if (empty($role)) {
				echo 'Role cannot be blank. A role must be selected.';
				return;
			}
			else if (empty($person_id)) {
				echo 'Person ID cannot be blank. A person ID must be entered.';
				return;
			}
			// Check if username is already in the database
			$sql = 'SELECT * FROM users WHERE user_name=\''.$username.'\'';
			echo 'SELECT * FROM users WHERE user_name=\''.$username.'\' <br>';

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
			if (!empty($results)) {
				echo 'Username: '.$username.' is already taken. <br/>';
				return;
			}


			$date = date('Y-m-d H:i:s',time());;
			echo '<br><br><br>';
			echo $date;
			echo '<br><br><br>';


			// http://stackoverflow.com/questions/25519309/inserting-date-into-oracle-database-from-php
			$delivDate = date('d-m-Y h:i:s', strtotime($_POST['date_registered']));    

			echo 'Thank You !<br/> The username is '.$username.'.<br/> The password is '.$password.'.<br/> The role is '.$role.'. <br/>The person_id is '.$person_id.'.<br/> The date registered is '.$date_registered.'. <br/>';
			$sql = 'INSERT INTO users VALUES (\''.$username.'\', \''.$password.'\', \''.$role.'\', '.(int)$person_id.', NULL)';
			
			//$sql = 'INSERT INTO users VALUES (\''.$username.'\', \''.$password.'\', \''.$role.'\', '.(int)$person_id.', to_date(\''.$date.'\', "yy-mm-dd hh24:mi:ss"))';

			//echo 'INSERT INTO users VALUES (\''.$username.'\', \''.$password.'\', \''.$role.'\', '.(int)$person_id.', to_date(\''.$delivDate.'\', "dd-mm-yy hh24:mi:ss"))';

			echo $sql;
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


		// REMOVE USER
		if(isset($_POST['removeUserBtn'])){
			$username = $_POST['username'];
			// Checks
			// --- QUESTON : Can sensor type, location & description be empty/blank since it's not a key? --- //
			// Empty fields check
			if (empty($username)) {
				echo 'Username cannot be blank. A username must be selected.';
				return;
			}

			// Check if username is in the database
			$sql = 'SELECT * FROM users WHERE user_name =\''.$username.'\'';
			echo  'SELECT * FROM users WHERE user_name =\''.$username.'\' <br>';

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
				echo 'username does not exist in the database. <br/>';
				return;
			}

			echo '<br/> The username is '.$username.' <br/><br/>';

			$sql = 'DELETE FROM users WHERE ( user_name =\''.$username.'\')';

			echo 'DELETE FROM users WHERE ( user_name =\''.$username.'\') <br>';

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
			echo 'Row deleted <br/>';
			}

			// Free the statement identifier when closing the connection
			oci_free_statement($stid);
		}


		// UPDATE USER
		if(isset($_POST['updateUserBtn'])){        	
			$username = $_POST['username'];
			$password = $_POST['password'];
			$role = $_POST['role'];


			// Checks
			// Empty fields check
			if (empty($username)) {
				echo 'Username cannot be blank. A username must be selected.';
				return;
			}

			// Check if username is in the database
			$sql = 'SELECT * FROM users WHERE user_name =\''.$username.'\'';
			echo  'SELECT * FROM users WHERE user_name =\''.$username.'\' <br>';

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
				echo 'username does not exist in the database. <br/>';
				return;
			}

			$sql = 'UPDATE users SET password =\''.$password.'\', role=\''.$role.'\' WHERE user_name=\''.$username.'\'';
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

			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

		}

	    oci_close($conn);
	
		?>

		<button><a href="sensorUserManagementPage.html"> Go Back </a></button>

    </body>
</html>