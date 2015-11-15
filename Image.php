<html>
    <body>    	
		<?php
		// http://www.w3schools.com/php/php_file_upload.asp
		// http://coyotelab.org/php/upload-csv-and-insert-into-database-using-phpmysql.html
		// http://www.php-mysql-tutorial.com/wikis/mysql-tutorials/uploading-files-to-mysql-database.aspx

		if(isset($_POST['submit']) && $_FILES['imageToUpload']['size'] > 0) {
			
			// name of the file
			$fileName = $_FILES['imageToUpload']['name'];
			// type of the file
			$fileType = $_FILES['imageToUpload']['type'];
			// size of the file
			$fileSize = $_FILES['imageToUpload']['size'];
			// temporary filename of the file in which the uploaded file was stored on the server
			$tmpName = $_FILES['imageToUpload']['tmp_name'];
			// description from user input
			$description = $_POST['description'];

			// directory where images will be saved
			$target = "your directory";
			$target = $target . basename($fileName);

			// Check file size
			if ($fileSize > 640000) { // check to see if it exceeds 64kb which is the limit for a blob
			    echo "Sorry, your file is too large.";
			    return;
			}
			// Allow certain file formats
			if($fileType != "jpg") {
			    echo "Only JPG allowed.";
			    return;
			}

			// setup connection
			$conn = connect();
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
			$image_id = $res + 1; // create a new image id
			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

			// now add the new data to the images table
			$conn = connect();
			$date = date('Y-m-d H:i:s',time());;
			
			$sql = 'INSERT INTO images VALUES (\''.$image_id.'\', 'sensor id', to_date(\''.$date.'\', \'yy-mm-dd hh24:mi:ss\'), 'recorded data', \''.$fileName.'\', \''.$description.'\')';
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
				echo "Problem uploading the image.";
			}

		}	
			/*
			$target_dir = "uploads/"; // directory where the file is placed
			$target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);
			$description = $_POST['description'];
			$uploadOk = 1;
			$imageFileType = pathinfo($target_file,PATHINFO_EXTENSION);

			// Check if image file is a actual image or fake image
			if(isset($_POST["submit"])) {
			    $check = getimagesize($_FILES["fileToUpload"]["tmp_name"]);
			    if($check !== false) {
			        echo "File is an image - " . $check["mime"] . ".";
			        $uploadOk = 1;
			    } else {
			        echo "File is not an image.";
			        $uploadOk = 0;
			    }
			}
			// Check if file already exists
			if (file_exists($target_file)) {
			    echo "Sorry, file already exists.";
			    $uploadOk = 0;
			}			

			// Check if $uploadOk is set to 0 by an error
			if ($uploadOk == 0) {
			    echo "File was not uploaded.";
			// if everything is ok, try to upload file
			} else {
			    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
			        echo "The file ". basename( $_FILES["fileToUpload"]["name"]). " has been uploaded.";
			        $date = date('Y-m-d H:i:s',time());;
			        $sql = 'INSERT INTO images VALUES ('image id', 'sensor id', to_date(\''.$date.'\', \'yy-mm-dd hh24:mi:ss\'), 'recorded data', 'thumbnail', \''.$description.'\')';
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

			    } else {
			        echo "Sorry, there was an error uploading your file.";
			    }
			}*/
					
		?>
    </body>
</html>