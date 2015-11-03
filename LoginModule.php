<?php
include("PHPconnectionDB.php");
?>
<html>
    <body>
       <?php

	if (empty($_POST['username']) || empty($_POST['password'])) {
		echo "Missing some fields, please retry.";
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
					$count = 0;
					
					foreach ($row as $item) {
						//echo " item is ";
						//echo $item;
						//echo " count is ";
						//echo $count;
						$count++;
						if (($item == 'a' || $item == 's' || $item == 'd') && ($count==3)) {
							$status = $item;
							//echo "status is ";
							//echo $status;
						}	
					}
					echo '<br/>';
					
					session_start();
					$_SESSION['status'] = $status;
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
					header('Location: LoginModule.html');
				}
			}
		}
	}
	?>
    </body>
</html>
