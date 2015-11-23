<html>
	<head>
		<title>User Mangagement</title>
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

	<h1> User Management </h1>

	<br><br>

        <?php 
	   	include ("PHPconnectionDB.php");

		//establish connection
		$conn=connect();
		?>

		<h3 id='persons'> Persons </h3>
		<br>

		<?php
		// CREATE / ADD PERSON
		if(isset($_POST['addNewPersonBtn'])){        	
			$first_name=$_POST['first_name'];            		
			$last_name=$_POST['last_name'];
			$address = $_POST['address'];
			$email = $_POST['email'];
			$phone_number = $_POST['phone_number'];

			// check if email is already in the database
			$sql = 'SELECT * FROM persons WHERE email = \''.$email.'\'';
						//Prepare sql using conn and returns the statement identifier
			$stid = oci_parse($conn, $sql);
			//Execute a statement returned from oci_parse()
			$res = oci_execute($stid);
			//if error, retrieve the error using the oci_error() function & output an error message
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}

			$results = oci_fetch_array($stid, OCI_ASSOC);
			
			if (!empty($results)) {
				echo '<ul class="list-group">
						<li class="list-group-item list-group-item-danger">The email: '.$email.' already exists in the database.</li>
					  </ul><br>';
			}
			else {
				$sql = 'INSERT INTO persons VALUES (person_id.nextval, \''.$first_name.'\', \''.$last_name.'\', \''.$address.'\', \''.$email.'\', \''.$phone_number.'\')';
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
							<li class="list-group-item list-group-item-success">Person added! <br>
							First Name: '.$first_name.'<br>Last Name: '.$last_name.'<br>Address: '.$address.'<br>Email: '.$email.'<br>Phone Number: '.$phone_number.'</li>
						 </ul>';
				}
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

			$continueFlag = true;
			$exitFlag = false;
			// echo "Person ID: ".$personID.'<br>';
			if (empty($personID)) {
				echo '<ul class="list-group">
						<li class="list-group-item list-group-item-danger">PersonID must be entered to update a person!</li>
					  </ul><br>'; 
				$continueFlag = false;
				$exitFlag = true;
			}

			if($continueFlag) {
				// Check if username is in the database
				$sql = 'SELECT * FROM persons WHERE person_id ='.$personID.'';
				// echo  $sql.'<br>';
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
					echo '<ul class="list-group">
							<li class="list-group-item list-group-item-danger">PersonID: '.$personID.' does not exist in the database.</li>
						  </ul><br>';
					$continueFlag = false;
				}

				if (empty($first_name) && empty($last_name) && empty($address) && empty($email) && empty($phone_number)) {
					echo '<ul class="list-group">
							<li class="list-group-item list-group-item-info">Nothing to update!</li>
						  </ul>';
					$continueFlag = false;
				}

				$updateStatus = 'PersonID : '.(int)$personID.'<br>';
			}


			if ($continueFlag) {
				$sql = 'UPDATE persons SET ';
				if (!empty($first_name)) {
					$sql = $sql.'first_name =\''.$first_name.'\', ';
					$updateStatus = $updateStatus.'First Name : '.$first_name.'<br>';
				}
				if (!empty($last_name)) {
					$sql = $sql.'last_name=\''.$last_name.'\', ';
					$updateStatus = $updateStatus.'Last Name : '.$last_name.'<br>';
				}
				if (!empty($address)) {
					$sql = $sql.'address=\''.$address.'\', ';
					$updateStatus = $updateStatus.'Address : '.$address.'<br>';
				}
				if (!empty($email)) {
					$sql = $sql.'email=\''.$email.'\', ';
					$updateStatus = $updateStatus.'Email : '.$email.'<br>';
				}
				if (!empty($phone_number)) {
					$sql = $sql.'phone=\''.$phone_number.'\', ';
					$updateStatus = $updateStatus.'Phone Number : '.$phone_number;
				}
				$sql = substr($sql, 0, -2);
				$sql = $sql.' WHERE person_id=\''.$personID.'\'';

				// echo $sql.'<br>';
				//Prepare sql using conn and returns the statement identifier
				$stid = oci_parse($conn, $sql);
				//Execute a statement returned from oci_parse()
				$res=oci_execute($stid);
				//if error, retrieve the error using the oci_error() function & output an error message
				if (!$res) {
					$err = oci_error($stid); 
					echo htmlentities($err['message']);
				} else {
					echo '<ul class="list-group">
							<li class="list-group-item list-group-item-success">Person updated! <br>'.$updateStatus.'</li>
						  </ul>';
				}
			}

			if (!$exitFlag) {
				// Free the statement identifier when closing the connection
				oci_free_statement($stid);
			}

		}


		// REMOVE PERSON
		if(isset($_POST['removePersonBtn'])){
			$personID = $_POST['person_id'];

			$continueFlag = true;
			$exitFlag = false;
			// echo "Person ID: ".$personID.'<br>';
			if (empty($personID)) {
				echo '<ul class="list-group">
						<li class="list-group-item list-group-item-info">No PersonID was entered!</li>
					  </ul><br>'; 
				$continueFlag = false;
				$exitFlag = true;
			}
			else {
				// Check if sensorID is in the database
				$sql = 'SELECT * FROM persons WHERE person_id='.$personID.'';
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
				if (empty($results)) {
					echo '<ul class="list-group">
							<li class="list-group-item list-group-item-danger">PersonID : '.(int)$personID.' does not exist in the database.</li>
						  </ul>';
					$continueFlag = false;
				}

				if ($continueFlag) {
					// Check if person has any subscriptons & delete those rows
					$sql = 'SELECT * FROM subscriptions WHERE person_id='.$personID.'';
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
					if (!empty($results)) {
						$sql = 'DELETE FROM subscriptions WHERE ( person_id = '.(int)$personID.')';

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
						// else{
						// 	echo '<ul class="list-group">
						// 			<li class="list-group-item list-group-item-success">Subscriptions for person #'.$personID.' deleted. <br></li>
						// 		  </ul>';
						// }
					}


					// Check if person has any associated users & delete those rows
					$sql = 'SELECT * FROM users WHERE person_id='.$personID.'';
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
					if (!empty($results)) {
						$sql = 'DELETE FROM users WHERE ( person_id = '.(int)$personID.')';

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
						// else{
						// 	echo '<ul class="list-group">
						// 			<li class="list-group-item list-group-item-success">Users associated with person #'.$personID.' deleted.</li>
						// 		  </ul>';
						// }
					}


					// delete person
					$sql = 'DELETE FROM persons WHERE ( person_id = '.(int)$personID.')';

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
								<li class="list-group-item list-group-item-success">Person ID : '.(int)$personID.' deleted.</li>
							  </ul>';
					}
				}
				// Free the statement identifier when closing the connection
				oci_free_statement($stid);
			}
		}

	    function get_all_persons($conn){
	        $arr = array();
	        $sql = 'SELECT * FROM persons';
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
	   	$rows = get_all_persons($conn);
		?>

		<table class="table">
			<thead>
				<tr>
				<th>PersonID</th>
				<th>First Name</th>
				<th>Last Name</th>
				<th>Address</th>
				<th>Email</th>
				<th>Phone</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($rows as $row) { ?>
				<tr>
					<td> <?php echo $row["PERSON_ID"]; ?> </td>
					<td> <?php echo $row["FIRST_NAME"]; ?> </td>
					<td> <?php echo $row["LAST_NAME"]; ?> </td>
					<td> <?php echo $row['ADDRESS']; ?> </td>
					<td> <?php echo $row['EMAIL']; ?> </td>
					<td> <?php echo $row['PHONE']; ?> </td>
				</tr>
			<?php }; ?>
			</tbody>
		</table>
		<br><br>
		


		<legend> Add New Person </legend>
		<form name="addNewPerson"  class='form-group' method="post" action="userModule.php#persons">
			<label for="first_name">First Name</label>
			<input type='text' name='first_name' class="form-control" placeholder="First Name" maxlength='24'><br>

			<label for="last_name">Last Name</label>
			<input type='text' name='last_name' class="form-control" placeholder="Last Name" maxlength='24'><br>

			<label for="address">Address</label>
			<input type='text' name='address' class="form-control" placeholder="Address" maxlength='128'><br>

			<label for="email">Email</label>
			<input type='text' name='email' class="form-control" placeholder="Email" maxlength='128'><br>

			<label for="phone_number">Phone Number</label>
			<input type='text' name='phone_number' class="form-control" placeholder="Phone Number" minlength='10' maxlength='10'><br>
			
			<button type='submit' name='addNewPersonBtn' class='btn btn-default'>Add New Person</button>
		</form>

		<br><br>


		<legend>Update Person</legend>
		<form name="updatePerson" class='form-group' method="post" action="userModule.php#persons">
			<label for="person_id">PersonID</label>
			<input type='text' name='person_id' class="form-control" placeholder="PersonID"><br><br><br>

			<label for="first_name">First Name</label>
			<input type='text' name='first_name' class="form-control" placeholder="First Name"><br>

			<label for="last_name">Last Name</label>
			<input type='text' name='last_name' class="form-control" placeholder="Last Name"><br>

			<label for="address">Address</label>
			<input type='text' name='address' class="form-control" placeholder="Address"><br>

			<label for="email">Email</label>
			<input type='text' name='email' class="form-control" placeholder="Email"><br>

			<label for="phone_number">Phone Number</label>
			<input type='text' name='phone_number' class="form-control" placeholder="Phone Number"><br>

			<button type='submit' class='btn btn-default' name='updatePersonBtn'>Update Person</button>
		</form>

		<br><br>

		<legend>Remove Person</legend>	
		<form name="removePerson" method="post" action="userModule.php#persons">
			<label for="person_id">PersonID</label>
			<input type='text' name='person_id' class="form-control" placeholder="PersonID"><br>
			
			<button type='submit' class='btn btn-default' name='removePersonBtn'>Remove</button>
		</form>

		<br><br>

		<h2 id='users'> Users </h2>
		<br>

		
		<?php
		// ADD / CREATE USER
		if(isset($_POST['createUserBtn'])){        	
			$username = $_POST['username'];
			$password = $_POST['password'];
			$role = $_POST['role'];
			$person_id = $_POST['person_id'];
			$date_registered = date('d-m-Y H:i:s',time());

			$continueFlag = true;
			$exitFlag = false;

			if (empty($username)) {
				echo '<ul class="list-group">
						<li class="list-group-item list-group-item-info">Username cannot be blank. A username must be selected.</li>
					  </ul>';
				$continueFlag = false;
			}
			else if (empty($role)) {
				echo '<ul class="list-group">
						<li class="list-group-item list-group-item-info">Role cannot be blank. A role must be selected.</li>
					  </ul>';
				$continueFlag = false;
			}
			else if (empty($person_id)) {
				echo '<ul class="list-group">
						<li class="list-group-item list-group-item-info">Person ID cannot be blank. A person ID must be entered.</li>
					  </ul>';
				$continueFlag = false;
			}

			if ($continueFlag) {
				// Check if username is already in the database
				$sql = 'SELECT * FROM users WHERE user_name=\''.$username.'\'';
				// echo $sq;.'<br>';

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
					echo '<ul class="list-group">
							<li class="list-group-item list-group-item-danger">Username: '.$username.' is already taken.</li>
						  </ul>';
					$exitFlag = true;
				}
				else {
					// Check if a valid/existing person_id was entered
					$sql = 'SELECT * FROM persons WHERE person_id=\''.$person_id.'\'';
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
					if (empty($results)) {
						echo '<ul class="list-group">
								<li class="list-group-item list-group-item-danger">PersonID: '.(int)$person_id.' is not in the database. Invalid.</li>
							  </ul>';
						$exitFlag = true;
					}
					else {
						$sql = 'INSERT INTO users VALUES (\''.$username.'\', \''.$password.'\', \''.$role.'\', '.(int)$person_id.', to_date(\''.$date_registered.'\', \'dd-mm-yyyy hh24:mi:ss\'))';
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
						else {
							echo '<ul class="list-group">
									<li class="list-group-item list-group-item-success">User added! <br>
									Username: '.$username.'<br/>Password: '.$password.'<br/>Role: '.$role.'<br/>PersonID: '.$person_id.'<br/>
									</li>
								  </ul>';
						}
					}
				}

				// Free the statement identifier when closing the connection
				oci_free_statement($stid);
			}

		}


		// REMOVE USER
		if(isset($_POST['removeUserBtn'])){
			$username = $_POST['username'];

			if (empty($username)) {
				echo '<ul class="list-group">
						<li class="list-group-item list-group-item-info">Username cannot be blank. A username must be selected.</li>
					  </ul>';
			}
			else {

				// Check if username is in the database
				$sql = 'SELECT * FROM users WHERE user_name =\''.$username.'\'';
				// echo  $sql.'<br>';

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
					echo '<ul class="list-group">
							<li class="list-group-item list-group-item-danger">Username does not exist in the database.</li>
						  </ul>';
				}
				else {
					// delete user
					$sql = 'DELETE FROM users WHERE ( user_name =\''.$username.'\')';
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
								<li class="list-group-item list-group-item-success">User: '.$username.' deleted! <br>
								</li>
							  </ul>';
					}
				}
				// Free the statement identifier when closing the connection
				oci_free_statement($stid);
			}
		}


		// UPDATE USER
		if(isset($_POST['updateUserBtn'])){        	
			$username = $_POST['username'];
			$password = $_POST['password'];
			$role = $_POST['role'];

			if (empty($username)) {
				echo '<ul class="list-group">
						<li class="list-group-item list-group-item-info">Username cannot be blank. A username must be selected.</li>
					  </ul>';
			}
			else {
				// Check if username is in the database
				$sql = 'SELECT * FROM users WHERE user_name =\''.$username.'\'';
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
				if (empty($results)) {
					echo '<ul class="list-group">
							<li class="list-group-item list-group-item-danger">Username does not exist in the database.</li>
						  </ul>';
				}
				else {
					if ((empty($password)) && (empty($role))) {
						echo '<ul class="list-group">
								<li class="list-group-item list-group-item-info">Nothing to update!</li>
							  </ul>';
					}
					else {

						if ((!empty($password)) && (!empty($role))) {
							$sql = 'UPDATE users SET password =\''.$password.'\', role=\''.$role.'\' WHERE user_name=\''.$username.'\'';
							$updateStatus = $updateStatus.' Username :'.$username.'<br>Password: '.$password.'<br>Role: '.$role.'<br>';
						} else {
							$sql = 'UPDATE users SET ';
							if (!empty($password)) {
								$sql = $sql.'password =\''.$password.'\' WHERE user_name = \''.$username.'\'';
								$updateStatus = $updateStatus.' Username :'.$username.'<br>Password: '.$password.'<br>';
							}
							else if (!empty($role)) {
								$sql = $sql.'role=\''.$role.'\' WHERE user_name=\''.$username.'\'';
								$updateStatus = $updateStatus.' Username :'.$username.'<br>Role: '.$role.'<br>';
							}
						}

						// echo $sql.'<br>';
						//Prepare sql using conn and returns the statement identifier
						$stid = oci_parse($conn, $sql);
						//Execute a statement returned from oci_parse()
						$res=oci_execute($stid);
						//if error, retrieve the error using the oci_error() function & output an error message
						if (!$res) {
							$err = oci_error($stid); 
							echo htmlentities($err['message']);
						} else {
							echo '<ul class="list-group">
									<li class="list-group-item list-group-item-success">User updated! <br>'.$updateStatus.'</li>
								  </ul>';
						}
					}
				}
				// Free the statement identifier when closing the connection
				oci_free_statement($stid);
			}
		}

	    function get_all_users($conn){
	        $arr = array();
	        $sql = 'SELECT * FROM users';
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
	   	$rows = get_all_users($conn);
		?>
		<table class="table">
			<thead>
				<tr>
				<th>User Name</th>
				<th>Password</th>
				<th>Role</th>
				<th>PersonID</th>
				<th>Date Registered</th>
				</tr>
			</thead>
			<tbody>
			<?php foreach($rows as $row) { ?>
				<tr>
					<td> <?php echo $row["USER_NAME"]; ?> </td>
					<td> <?php echo $row["PASSWORD"]; ?> </td>
					<td> <?php echo $row['ROLE']; ?> </td>
					<td> <?php echo $row['PERSON_ID']; ?> </td>
					<td> <?php echo $row['DATE_REGISTERED']; ?> </td>
				</tr>
			<?php }; ?>
			</tbody>
		</table>
		<br><br>

		<legend>Create User Account</legend>
		<form name="createUser"class='form-group' method="post" action="userModule.php#users">
		<label for="username">Username</label>
		<input type='text' name='username' class="form-control" placeholder="Username"><br>

		<label for="password">Password</label>
		<input type='password' name='password' class="form-control" placeholder="Password"><br>

		<label for="role">Role</label> 
		<select name='role' class='form-control'>
				<option disabled selected> --- Select a role --- </option>
				<option value="a">Admin (a) </option>
				<option value="d">Data Curator (d) </option>
				<option value="s">Scientist (s) </option>
			</select> <br>

		<label for="person_id">PersonID</label>
		<input type='text' name='person_id' class="form-control" placeholder="PersonID"><br>
		
		<button type='submit' class='btn btn-default' name='createUserBtn'>Create User</button>

		</form>

		<br><br>

		<legend>Remove User Account</legend>	
		<form name="removeUser" method="post" action="userModule.php#users">
			<label for="username">Username</label>
			<input type='text' name='username' class="form-control" placeholder="Username"><br>
			
			<button type='submit' class='btn btn-default' name='removeUserBtn'>Remove</button>
		</form>

		<br><br>

		<legend>Update User Account</legend>	
		<form name="updateUser" method="post" action="userModule.php#users">
			<label for="username">Username</label>
			<input type='text' name='username' class="form-control" placeholder="Username"><br>

			<label for="password">Password</label>
			<input type='password' name='password' class="form-control" placeholder="Password"><br>

			<label for="role">Role</label> 
			<select name='role' class='form-control'>
					<option disabled selected> --- Select a role --- </option>
					<option value="a">Admin (a) </option>
					<option value="d">Data Curator (d) </option>
					<option value="s">Scientist (s) </option>
				</select> <br>

		<!-- 	<label for="person_id">PersonID</label>
			<input type='text' name='person_id' class="form-control" placeholder="PersonID"><br> -->
			
			<button type='submit' class='btn btn-default' name='updateUserBtn'>Update User</button>
		</form>

	</div>

	<?php } ?>
    </body>
</html>
