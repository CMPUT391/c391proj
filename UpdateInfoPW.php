<html>
    <head>
        <title> Update Info & Password </title>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap-theme.min.css">
    </head>

    <body>
    	<div class='container'>
	<?php
	include("PHPconnectionDB.php");

	session_start();
	if ($_SESSION['status'] != 'a' && $_SESSION['status'] != 'd' && $_SESSION['status'] != 's') { ?>
		Login Expired. Please Log in
		<a href = 'LogoutModule.php'>
			<button>Login</button>
		</a>
		<?php
		return;
	}

	if (isset ($_POST['validate'])){	
		if (empty($_POST['newpassword1']) || empty($_POST['newpassword2'])) {
			echo "Missing some fields, please retry.";
			?>
			<a href ='UpdateInfoPW.html'>
				<button>Retry</button>
			</a>
			<?php
			return;
		} 

		$username = $_SESSION['username'];
		$personid = $_SESSION['personid'];
		$oldpassword = $_POST['oldpassword'];
		$old_pw = $_SESSION['password'];
		if ($old_pw != $oldpassword) {
			echo "Old password is incorrect, please retry.";
			echo $oldpassword. " ". $old_pw;
			
			?>
			<a href ='UpdateInfoPW.html'>
				<button>Retry</button>
			</a>
			<?php
			return;
		}						
		$password1=$_POST['newpassword1'];
		$password2=$_POST['newpassword2'];

		if ($password1 != $password2) {
			echo "Different passwords inputted, please retry.";
			?>
			<a href ='UpdateInfoPW.html'>
				<button>Retry</button>
			</a>
			<?php
			return;
		}

		// update on the sql table now
		$conn = connect();
		$sql = "UPDATE users SET password='$password1' WHERE user_name='$username' AND person_id=$personid";
		//echo $sql;
		$stid = oci_parse($conn, $sql);
		$res = oci_execute($stid);

		//if error, retrieve the error using the oci_error() function & output an error
		if (!$res) {
			$err = oci_error($stid);
			echo htmlentities($err['message']);
		} else {
			echo 'Updated Password <br>';
		}
		oci_free_statement($stid);
		oci_close($conn);

		echo '<a href ="MainPage.php">
				<button>Go Back</button>
			  </a>';

		return;
	}


	// check if email is already in the database
	function validateEmail($email, $conn) {
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

		// Free the statement identifier when closing the connection
		oci_free_statement($stid);
		
		return $results;
	}

	// UPDATE PERSON
	if(isset($_POST['updateInfoBtn'])){
		$personid = $_SESSION['personid'];

		$first_name=$_POST['first_name'];            		
		$last_name=$_POST['last_name'];
		$address = $_POST['address'];
		$email = $_POST['email'];
		$phone_number = $_POST['phone_number'];

		$conn = connect();

		if (!empty($email)) {
			$results = validateEmail($email, $conn);
			if (!empty($results)) {
				echo '<ul class="list-group">
						<li class="list-group-item list-group-item-danger">The email: '.$email.' already exists in the database. Enter a different email.</li>
					  </ul><br>';
				echo '<a href ="UpdateInfoPW.html">
						<button>Retry</button>
					</a>';
				oci_close($conn);
				exit;
			}
		}

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
		$sql = $sql.' WHERE person_id=\''.$personid.'\'';

		//echo $sql.'<br>';
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
		// Free the statement identifier when closing the connection
		oci_free_statement($stid);
		oci_close($conn);

		echo '<a href ="MainPage.php">
				<button>Go Back</button>
			  </a>';
		return;
	}
			
	?>

		</div>

    </body>
</html>
