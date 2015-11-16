<html>
    <body>    	
		<?php
		// http://stackoverflow.com/questions/21332380/csv-upload-with-php-mysql
		if(isset($_POST['submit']) && $_FILES['batchToUpload']['size'] > 0) {
			// csv file in format sensor_id, date (dd/mm/yyyy time), value
			$csv_file = $_FILES['batchToUpload'];
			$separator = ',';

			// store information into the array import queries
			$import_queries = array();
			$conn = connect();
			$sql = 'SELECT MAX(id) FROM scalar_data';
			$stid = oci_parse($conn, $sql);
			$res = oci_execute($stid);
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}
			$new_id = $res + 1;
			oci_free_statement($stid);

			if (($handle = fopen($csv_file, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, $separator, )) !== FALSE) {
					list($sensor_id, $date, $value) = $data;
					$import_queries[] = 'INSERT INTO scalar_data VALUES (\''.$new_id.'\', \''.$sensor_id.'\', \''.$date.'\', \''.$value.'\')';
					$new_id++; // update for new unique id
				}
				fclose($handle);
			}

			// import queries now contains sql statements and we now have to execute them all (DONT KNOW IF WORKS)
			if (sizeof($import_queries)) {
				foreach($import_queries as &$queries) {
					$stid = oci_parse($conn, $queries);
					$res = oci_execute($stid);
					if (!$res) {
						$err = oci_error($stid); 
						echo htmlentities($err['message']);
					}
					oci_free_statement($stid);
				}
			}

			oci_close($conn);

		} else {
			echo "Nothing was submitted.";
			return;
		}
		?>
		<button><a href="UploadModule.html"> Go Back </a></button>
    </body>
</html>