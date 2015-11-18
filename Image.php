<html>
    <body>    	
		<?php
		include("PHPconnectionDB.php");
		// http://www.w3schools.com/php/php_file_upload.asp
		// http://coyotelab.org/php/upload-csv-and-insert-into-database-using-phpmysql.html
		// http://www.php-mysql-tutorial.com/wikis/mysql-tutorials/uploading-files-to-mysql-database.aspx
		// http://php.net/manual/en/function.oci-new-descriptor.php
		
		if(isset($_POST['submit']) && $_FILES['imageToUpload']['size'] > 0) {
			
			// name of the file
			$fileName = $_FILES['imageToUpload']['name'];
			// type of the file
			$fileType = $_FILES['imageToUpload']['type'];
			// size of the file
			$fileSize = $_FILES['imageToUpload']['size'];
			// temporary filename of the file in which the uploaded file was stored on the server
			$tmpName = $_FILES['imageToUpload']['tmp_name'];
			// sensor id from user
			$sensor_id = $_POST['sensor_id'];
			// description from user input
			$description = $_POST['description'];

			// directory where images will be saved
			$target = "your directory";
			$target = $target . basename($fileName);

			// Check file size
			if ($fileSize > 640000) { // check to see if it exceeds 64kb which is the limit for a blob
			    echo "Sorry, your file is too large.";
				?>
				<a href ='UploadModule.html'>
					<button>Retry</button>
				</a>
				<?php
			    return;
			}
			// Allow certain file formats
			if($fileType != "image/jpg" && $fileType != "image/jpeg") {
			    echo "Only JPG allowed.";
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
			$imageData = file_get_contents($tmpName);			

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
			oci_free_statement($stid);
			// get an unique image id
			$sql = 'SELECT MAX(image_id) FROM images';
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
				$image_id = 1;
			} else {
				foreach ($row as $item) {
					$image_id = $item + 1; // add 1 to max
				}
			}
			
			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

			// now add the new data to the images table
			$date = date('Y-m-d H:i:s',time());;

			$lob = oci_new_descriptor($conn, OCI_D_LOB);
			$stmt = oci_parse($conn, "INSERT INTO images(image_id, sensor_id, date_created, description, thumbnail, recoreded_data) VALUES ('$image_id', '$sensor_id', NULL, '$description', empty_blob(), empty_blob()) returning thumbnail, recoreded_data into :thumbnail, :recoreded_data");
			
			oci_bind_by_name($stmt, ':thumbnail', $lob, -1, OCI_B_BLOB);
			oci_bind_by_name($stmt, ':recoreded_data', $lob, -1, OCI_B_BLOB);
			oci_execute($stmt, OCI_NO_AUTO_COMMIT);

			if ($lob->savefile($tmpName)) {
				oci_commit($conn);
				echo "successful thumbnail and recorded data upload";
				$lob->free();
				oci_free_statement($stmt);
				oci_close($conn);
				?>
				<a href ='UploadModule.html'>
					<button>Return</button>
				</a>
				<?php
			} else {
				echo "failed thumbnail and recorded data upload";
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
