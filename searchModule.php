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
			// $user_name = 'genevaSci'; 	// change this later
			// echo 'Username: '.$user_name.'<br>';
			echo 'Keywords: '.$keywords.'<br>';
			echo 'Sensor Type: '.$sensorType.'<br>';
			echo 'Sensor Location: '.$sensorLocation.'<br>';
			echo 'StartDate: '.$startDate.'<br>';
			echo 'EndDate: '.$endDate.'<br>';
			//

			// // Find user's personID
			// echo "Finding user's personID: ";
			// $sql = 'SELECT * FROM users WHERE user_name = \''.$user_name.'\'';
			// echo $sql;
			// echo '<br>';
			// $stid = oci_parse($conn, $sql);
			// $res = oci_execute($stid);
			// if (!$res) {
			// 	$err = oci_error($stid); 
			// 	echo htmlentities($err['message']);
			// }
			// $row = oci_fetch_array($stid, OCI_ASSOC);
			// $personID = $row['PERSON_ID'];				// personID of the current user
			echo 'PersonID ';
			echo $personID;
			echo '<br><br>';

			// // Find all sensorID's the current user is subscribed to & put it in a view called subscribedSensors
			// $sql = 'CREATE VIEW subscribedSensors AS SELECT s.sensor FROM subscriptions s, users u WHERE u.user_name = \''.$user_name.'\' AND u.person_id = s.person_id';
			// echo $sql;
			// echo '<br>';
			// $stid = oci_parse($conn, $sql);
			// $res = oci_execute($stid);
			// if (!$res) {
			// 	$err = oci_error($stid); 
			// 	echo htmlentities($err['message']);
			// }


			// only sensor the current user is subscribed to
			$userSubscribedSensors = 't.person_id = '.$personID.' AND s.sensor_id = t.sensor_id';

			// to_date(\''.$date.'\', \'yy-mm-dd hh24:mi:ss\')

			// images
			$subscribedSensorsImages = 'i.sensor_id = s.sensor_id';// AND - NEED
			$imageDateRange = 'i.date_created BETWEEN to_date(\''.$startDate.'\', \'yy-mm-dd\') AND to_date(\''.$endDate.'\', \'yy-mm-dd\')';	// AND - MANDATORY

			// sensors
			$sensorTypeSearch = 's.sensor_type = \''.$sensorType.'\'';							// AND - IF SENSOR TYPE IS GIVEN
			$sensorLocationSearch = 's.location = \''.$sensorLocation.'\'';						// AND - IF SENSORY LOCATION IS GIVEN


			// //
			// echo 'userSubscribedSensors :';
			// echo $userSubscribedSensors;
			// echo '<br>imageDateRange: ';
			// echo $imageDateRange;
			// echo '<br>imageDescritionKeywords: ';
			// echo $imageDescriptionKeywords;
			// echo '<br>sensorTypeSearch :';
			// echo $sensorTypeSearch;
			// echo '<br>sensorLocationSearch: ';
			// echo $sensorLocationSearch;
			// echo '<br>';
			// //


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
				// var_dump($keywordsArrayTemp);
				// echo '<br><br><br>';
				$count = count($keywordsArrayTemp);
				for ($i = 0; $i < $count; $i++) {
					if (strlen(trim($keywordsArrayTemp[$i])) > 0) {
						array_push($keywordsArray, trim($keywordsArrayTemp[$i]));
					}
				}
				$count = count($keywordsArray);

				/*
				echo 'COUNT: ';
				echo $count;
				echo '<br><br>KEY WORDS : [';
				for ($i = 0; $i < $count; $i++) {
					echo $keywordsArray[$i];
					echo ', ';
				}
				echo ']<br><br>';
				*/


				if ($count > 0) {
					$sql = $sql." AND (";
					$sql = $sql.' i.description LIKE \'%'.$keywordsArray[0].'%\'';
					$sql = $sql.' OR s.description LIKE \'%'.$keywordsArray[0].'%\'';
					if (count($keywordsArray) == 1) {
						$sql = $sql.' )';
					}
					else {
						for ($i = 1; $i < $count; $i++) {
							$sql = $sql.' OR i.description LIKE \'%'.$keywordsArray[$i].'%\'';
							$sql = $sql.' OR s.description LIKE \'%'.$keywordsArray[$i].'%\'';
						}
						$sql = $sql.")";
					}
				}
			}

			$sql = $sql." AND ".$imageDateRange;			// MANDATORY - IMAGES
			

			// Get all IMAGES satisfying the search query
			echo '<br><br><br>'.$sql.'<br><br><br>';

			$stid = oci_parse($conn, $sql);
			$res = oci_execute($stid);
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}

			echo '<h3> Images </h3>';
			echo '<div class="container">';
			$i = 0;
			// Display all image results
			while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
				$i = $i + 1;
				// still need to do download full size image button action
				echo '
				<div class="card col-md-3" style="max-width:30rem; padding-bottom: 45px;">
					<img class="card-img-top img-thumbnail" src=\'.$row[
					recoreded_data"].\' alt="Card image cap">
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
						<a href="#" class="btn btn-primary">Download</a>
					</div>
				</div>
				';
			}

			if (!$i) {
				echo '<p> No matching images </p>';
			}

			echo '</div>';


			/* -------- AUDIO RECORDINGS --------- */
			$subscribedSensorsAudio = 'a.sensor_id = s.sensor_id';
			$audioDateRange = 'a.date_created BETWEEN to_date(\''.$startDate.'\', \'yy-mm-dd\') AND to_date(\''.$endDate.'\', \'yy-mm-dd\')';	// AND - MANDATORY
			$sql = 'SELECT a.* FROM audio_recordings a, sensors s, subscriptions t WHERE '.$userSubscribedSensors.' AND '.$subscribedSensorsAudio;

			if (!empty($sensorType)) {
				$sql = $sql.' AND '.$sensorTypeSearch;
			}

			if (!empty($sensorLocation))
				$sql = $sql.' AND '.$sensorLocationSearch;

			if (!empty($keywords)) {
				$keywordsArray = array();
				$keywordsArrayTemp = explode(',', $keywords);
				// var_dump($keywordsArrayTemp);
				// echo '<br><br><br>';
				$count = count($keywordsArrayTemp);
				for ($i = 0; $i < $count; $i++) {
					if (strlen(trim($keywordsArrayTemp[$i])) > 0) {
						array_push($keywordsArray, trim($keywordsArrayTemp[$i]));
					}
				}
				$count = count($keywordsArray);

				/*
				echo 'COUNT: ';
				echo $count;
				echo '<br><br>KEY WORDS : [';
				for ($i = 0; $i < $count; $i++) {
					echo $keywordsArray[$i];
					echo ', ';
				}
				echo ']<br><br>';
				*/


				if ($count > 0) {
					$sql = $sql." AND (";
					$sql = $sql.' a.description LIKE \'%'.$keywordsArray[0].'%\'';
					$sql = $sql.' OR s.description LIKE \'%'.$keywordsArray[0].'%\'';
					if (count($keywordsArray) == 1) {
						$sql = $sql.' )';
					}
					else {
						for ($i = 1; $i < $count; $i++) {
							$sql = $sql.' OR a.description LIKE \'%'.$keywordsArray[$i].'%\'';
							$sql = $sql.' OR s.description LIKE \'%'.$keywordsArray[$i].'%\'';
						}
						$sql = $sql.")";
					}
				}
			}


			$sql = $sql." AND ".$audioDateRange;			// MANDATORY - AUDIO

			// Get all AUDIO RECORDINGS satisfying the search query
			echo '<br><br><br>'.$sql.'<br><br><br>';
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
				// still need to do download audio recording button action
				echo '
				<div class="card col-md-3" style="max-width:30rem; padding-bottom: 45px;">
					<img class="card-img-top img-thumbnail" src=\'.$row[
					recoreded_data"].\' alt="Card image cap">
					<table class="table">
						<tbody>
							<tr>
							<td>ImageID: '.$row["RECORDING_ID"].'</td>
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
						<a href="#" class="btn btn-primary">Download</a>
					</div>
				</div>
				';
			}
				// echo '
				// <div class="card col-md-3" style="max-width:20rem;">
				// 	<table class="table">
				// 		<tbody>
				// 			<tr>
				// 			    <td>RecordingID: '.$row["RECORDING_ID"].'</td>
				// 			</tr>
				// 			<tr>
				// 			    <td>SensorID: '.$row["SENSOR_ID"].'</td>
				// 			</tr>
				// 			<tr>
				// 			    <td>Description: '.$row["DESCRIPTION"].'</td>
				// 			</tr>
				// 			<tr>
				// 			    <td>Length: '.$row["LENGTH"].'</td>
				// 			</tr>
				// 			<tr>
				// 			    <td>Date Created: '.$row["DATE_CREATED"]'</td>
				// 			</tr>
				// 		</tbody>
				// 	</table>
				// 	<div class="card-block">
				// 		<a href="#" class="btn btn-primary">Download</a>
				// 	</div>
				// </div>
				// ';
			// }

			if (!$i) {
				echo '<p> No matching audio recordings </p>';
			}
			echo '</div>';






			// /* ------ SCALAR DATA ----- */

			$subscribedSensorsScalar = 'c.sensor_id = s.sensor_id';// AND - NEED
			$scalarDateRange = 'c.date_created BETWEEN to_date(\''.$startDate.'\', \'yy-mm-dd\') AND to_date(\''.$endDate.'\', \'yy-mm-dd\')';	// AND - MANDATORY

			// Formulate sql query based on user's search paramaters
			$sql = 'SELECT c.* FROM scalar_data c, sensors s, subscriptions t WHERE '.$userSubscribedSensors.' AND '.$subscribedSensorsScalar;
			
			if (!empty($sensorType))
				$sql = $sql." AND ".$sensorTypeSearch;

			if (!empty($sensorLocation))
				$sql = $sql." AND ".$sensorLocationSearch;
			

			// match keywords from sensor description b/c scalar data doesn't have descriptions
			if (!empty($keywords)) {
				$keywordsArray = array();
				$keywordsArrayTemp = explode(',', $keywords);
				// var_dump($keywordsArrayTemp);
				// echo '<br><br><br>';
				$count = count($keywordsArrayTemp);
				for ($i = 0; $i < $count; $i++) {
					if (strlen(trim($keywordsArrayTemp[$i])) > 0) {
						array_push($keywordsArray, trim($keywordsArrayTemp[$i]));
					}
				}
				$count = count($keywordsArray);

				/*
				echo 'COUNT: ';
				echo $count;
				echo '<br><br>KEY WORDS : [';
				for ($i = 0; $i < $count; $i++) {
					echo $keywordsArray[$i];
					echo ', ';
				}
				echo ']<br><br>';
				*/


				if ($count > 0) {
					$sql = $sql." AND (";
					$sql = $sql.' s.description LIKE \'%'.$keywordsArray[0].'%\'';
					if (count($keywordsArray) == 1) {
						$sql = $sql.' )';
					}
					else {
						for ($i = 1; $i < $count; $i++) {
							$sql = $sql.' OR s.description LIKE \'%'.$keywordsArray[$i].'%\'';
						}
						$sql = $sql.")";
					}
				}
			}

			$sql = $sql." AND ".$scalarDateRange;			// MANDATORY - SCALAR DATA


			// // Get all SCALAR MEASUREMENTS/DATA satisfying the search query
			echo '<br><br><br>'.$sql.'<br><br><br>';
			$stid = oci_parse($conn, $sql);
			$res = oci_execute($stid);
			if (!$res) {
				$err = oci_error($stid); 
				echo htmlentities($err['message']);
			}

			echo '<h3> Scalar Data </h3>';
			echo '<div class="container">';
			$i = 0;
			// Display all image results
			while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
				$i = $i + 1;
				// still need to do download scalar data button action
				echo '
				<div class="card col-md-3" style="max-width:30rem; padding-bottom: 45px;">
					<img class="card-img-top img-thumbnail" src="pane4.jpg" alt="Card image cap">
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
						<a href="#" class="btn btn-primary">Download</a>
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

		}

	    oci_close($conn);
	
		?>

		<br><br>

		<button><a href="searchModule.html"> Go Back </a></button>
	
	</div>


    </body>
</html>
