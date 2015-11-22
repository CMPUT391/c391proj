<html>

<head>
<title>Search Module</title>
<link rel="stylesheet" href="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/css/bootstrap.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap-theme.min.css">
</head>

<body>
	<div class='container'>

	<?php
		session_start();
		if ($_SESSION['status'] != 'a' && $_SESSION['status'] != 'd' && $_SESSION['status'] != 's') { ?>
			Please Log in
			<a href = 'LoginModule.html'>
				<button>Login</button>
			</a>
	<?php 
		exit; }
		else if ($_SESSION['status'] != 's') {
			echo '<ul class="list-group">
					<li class="list-group-item list-group-item-danger">Access Denied. This page is only accessible by scientists.</li>
				 </ul> <br>';
			echo '<button class="btn btn-default" name="homeBtn"> <a href="MainPage.php"> Home </a></button>';
			return;
		}
		else {
	?>


	<button class='btn btn-default' name='homeBtn'> <a href="MainPage.php"> Home </a></button>

	<h1> Search Module </h1>
	<br>
	<legend> Search Conditions </legend>
	<form name='search' class="form-group" method='post' action='searchModule.php'>
			<label for="keywords">Keywords</label>
			<input type="text" class="form-control" name="keywords" placeholder="Enter keywords separated by a comma">
		<br>
			<label for="sensor_type">Sensor Type</label> 
			<select name='sensor_type' class='form-control'>
					<option disabled selected> --- Select a type --- </option>
					<option value="a">Audio (a) </option>
					<option value="i">Image (i) </option>
					<option value="s">Scalar (s) </option>
				</select> <br>
		<br>
			<label for="sensorLocation">Sensor Location</label>
			<input type="text" class="form-control" name="sensorLocation" placeholder="Sensor Location">
		<br>
		<div class="form-group has-warning">
		  <label class="control-label" for="startDate">Start Date</label>
		  <input type="date" class="form-control" id="startDate" name="startDate">
		</div>
		<div class="form-group has-warning">
		  <label class="control-label" for="endDate">End Date</label>
		  <input type="date" class="form-control" name="endDate" id='endDate'>
		</div>
	  <button type="submit" class="btn btn-primary" id='searchBtn' name="searchBtn">Submit</button>

	</form>

	<?php } ?>

</body>

</html>