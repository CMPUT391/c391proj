<!-- Reference: http://www.iandevlin.com/blog/2012/09/html5/html5-media-and-data-uri -->
<html>
	<head> 
		<title> Search Results </title>
		
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap-theme.min.css">

	</head>

    <body>
    	<div class='container'>
    		<h1> Search Results </h1>

        <?php 
	   	include ("PHPconnectionDB.php");
		session_start();
		$personID = $_SESSION['personid'];

		//establish connection
		$conn=connect();

		if(isset($_POST['searchBtn'])){   
			// extract search specifications     	
			$keywords=trim($_POST['keywords']);            		
			$sensorType=$_POST['sensor_type'];
			$sensorLocation = $_POST['sensorLocation'];
			$startDate = $_POST['startDate'];
			$endDate = $_POST['endDate'];

			if (empty($startDate) OR empty($endDate)) {
				echo 'Time period must be specified!';
				exit;
			}

			echo 'PersonID:: '.$personID.'<br>';
			echo 'Keywords: '.$keywords.'<br>';
			echo 'Sensor Type: '.$sensorType.'<br>';
			echo 'Sensor Location: '.$sensorLocation.'<br>';
			echo 'StartDate: '.$startDate.'<br>';
			echo 'EndDate: '.$endDate.'<br>';

			// only sensor the current user is subscribed to
			$userSubscribedSensors = 't.person_id = '.$personID.' AND s.sensor_id = t.sensor_id';
			
			// images
			$subscribedSensorsImages = 'i.sensor_id = s.sensor_id';
			$imageDateRange = 'to_date(i.date_created, \'dd-mm-YYYY hh24:mi:ss\') >= to_date(to_date(\''.$startDate.'\', \'YYYY-mm-dd\'), \'dd-mm-YYYY hh24:mi:ss\')  AND to_date(i.date_created, \'dd-mm-YYYY hh24:mi:ss\') <= to_date(to_date(\''.$endDate.'\', \'YYYY-mm-dd\'), \'dd-mm-YYYY hh24:mi:ss\')';
			
			// sensors
			$sensorTypeSearch = 's.sensor_type = \''.$sensorType.'\'';
			$sensorLocationSearch = 's.location = \''.$sensorLocation.'\'';

			// Formulate sql query based on user's search paramaters
			// ---------- ********************* NEED TO DO "DISTINCT" after blobs are figured out *********** -------------- //
			$sql = 'SELECT i.* FROM images i, sensors s, subscriptions t WHERE '.$userSubscribedSensors.' AND '.$subscribedSensorsImages;

			if (!empty($sensorType))
				$sql = $sql." AND ".$sensorTypeSearch;

			if (!empty($sensorLocation))
				$sql = $sql." AND ".$sensorLocationSearch;

			if (!empty($keywords)) {
				$keywordsArray = array();
				$keywordsArrayTemp = explode(',', $keywords);

				$count = count($keywordsArrayTemp);
				for ($i = 0; $i < $count; $i++) {
					if (strlen(trim($keywordsArrayTemp[$i])) > 0) {
						array_push($keywordsArray, strtolower(trim($keywordsArrayTemp[$i])));
					}
				}
				$count = count($keywordsArray);

				if ($count > 0) {
					$sql = $sql." AND (";
					$sql = $sql.' lower(i.description) LIKE \'%'.$keywordsArray[0].'%\'';
					$sql = $sql.' OR lower(s.description) LIKE \'%'.$keywordsArray[0].'%\'';
					if (count($keywordsArray) == 1) {
						$sql = $sql.' )';
					}
					else {
						for ($i = 1; $i < $count; $i++) {
							$sql = $sql.' OR lower(i.description) LIKE \'%'.$keywordsArray[$i].'%\'';
							$sql = $sql.' OR lower(s.description) LIKE \'%'.$keywordsArray[$i].'%\'';
						}
						$sql = $sql.")";
					}
				}
			}

			$sql = $sql." AND ".$imageDateRange;
			

			// Get all IMAGES satisfying the search query
			// echo '<br><br><br>'.$sql.'<br><br><br>';
			$stid = oci_parse($conn, $sql);
			$res = oci_execute($stid);
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}

			echo '<h3> Images </h3>';
			echo '<div class="container">';

			$rows = array();
	        while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
		        array_push($rows,$row);
	        }


	        if (empty($rows)) {
				echo '<p> No matching images </p>';
			} else {
				// Display all image results
				foreach($rows as $row) {
				    $image = "data:image/jpg" . ";base64," . base64_encode($row['RECOREDED_DATA']->load());		
					$Img = imagecreatefromstring($row['RECOREDED_DATA']->load());
					$oldWidth = imagesx($Img);
					$oldHeight = imagesy($Img);

					$newWidth = 100;
					$newHeight = 100;
					$thumb = ImageCreateTrueColor($newWidth, $newHeight);
			ImageCopyResampled($thumb, $Img, 0, 0, 0, 0, $newWidth, $newHeight, $oldWidth, $oldHeight);

					ob_start();
					imagejpeg($thumb);	
					$contents = ob_get_contents();
					ob_end_clean();
					$thumbnail = "data:image/jpg" . ";base64," .base64_encode($contents);

					file_put_contents('/tmp/image.png', $row['RECOREDED_DATA']->load());

					echo '
					<div class="card col-md-3" style="max-width:30rem; padding-bottom: 45px;">
						<img class="card-img-top img-thumbnail" src=\''.$thumbnail.'\' alt="Card image cap" id=\''.$row["IMAGE_ID"].'\'>
						<table class="table">
							<tbody>
								<tr>
								<td>ImageID: '.$row["IMAGE_ID"].'</td>
								</tr>
								<tr>
								<td>SensorID: '.$row["SENSOR_ID"].'</td>
								</tr>
								<tr>
								<td>Description: '.$row["DESCRIPTION"].'</td>
								</tr>
								<tr>
								<td>Date Created: '.$row["DATE_CREATED"].'</td>
								</tr>
							</tbody>
						</table>
						<div class="card-block">
							<a download="image.jpg" href=\''.$image.'\' class="btn btn-primary">Download</a>
						</div>
					</div>
					';
				}
			}
		}

			echo '</div>';



			/* -------- AUDIO RECORDINGS --------- */
			$subscribedSensorsAudio = 'a.sensor_id = s.sensor_id';
			$audioDateRange = 'to_date(a.date_created, \'dd-mm-YYYY hh24:mi:ss\') >= to_date(to_date(\''.$startDate.'\', \'YYYY-mm-dd\'), \'dd-mm-YYYY hh24:mi:ss\')  AND to_date(a.date_created, \'dd-mm-YYYY hh24:mi:ss\') <= to_date(to_date(\''.$endDate.'\', \'YYYY-mm-dd\'), \'dd-mm-YYYY hh24:mi:ss\')';
			$sql = 'SELECT a.* FROM audio_recordings a, sensors s, subscriptions t WHERE '.$userSubscribedSensors.' AND '.$subscribedSensorsAudio;

			if (!empty($sensorType)) {
				$sql = $sql.' AND '.$sensorTypeSearch;
			}

			if (!empty($sensorLocation))
				$sql = $sql.' AND '.$sensorLocationSearch;


			if (!empty($keywords)) {
				$keywordsArray = array();
				$keywordsArrayTemp = explode(',', $keywords);

				$count = count($keywordsArrayTemp);
				for ($i = 0; $i < $count; $i++) {
					if (strlen(trim($keywordsArrayTemp[$i])) > 0) {
						array_push($keywordsArray, strtolower(trim($keywordsArrayTemp[$i])));
					}
				}
				$count = count($keywordsArray);

				if ($count > 0) {
					$sql = $sql." AND (";
					$sql = $sql.' lower(a.description) LIKE \'%'.$keywordsArray[0].'%\'';
					$sql = $sql.' OR lower(s.description) LIKE \'%'.$keywordsArray[0].'%\'';
					if (count($keywordsArray) == 1) {
						$sql = $sql.' )';
					}
					else {
						for ($i = 1; $i < $count; $i++) {
							$sql = $sql.' OR lower(a.description) LIKE \'%'.$keywordsArray[$i].'%\'';
							$sql = $sql.' OR lower(s.description) LIKE \'%'.$keywordsArray[$i].'%\'';
						}
						$sql = $sql.")";
					}
				}
			}


			$sql = $sql." AND ".$audioDateRange;

			// Get all AUDIO RECORDINGS satisfying the search query
			// echo '<br><br><br>'.$sql.'<br><br><br>';
			$stid = oci_parse($conn, $sql);
			$res = oci_execute($stid);
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}

			echo '<h3> Audio Recordings </h3>';
			echo '<div class="container">';
			$i = 0;
			// Display all audio recording results
			while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
				$i = $i + 1;
				$audio = "data:audio/wav" . ";base64," . base64_encode($row['RECORDED_DATA']->load());

				// audio icon from http://researchradio.org/wp-content/uploads/2015/06/audio-icon.png
				echo '
				<div class="card col-md-3" style="max-width:30rem; padding-bottom: 45px;">
					<img class="card-img-top img-thumbnail" src=\'audio-icon.png\'>
					<table class="table">
						<tbody>
							<tr>
							<td>RecordingID: '.$row["RECORDING_ID"].'</td>
							</tr>
							<tr>
							<td>SensorID: '.$row["SENSOR_ID"].'</td>
							</tr>
							<tr>
							<td>Date Created: '.$row["DATE_CREATED"].'</td>
							</tr>
							<tr>
			 			    <td>Length: '.$row["LENGTH"].'</td>
							</tr>
							<tr>
							<td>Description: '.$row["DESCRIPTION"].'</td>
							</tr>

						</tbody>
					</table>
					<div class="card-block">
						<a download="audio.wav" href=\''.$audio.'\' class="btn btn-primary">Download</a>
					</div>
				</div>
				';
			}

			if (!$i) {
				echo '<p> No matching audio recordings </p>';
			}
			echo '</div>';




			// /* ------ SCALAR DATA ----- */
			$subscribedSensorsScalar = 'c.sensor_id = s.sensor_id';
			$scalarDateRange = 'to_date(c.date_created, \'dd-mm-YYYY hh24:mi:ss\')  >= to_date(to_date(\''.$startDate.'\', \'YYYY-mm-dd\'), \'dd-mm-YYYY hh24:mi:ss\')  AND to_date(c.date_created, \'dd-mm-YYYY hh24:mi:ss\')  <= to_date(to_date(\''.$endDate.'\', \'YYYY-mm-dd\'), \'dd-mm-YYYY hh24:mi:ss\')';

			// Formulate sql query based on user's search paramaters
			$sql = 'SELECT c.id, c.sensor_id, to_char(c.date_created, \'dd/mm/YYYY hh24:mi:ss\') as date_created, c.value FROM scalar_data c, sensors s, subscriptions t WHERE '.$userSubscribedSensors.' AND '.$subscribedSensorsScalar;
			
			if (!empty($sensorType))
				$sql = $sql." AND ".$sensorTypeSearch;

			if (!empty($sensorLocation))
				$sql = $sql." AND ".$sensorLocationSearch;
			

			// match keywords from sensor description b/c scalar data doesn't have descriptions
			if (!empty($keywords)) {
				$keywordsArray = array();
				$keywordsArrayTemp = explode(',', $keywords);

				$count = count($keywordsArrayTemp);
				for ($i = 0; $i < $count; $i++) {
					if (strlen(trim($keywordsArrayTemp[$i])) > 0) {
						array_push($keywordsArray, strtolower(trim($keywordsArrayTemp[$i])));
					}
				}
				$count = count($keywordsArray);

				if ($count > 0) {
					$sql = $sql." AND (";
					$sql = $sql.' lower(s.description) LIKE \'%'.$keywordsArray[0].'%\'';
					if (count($keywordsArray) == 1) {
						$sql = $sql.' )';
					}
					else {
						for ($i = 1; $i < $count; $i++) {
							$sql = $sql.' OR lower(s.description) LIKE \'%'.$keywordsArray[$i].'%\'';
						}
						$sql = $sql.")";
					}
				}
			}

			$sql = $sql." AND ".$scalarDateRange;


			// // Get all SCALAR MEASUREMENTS/DATA satisfying the search query
			// echo '<br><br><br>'.$sql.'<br><br><br>';
			$stid = oci_parse($conn, $sql);
			$res = oci_execute($stid);
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}

			echo '<h3> Scalar Data </h3>';
			echo '<div class="container">';
			$i = 0;
			// Display all scalar data results
			while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
				$i = $i + 1;
				$csvString = $row["SENSOR_ID"].','.$row["DATE_CREATED"].','.$row["VALUE"];
				$scalarCSV = 'data:text/csv;base64,'.base64_encode($csvString);

				// CSV icon from https://cdn2.iconfinder.com/data/icons/document-file-types/512/csv-128.png
				echo '
				<div class="card col-md-3" style="max-width:30rem; padding-bottom: 45px;">
					<img class="card-img-top img-thumbnail" src="csv-icon.png">
					<table class="table">
						<tbody>
							<tr>
							    <td>ID: '.$row["ID"].'</td>
							</tr>
							<tr>
							    <td>SensorID: '.$row["SENSOR_ID"].'</td>
							</tr>
							<tr>
								<td> Value: '.$row["VALUE"].'</td>
							</tr>
							<tr>
							    <td>Date Created: '.$row["DATE_CREATED"].'</td>
							</tr>
						</tbody>
					</table>
					<div class="card-block">
						<a download="scalar.csv" href=\''.$scalarCSV.'\'  class="btn btn-primary">Download</a>
					</div>
				</div>
				';
			}

			if (!$i) {
				echo '<p> No matching scalar data </p>';
			}
			echo '</div>';


			// Free the statement identifier when closing the connection
			oci_free_statement($stid);

		// }

	    oci_close($conn);
	
		?>

		<br><br>

		<a href="searchModule1.php"><button> Go Back </button></a>
	
	</div>


    </body>
</html>
