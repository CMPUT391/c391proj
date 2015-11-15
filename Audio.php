<html>
    <body>    	
		<?php

		if(isset($_POST['submit']) && $_FILES['audioToUpload']['size'] > 0) {
			// name of the file
			$fileName = $_FILES['audioToUpload']['name'];
			// type of the file
			$fileType = $_FILES['audioToUpload']['type'];
			// temporary filename of the file in which the uploaded file was stored on the server
			$tmpName = $_FILES['audioToUpload']['tmp_name'];
			// description from user input
			$description = $_POST['description'];
			// length of the audio from user input
			$length = $_POST['length'];

			// directory where images will be saved
			$target = "your directory";
			$target = $target . basename($fileName);

			// Allow certain file formats
			if($fileType != "wav") {
			    echo "Only wav allowed.";
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
			$recording_id = $res + 1; // create a new image id
			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

			// now add the new data to the images table
			$conn = connect();
			$date = date('Y-m-d H:i:s',time());;
			
			$sql = 'INSERT INTO audio_recordings VALUES (\''.$recording_id.'\', 'sensor id', to_date(\''.$date.'\', \'yy-mm-dd hh24:mi:ss\'), \''.$length.'\', 'recorded data', \''.$description.'\')';
			$stid = oci_parse($conn, $sql);
			$res=oci_execute($stid);
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}
			oci_free_statement($stid);

			// now write image to server
			if (move_uploaded_file($tmpName, $target)) {
				echo "Successfully uploaded and stored in the directory.";
			} else {
				echo "Problem uploading the audio recording.";
			}

		}	

		?>
    </body>
</html>