<html>
    <body>    	
		<?php
		include("PHPconnectionDB.php");
		// http://stackoverflow.com/questions/21332380/csv-upload-with-php-mysql
		
		if(isset($_POST['submit']) && $_FILES['batchToUpload']['size'] > 0) {
			// csv file in format sensor_id, date (dd/mm/yyyy time), value
			$csv_file = $_FILES['batchToUpload']['tmp_name'];
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
			$row = oci_fetch_array($stid, OCI_ASSOC);
			if (empty($row)) {
				// no id exists, start at 1
				$new_id = 1;
			} else {
				foreach ($row as $item) {
					$new_id = $item + 1; // add 1 to max
				}
			}
			oci_free_statement($stid);

			if (($handle = fopen($csv_file, "r")) !== FALSE) {
				while (($data = fgetcsv($handle, 1000, $separator)) !== FALSE) {
					list($sensor_id, $date, $value) = $data;
					$import_queries[] = 'INSERT INTO scalar_data VALUES (\''.$new_id.'\', \''.$sensor_id.'\', to_date(\''.$date.'\', \'dd-mm-yyyy hh24:mi:ss\'), \''.$value.'\')';
					$new_id++; // update for new unique id
				}
				fclose($handle);
			}

			// import queries now contains sql statements and we now have to execute them all (DONT KNOW IF WORKS)
			if (sizeof($import_queries)) {
				foreach($import_queries as &$queries) {
					// echo $queries;
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
			?>
			<a href ='UploadModule.html'>
				<button>Retry</button>
			</a>
			<?php
			return;
		}
		?>
		Successful Upload
		<a href ='UploadModule.html'>
			<button>Return</button>
		</a>
    </body>
</html>
