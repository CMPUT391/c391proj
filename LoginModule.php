<?php
include("PHPconnectionDB.php");
?>
<html>
    <body>
       <?php

	if (empty($_POST['username']) || empty($_POST['password'])) {
		echo "Missing some fields, please retry.";
		?>
		<a href ='LoginModule.html'>
			<button>Retry</button>
		</a>
	<?php
	}
	else{
		if (isset ($_POST['validate'])){ 
			//get the input
			$username=$_POST['username'];
			$password=$_POST['password'];

			ini_set('display_errors', 1);
			error_reporting(E_ALL);
			    
			//establish connection
			$conn=connect();
			if (!$conn) {
		    		$e = oci_error();
		    		trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
			}
	 	
			//TEST 1
			$sql = "SELECT * FROM users WHERE users.user_name = '$_POST[username]' and users.password = '$_POST[password]'";
			$stid = oci_parse($conn, $sql);
			$res = oci_execute($stid);

			//if error, retrieve the error using the oci_error() function & output an error
			if (!$res) {
				$err = oci_error($stid);
				echo htmlentities($err['message']);

			} else { 
				
				if ($row = oci_fetch_array($stid, OCI_ASSOC)) {
					echo "Successful Login";
					# get the type of user
					$status = NULL;
					$personid = NULL;
					$count = 0;
					
					foreach ($row as $item) {
						$count++;
						if (($item == 'a' || $item == 's' || $item == 'd') && ($count==3)) {
							$status = $item;
						} else if ($count == 4) {
							$personid = $item;
						}
					}
					echo '<br/>';
					
					session_start();
					$_SESSION['status'] = $status;
					$_SESSION['personid'] = $personid;
					echo '<br /><a href="MainPage.php">MainPage</a>';
					// Free the statement identifier when closing the connection
					oci_free_statement($stid);
					oci_close($conn);
					header('Location: MainPage.php');
				} else {
					echo "Unsuccessful Login";
					// Free the statement identifier when closing the connection
					oci_free_statement($stid);
					oci_close($conn);	
					?>
					<a href ='LoginModule.html'>
						<button>Retry</button>
					</a>
					<?php		
					// header('Location: LoginModule.html');
				}
			}
		}
	}
	?>
    </body>
</html>
