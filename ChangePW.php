<html>
    <body>
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
	} else if (empty($_POST['newpassword1']) || empty($_POST['newpassword2'])) {
		echo "Missing some fields, please retry.";
		?>
		<a href ='ChangePW.html'>
			<button>Retry</button>
		</a>
		<?php
		return;
	} else {
		if (isset ($_POST['validate'])){	

			$username = $_SESSION['username'];
			$personid = $_SESSION['personid'];
			$oldpassword = $_POST['oldpassword'];
			$old_pw = $_SESSION['password'];
			if ($old_pw != $oldpassword) {
				echo "Old password is incorrect, please retry.";
				echo $oldpassword. " ". $old_pw;
				
				?>
				<a href ='ChangePW.html'>
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
				<a href ='ChangePW.html'>
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
			?>
		<a href ='MainPage.php'>
			<button>Go Back</button>
		</a>
		<?php
		return;
		}
	}
	?>
    </body>
</html>
