<html>
	<head>
	<title>Subscribe Module</title>
	<link rel="stylesheet" href="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/css/bootstrap.css">
	</head>
    <body>
    	<div class='container'>
    		<h1> Data Analysis Module </h1>
        <?php

        
	    function add_subscription($conn,$sensor_id,$person_id){
	        #check if value already exists or not
	        #echo "adding subscription sid:$sensor_id pid:$person_id<br>";
	        $sql = "SELECT * FROM subscriptions 
	        WHERE sensor_id = $sensor_id AND person_id = $person_id";
	        $stid = oci_parse($conn,$sql);
	        $res = oci_execute($stid);
	        $row = oci_fetch_array($stid,OCI_ASSOC);
	        if(empty($row)){
                #echo "added subscription sid:$sensor_id pid:$person_id<br>";
                $sql = "INSERT INTO subscriptions (sensor_id, person_id) 
                        VALUES ($sensor_id,$person_id)";
                $stid = oci_parse($conn,$sql);
                $res = oci_execute($stid);
            } else {
                #echo " value aleady exists <br>";
            }
            
            if (!$res) {
		        $err = oci_error($stid);
		        echo htmlentities($err['message']);
            } else { 
                #echo 'Rows Extracted <br/>'; 
            }
            oci_free_statement($stid);
	    }
	    
	    function remove_subscription($conn,$sensor_id,$person_id){
	        #check if value already exists or not
	        #echo "removing subscription sid:$sensor_id pid:$person_id<br>";
	        $sql = "SELECT * FROM subscriptions 
	        WHERE sensor_id = $sensor_id AND person_id = $person_id";
	        $stid = oci_parse($conn,$sql);
	        $res = oci_execute($stid);
	        $row = oci_fetch_array($stid,OCI_ASSOC);
	        if(empty($row)){
	            #echo "value does not exist <br>";
            } else {
                #echo "removed subscription sid:$sensor_id pid:$person_id<br>";
                $sql = "DELETE FROM subscriptions
                WHERE sensor_id = $sensor_id AND person_id = $person_id";
                $stid = oci_parse($conn,$sql);
                $res = oci_execute($stid);
            }
            
            if (!$res) {
		        $err = oci_error($stid);
		        echo htmlentities($err['message']);
            } else { 
                #echo 'Rows Extracted <br/>'; 
            }
            oci_free_statement($stid);
	    }
	    
	    function get_sensors($conn){
	        #echo 'getting all sensors<br>';
	        $arr = array();
	        $sql = "SELECT * FROM sensors";
	        $stid = oci_parse($conn,$sql);
	        $res = oci_execute($stid);
	        
	        if (!$res) {
		        $err = oci_error($stid);
		        echo htmlentities($err['message']);
            } else { 
                #echo 'Rows Extracted <br/>'; 
            }
	        
	        
	        while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
		        foreach ($row as $item) {
		            #echo $item.'&nbsp;';
		        }
		        array_push($arr,$row);
		        #echo '<br/>';
	        }
	        oci_free_statement($stid);
	        return $arr;
	    }
	    
	    function get_subscribed_sensors($conn,$person_id){
	        #echo "getting all sensored subscribed by pid:$person_id<br>";
	        $arr = array();
	        $sql = "SELECT * FROM subscriptions
	        WHERE person_id=$person_id";
	        $stid = oci_parse($conn,$sql);
	        $res = oci_execute($stid);
	        
	        if (!$res) {
		        $err = oci_error($stid);
		        echo htmlentities($err['message']);
            } else { 
                #echo 'Rows Extracted <br/>'; 
            }
	        
	        while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
		        foreach ($row as $item) {
		            #echo $item.'&nbsp;';
		        }
		        array_push($arr,$row);
		        #echo '<br/>';
	        }
	        oci_free_statement($stid);
	        return $arr;
	    }
	    
	    function is_subscribed($conn,$sensor_id,$person_id){
	        $sql = "SELECT * FROM subscriptions
	        WHERE sensor_id=$sensor_id AND person_id=$person_id";
	        $stid = oci_parse($conn,$sql);
	        $res = oci_execute($stid);
	        if (!$res) {
		        $err = oci_error($stid);
		        echo htmlentities($err['message']);
            } else { 
                #echo 'Rows Extracted <br/>'; 
            }
            if($row = oci_fetch_array($stid,OCI_ASSOC)){
                return true;
            } else {
                return false;
            }
	    }


	    include ("PHPconnectionDB.php");        
	    //establish connection
            $conn=connect();

            session_start();
			if ($_SESSION['status'] != 's') {?>
				Not a valid scientist, Please log in again. 
				<a href = 'LogoutModule.php'>
					<button>Login</button>
				</a>
				<?php
				return;
			}
			$pid = $_SESSION['personid'];
            echo "<form name='submit1' method='POST' action='subscribeModule.php'>";
           	                
            $rows = get_sensors($conn);
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                $submit = $_POST['sensors'];
                $i = 0;                
                foreach($rows as $row){
                    if(!empty($submit) and in_array($row['SENSOR_ID'],$submit)){
                        add_subscription($conn,$row['SENSOR_ID'],$pid);
                    } else {
                        remove_subscription($conn,$row['SENSOR_ID'],$pid);
                    }
                    $i++;
                }
                echo "Subscribed to selected sensors. (Total:".count(get_subscribed_sensors($conn,$pid)).")";
            }
    
            
            echo "<table border='1' class='table table-hover'>";
            echo "<tr><td>Subscribed</td><td>SENSOR_ID</td><td>LOCATION</td></tr>";
            foreach($rows as $row){
                if(is_subscribed($conn,$row['SENSOR_ID'],$pid)){
                    echo "<tr><td><input type='checkbox' name='sensors[]' value='".$row['SENSOR_ID']."' checked></td>";
                    echo "<td>".$row['SENSOR_ID']."</td>";
                    echo "<td>".$row['LOCATION']."</td></tr>";
                } else {
                    echo "<tr><td><input type='checkbox' name='sensors[]' value='".$row['SENSOR_ID']."'></td><td>".$row['SENSOR_ID'].'</td><td>'.$row['LOCATION'].'</td></tr>';
                }
            }
            echo "</table>";
            oci_close($conn);
            echo "<br>";
            echo "<input type='submit' class='btn btn-primary' name='submit' value='submit'>";
            echo "</form>";
	
	    ?>
	<a href="MainPage.php" class="btn btn-primary">Back</a>
	</div>
    </body>
</html>

