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

			if (empty($first_name) && empty($last_name) && empty($address) && empty($email) && empty($phone_number)) {
				echo 'Nothing to update.';
				exit;
			}

			$sql = 'UPDATE persons SET ';
			if (!empty($first_name))
				$sql = $sql.'first_name =\''.$first_name.'\', ';
			if (!empty($last_name))
				$sql = $sql.'last_name=\''.$last_name.'\', ';
			if (!empty($email))
				$sql = $sql.'address=\''.$address.'\', ';
			if (!empty($email))
				$sql = $sql.'email=\''.$email.'\', ';
			if (!empty($phone_number))
				$sql = $sql.'phone=\''.$phone_number.'\', ';
			$sql = substr($sql, 0, -2);
			$sql = $sql.' WHERE person_id=\''.$personID.'\'';

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

		// ADD / CREATE USER
		if(isset($_POST['createUserBtn'])){        	
			$username = $_POST['username'];
			$password = $_POST['password'];
			$role = $_POST['role'];
			$person_id = $_POST['person_id'];
			$date_registered = date('Y-m-d H:i:s',time());

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

			// Check if a valid/existing person_id was entered
			$sql = 'SELECT * FROM persons WHERE person_id=\''.$person_id.'\'';
			echo 'SELECT * FROM persons WHERE person_id=\''.$person_id.'\' <br>';

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
				echo 'PersonID: '.$person_id.' is not in the database. Invalid. <br/>';
				return;
			}
			

			$date = date('Y-m-d H:i:s',time());;
			echo 'Username: '.$username.'<br/>Password: '.$password.'<br/>Role: '.$role.'<br/>PersonID: '.$person_id.'<br/>';
			$sql = 'INSERT INTO users VALUES (\''.$username.'\', \''.$password.'\', \''.$role.'\', '.(int)$person_id.', to_date(\''.$date.'\', \'yy-mm-dd hh24:mi:ss\'))';
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


		// REMOVE USER
		if(isset($_POST['removeUserBtn'])){
			$username = $_POST['username'];

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

			echo '<br/>Username: '.$username.' <br/><br/>';

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
			echo 'User deleted <br/>';
			}

			// Free the statement identifier when closing the connection
			oci_free_statement($stid);
		}


		// UPDATE USER
		if(isset($_POST['updateUserBtn'])){        	
			$username = $_POST['username'];
			$password = $_POST['password'];
			$role = $_POST['role'];

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

			if ((empty($password)) && (empty($role))) {
				echo 'Nothing to be changed. <br>';
				exit;
			} else if ((!empty($password)) && (!empty($role))) {
				$sql = 'UPDATE users SET password =\''.$password.'\', role=\''.$role.'\' WHERE user_name=\''.$username.'\'';
			} else {
				$sql = 'UPDATE users SET ';
				if (!empty($password)) {
					$sql = $sql.'password =\''.$password.'\' WHERE user_name = \''.$username.'\'';
				}
				else if (!empty($role)) {
					$sql = $sql.'role=\''.$role.'\' WHERE user_name=\''.$username.'\'';
				}
			}

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
				echo 'User updated! <br>';
			}

			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

		}

	    oci_close($conn);
	
		?>

		<button><a href="userModule.html"> Go Back </a></button>

    </body>
</html>