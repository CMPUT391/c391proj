<html>
    <body>    	
		<?php
		include("PHPconnectionDB.php");
		session_start();
		if ($_SESSION['status'] != 'd') {?>
			Not a valid data curator, Please log in again. 
			<a href = 'LogoutModule.php'>
				<button>Login</button>
			</a>
		<?php
			return;
		}
		if(isset($_POST['submit']) && $_FILES['audioToUpload']['size'] > 0) {
			// name of the file
			$fileName = $_FILES['audioToUpload']['name'];
			// type of the file
			$fileType = $_FILES['audioToUpload']['type'];
			// temporary filename of the file in which the uploaded file was stored on the server
			$tmpName = $_FILES['audioToUpload']['tmp_name'];
			// description from user input
			$description = $_POST['description'];
			// sensor id from user
			$sensor_id = $_POST['sensor_id'];
			// length of the audio from user input
			$length = $_POST['length'];

			// directory where images will be saved
			$target = "your directory";
			$target = $target . basename($fileName);

			// Allow certain file formats
			
			if($fileType != "audio/x-wav" && $fileType != 'audio/wav') {
			    echo "Only wav allowed.";
				?>
				<a href ='UploadModule.html'>
				<button>Retry</button>
				</a>
				<?php
			    return;
			}
			// Check if sensor_id is valid
			// setup connection
			$conn = connect();
			$sql = "SELECT * FROM sensors WHERE sensors.sensor_id = '$sensor_id'";
			$stid = oci_parse($conn, $sql);
			$res = oci_execute($stid);

			//if error, retrieve the error using the oci_error() function & output an error
			if (!$res) {
				$err = oci_error($stid);
				echo htmlentities($err['message']);
			}
			$row = oci_fetch_array($stid, OCI_ASSOC);
			if (empty($row)) {
				// no id exists
				echo "Not a valid sensor ID";
				oci_free_statement($stid);
				oci_close($conn);
				?>
				<a href ='UploadModule.html'>
				<button>Retry</button>
				</a>
				<?php
				return;
			}

			// setup connection
			$conn = connect();
			// get an unique image id
			$sql = 'SELECT MAX(recording_id) FROM audio_recordings';
			//Prepare sql using conn and returns the statement identifier
			$stid = oci_parse($conn, $sql);
			//Execute a statement returned from oci_parse()
			$res=oci_execute($stid);
			//if error, retrieve the error using the oci_error() function & output an error message
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}
			$row = oci_fetch_array($stid, OCI_ASSOC);
			if (empty($row)) {
				// no id exists, start at 1
				$recording_id = 1;
			} else {
				foreach ($row as $item) {
					$recording_id = $item + 1; // add 1 to max
				}
			}
			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

			// now add the new data to the images table
			$date = $_POST['createdate'];
			
			$lob = oci_new_descriptor($conn, OCI_D_LOB);
			$stmt = oci_parse($conn, "INSERT INTO audio_recordings(recording_id, sensor_id, date_created, length, description, recorded_data) VALUES ('$recording_id', '$sensor_id', to_date('$date', 'dd-mm-YYYY hh24:mi:ss'), '$length', '$description', empty_blob()) returning recorded_data into :recorded_data");
			
			oci_bind_by_name($stmt, ':recorded_data', $lob, -1, OCI_B_BLOB);
			oci_execute($stmt, OCI_NO_AUTO_COMMIT);

			if ($lob->savefile($tmpName)) {
				oci_commit($conn);
				echo "Upload Sucessful";
				$lob->free();
				oci_free_statement($stmt);
				oci_close($conn);
				?>
				<a href ='UploadModule.html'>
					<button>Return</button>
				</a>
				<?php
			} else {
				echo "Upload Failed";
				$lob->free();
				oci_free_statement($stmt);
				oci_close($conn);
				?>
				<a href ='UploadModule.html'>
					<button>Return</button>
				</a>
				<?php
			}

		} else {	

		?>
		Missing some fields.
		<a href ='UploadModule.html'>
			<button>Go Back</button>
		</a>
		<?php
		}
		?>
    </body>
</html>
