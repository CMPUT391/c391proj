<?php
function generateSensorID($conn){
	echo 'SensorID sequence generated <br>';

	$sql = 'SELECT max(sensor_id) FROM sensors';

	//Prepare sql using conn and returns the statement identifier
	$stid = oci_parse($conn, $sql);
	//Execute a statement returned from oci_parse()
	$res=oci_execute($stid);
	//if error, retrieve the error using the oci_error() function & output an error message
	if (!$res) {
		$err = oci_error($stid); 
		echo htmlentities($err['message']);
	}

	$result = oci_fetch_array($stid, OCI_ASSOC);
	if (empty($result)) {
		$startingID = 1;
	} else {
		foreach ($result as $id)
			$startingID = $id;

	}

	$sql = 'CREATE SEQUENCE sensor_id START WITH '.$startingID.' INCREMENT BY 1';
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
}



function generatePersonID($conn){
	echo 'PersonID sequence generated <br>';

	$sql = 'SELECT max(person_id) FROM persons';

	//Prepare sql using conn and returns the statement identifier
	$stid = oci_parse($conn, $sql);
	//Execute a statement returned from oci_parse()
	$res=oci_execute($stid);
	//if error, retrieve the error using the oci_error() function & output an error message
	if (!$res) {
		$err = oci_error($stid); 
		echo htmlentities($err['message']);
	}

	$result = oci_fetch_array($stid, OCI_ASSOC);
	if (empty($result)) {
		$startingID = 1;
	} else {
		foreach ($result as $id)
			$startingID = $id;

	}

	$sql = 'CREATE SEQUENCE person_id START WITH '.$startingID.' INCREMENT BY 1';
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
}
?>
