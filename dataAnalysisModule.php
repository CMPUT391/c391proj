<html>
 
<head>
<title>Search Module</title>
<link rel="stylesheet" href="https://cdn.rawgit.com/twbs/bootstrap/v4-dev/dist/css/bootstrap.css">
</head>
 
<body>
    <div class='container'>
	<?php
		include ("PHPconnectionDB.php");
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
        $conn=connect();

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
	?>
<h1> Data Analysis Module </h1>
<legend> Search Conditions </legend>
<form name='search' class="form-group" method='post' action='dataAnalysisModule.php'>
    <div class='col-xs-2'>
        <input type="number" value="<?php echo isset($_POST['analysis_year']) ? $_POST['analysis_year'] : '' ?>" class='form-control' min="1900" max="2099" name="analysis_year" placeholder="Enter year">
    </div>
    <div class='col-xs-3'>
        <select name='analysis_range' class='form-control'><br>
        <label for="analysis_range">Search Range</label> 
            <option disabled selected>--Select Window--</option>
            <option value="y" <?php echo (isset($_POST['analysis_range']) && $_POST['analysis_range'] == 'y') ? 'selected="selected"':''; ?>>Yearly</option>
            <option value="q" <?php echo (isset($_POST['analysis_range']) && $_POST['analysis_range'] == 'q') ? 'selected="selected"':''; ?>>Quarterly</option>
            <option value="m" <?php echo (isset($_POST['analysis_range']) && $_POST['analysis_range'] == 'm') ? 'selected="selected"':''; ?>>Monthly</option>
            <option value="w" <?php echo (isset($_POST['analysis_range']) && $_POST['analysis_range'] == 'w') ? 'selected="selected"':''; ?>>Weekly</option>
            <option value="d" <?php echo (isset($_POST['analysis_range']) && $_POST['analysis_range'] == 'd') ? 'selected="selected"':''; ?>>Daily</option>
        </select> 
    </div>
    <div class='col-xs-3'>
    	<select name='analysis_sid' class='form-control'><br>
        <label for="analysis_sid">Search Range</label> 
            <option disabled selected>--Select Sensor ID--</option>
            <?php
            	$sensors = get_subscribed_sensors($conn,$pid);
            	if(empty($sensors)){
            		echo "<option disabled selected>None Available</option>";
            	} else {
            		foreach($sensors as $sensor){
                        if (isset($_POST['analysis_sid']) && ($_POST['analysis_sid'] == $sensor['SENSOR_ID'])){
                            echo "<option value=".$sensor['SENSOR_ID'].' selected="selected">'.$sensor['SENSOR_ID']."</option>";
                        }else{
                			echo "<option value=".$sensor['SENSOR_ID'].">".$sensor['SENSOR_ID']."</option>";
                        }
            		}
            	}
            ?>
        </select> 
    </div>
    <button type="submit" class="btn btn-primary" id='searchBtn' name="searchBtn">Submit</button>

 	<br>
    <?php
        $valid = false;

      


        // select all subscribed sensors
        // from those sensors select all sensors that fall within the date range
        // do Avg/Min/Max calc from the remaning sensors 
        function data_analysis_all($conn,$sensor_id,$type){
        	$typestr = "";
        	if($type == 0){
        		$typestr = "MAX";
        	} elseif($type == 1){
        		$typestr = "MIN";
        	} else {
        		$typestr = "AVG";
        	}
        	$value;
	        $sql = "SELECT ".$typestr."(value) FROM scalar_data
	        WHERE $sensor_id=sensor_id";
	        $stid = oci_parse($conn,$sql);
	        $res = oci_execute($stid);

	        if (!$res) {
		        $err = oci_error($stid);
		        echo htmlentities($err['message']);
            } else { 
                #echo 'Rows Extracted <br/>'; 
            }
	        while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
	        	//echo "dfklgjldkgj";
	        	echo $typestr." IS".$row[$typestr."(VALUE)"];
	        	$value = $row;
	        }
	        oci_free_statement($stid);
	        if ($value == NULL){
	        	echo "no data";
	        }
	        return $value;
        }

        function data_analysis_year($conn,$sensor_id,$year){
            $year = intval($year);
            //echo $year;
            //$year = intval($year);
            $value;
            $sql = "SELECT MAX(value),MIN(value),AVG(value) FROM scalar_data
            WHERE $sensor_id=sensor_id AND
            $year=EXTRACT(year FROM date_created)";
            $stid = oci_parse($conn,$sql);
            $res = oci_execute($stid);
            $first = true;
            if (!$res) {
                $err = oci_error($stid);
                echo htmlentities($err['message']);
            } else { 
                #echo 'Rows Extracted <br/>'; 
            }
            while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
                if($first){
                    echo "<table class='table table-hover'><tr><td>Year</td><td>Max</td><td>Min</td><td>Avg</td></tr>";
                    $first = false;
                }
                //echo "dfklgjldkgj";
                echo "<tr><td><input type='submit' name='drilldown_year' value=".$year." /></td><td>".$row["MAX(VALUE)"]."</td><td>".$row["MIN(VALUE)"]."</td><td>".$row["AVG(VALUE)"]."</td></tr>";
                //echo "MAX: ".$row["MAX(VALUE)"]." MIN: ".$row["MIN(VALUE)"]." AVG: ".$row["AVG(VALUE)"];
                $value = $row;
            }
            oci_free_statement($stid);
            if ($value == NULL){
                echo "no data";
            } else {
                echo "</table>";
            }
            return $value;
        }

        function data_analysis_quarter($conn,$sensor_id,$year){
            $year = intval($year);
            //echo $year;
            //$year = intval($year);

            $value;
            $sql = "SELECT MAX(value),MIN(value),AVG(value), to_number(to_char(date_created,'Q')) FROM scalar_data
            WHERE $sensor_id=sensor_id AND
            $year=EXTRACT(year FROM date_created)
            GROUP BY to_number(to_char(date_created,'Q'))
            ORDER BY to_number(to_char(date_created,'Q'))";
            $stid = oci_parse($conn,$sql);
            $res = oci_execute($stid);
            $first = true;
            if (!$res) {
                $err = oci_error($stid);
                echo htmlentities($err['message']);
            } else { 
                #echo 'Rows Extracted <br/>'; 
            }
            while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
                if($first){
                    echo "<table class='table table-hover'><tr><td>Quarter</td><td>Max</td><td>Min</td><td>Avg</td></tr>";
                    $first = false;
                }
                //echo "dfklgjldkgj";
                /*
                foreach($row as $key => $value){
                    echo $key;
                }*/
                echo "<tr><td><input type='submit' name='drilldown_quarter' value=".$row["TO_NUMBER(TO_CHAR(DATE_CREATED,'Q'))"]." /></td><td>".$row["MAX(VALUE)"]."</td><td>".$row["MIN(VALUE)"]."</td><td>".$row["AVG(VALUE)"]."</td></tr>";
                //echo "Quarter:".$row["TO_NUMBER(TO_CHAR(DATE_CREATED,'Q'))"]." MAX: ".$row["MAX(VALUE)"]." MIN: ".$row["MIN(VALUE)"]." AVG: ".$row["AVG(VALUE)"];
                //echo "<br>";
                $value = $row;
            }
            oci_free_statement($stid);
            if ($value == NULL){
                echo "no data";
            } else {
                echo "</table>";
            }
            return $value;
        }

        function data_analysis_month($conn,$sensor_id,$year,$quarter=0){
            $year = intval($year);
            //echo $year;
            //$year = intval($year);

            $value;

            if($quarter == 0){
                $sql = "SELECT MAX(value),MIN(value),AVG(value), EXTRACT(month FROM date_created) FROM scalar_data
                WHERE $sensor_id=sensor_id AND
                $year=EXTRACT(year FROM date_created)
                GROUP BY EXTRACT(month FROM date_created)
                ORDER BY EXTRACT(month FROM date_created)";
            } else {
                $sql = "SELECT MAX(value),MIN(value),AVG(value), EXTRACT(month FROM date_created) FROM scalar_data
                WHERE $sensor_id=sensor_id AND
                $year=EXTRACT(year FROM date_created) AND
                $quarter=to_number(to_char(date_created,'Q'))
                GROUP BY EXTRACT(month FROM date_created)
                ORDER BY EXTRACT(month FROM date_created)";
            }
            $stid = oci_parse($conn,$sql);
            $res = oci_execute($stid);
            $first = true;
            if (!$res) {
                $err = oci_error($stid);
                echo htmlentities($err['message']);
            } else { 
                #echo 'Rows Extracted <br/>'; 
            }
            while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
                if($first){
                    echo "<table class='table table-hover'><tr><td>Month</td><td>Max</td><td>Min</td><td>Avg</td></tr>";
                    $first = false;
                }
                //echo "dfklgjldkgj";
                /*
                foreach($row as $key => $value){
                    echo $key;
                }*/
                echo "<tr><td><input type='submit' name='drilldown_month' value=".$row["EXTRACT(MONTHFROMDATE_CREATED)"]." /></td><td>".$row["MAX(VALUE)"]."</td><td>".$row["MIN(VALUE)"]."</td><td>".$row["AVG(VALUE)"]."</td></tr>";
                //echo "Month:".$row["EXTRACT(MONTHFROMDATE_CREATED)"]." MAX: ".$row["MAX(VALUE)"]." MIN: ".$row["MIN(VALUE)"]." AVG: ".$row["AVG(VALUE)"];
                //echo "<br>";
                $value = $row;
            }
            oci_free_statement($stid);
            if ($value == NULL){
                echo "no data";
            } else {
                echo "</table>";
            }
            return $value;
        }
 		
        function data_analysis_week($conn,$sensor_id,$year,$month=0){
            $year = intval($year);
            //echo $year;
            //$year = intval($year);
            $first = true;
            $value;
            if($month==0){
                $sql = "SELECT MAX(value),MIN(value),AVG(value), to_number(to_char(date_created,'WW')) FROM scalar_data
                WHERE $sensor_id=sensor_id AND
                $year=EXTRACT(year FROM date_created)
                GROUP BY to_number(to_char(date_created,'WW'))
                ORDER BY to_number(to_char(date_created,'WW'))";
            }else{
                $sql = "SELECT MAX(value),MIN(value),AVG(value), to_number(to_char(date_created,'WW')) FROM scalar_data
                WHERE $sensor_id=sensor_id AND
                $year=EXTRACT(year FROM date_created) AND
                $month=EXTRACT(month FROM date_created)
                GROUP BY to_number(to_char(date_created,'WW'))
                ORDER BY to_number(to_char(date_created,'WW'))"; 
            }
            $stid = oci_parse($conn,$sql);
            $res = oci_execute($stid);

            if (!$res) {
                $err = oci_error($stid);
                echo htmlentities($err['message']);
            } else { 
                #echo 'Rows Extracted <br/>'; 
            }
            while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
                if($first){
                    echo "<table class='table table-hover'><tr><td>Week</td><td>Max</td><td>Min</td><td>Avg</td></tr>";
                    $first = false;
                }
                //echo "dfklgjldkgj";
                /*
                foreach($row as $key => $value){
                    echo $key;
                }*/
                echo "<tr><td><input type='submit' name='drilldown_week' value=".$row["TO_NUMBER(TO_CHAR(DATE_CREATED,'WW'))"]." /></td><td>".$row["MAX(VALUE)"]."</td><td>".$row["MIN(VALUE)"]."</td><td>".$row["AVG(VALUE)"]."</td></tr>";
                //echo "Week:".$row["TO_NUMBER(TO_CHAR(DATE_CREATED,'WW'))"]." MAX: ".$row["MAX(VALUE)"]." MIN: ".$row["MIN(VALUE)"]." AVG: ".$row["AVG(VALUE)"];
                //echo "<br>";
                $value = $row;
            }
            oci_free_statement($stid);
            if ($value == NULL){
                echo "no data";
            } else {
                echo "</table>";
            }
            return $value;
        }

        function data_analysis_day($conn,$sensor_id,$year,$week=0){
            $year = intval($year);
            //echo $year;
            //$year = intval($year);
            $first = true;
            $value;
            if ($week == 0){
                $sql = "SELECT MAX(value),MIN(value),AVG(value), to_number(to_char(date_created,'DDD')) FROM scalar_data
                WHERE $sensor_id=sensor_id AND
                $year=EXTRACT(year FROM date_created)
                GROUP BY to_number(to_char(date_created,'DDD'))
                ORDER BY to_number(to_char(date_created,'DDD'))";
            }else{
                $sql = "SELECT MAX(value),MIN(value),AVG(value), to_number(to_char(date_created,'DDD')) FROM scalar_data
                WHERE $sensor_id=sensor_id AND
                $year=EXTRACT(year FROM date_created) AND 
                $week=to_number(to_char(date_created,'WW'))
                GROUP BY to_number(to_char(date_created,'DDD'))
                ORDER BY to_number(to_char(date_created,'DDD'))";
            }
            $stid = oci_parse($conn,$sql);
            $res = oci_execute($stid);

            if (!$res) {
                $err = oci_error($stid);
                echo htmlentities($err['message']);
            } else { 
                #echo 'Rows Extracted <br/>'; 
            }

            while ($row = oci_fetch_array($stid, OCI_ASSOC)) {
                if($first){
                    echo "<table class='table table-hover'><tr><td>Day</td><td>Max</td><td>Min</td><td>Avg</td></tr>";
                    $first = false;
                }
                //echo "dfklgjldkgj";
                /*
                foreach($row as $key => $value){
                    echo $key;
                }*/
                echo "<tr><td>".$row["TO_NUMBER(TO_CHAR(DATE_CREATED,'DDD'))"]."</td><td>".$row["MAX(VALUE)"]."</td><td>".$row["MIN(VALUE)"]."</td><td>".$row["AVG(VALUE)"]."</td></tr>";
                //echo "Day:".$row["TO_NUMBER(TO_CHAR(DATE_CREATED,'DDD'))"]." MAX: ".$row["MAX(VALUE)"]." MIN: ".$row["MIN(VALUE)"]." AVG: ".$row["AVG(VALUE)"];
                //echo "<br>";
                $value = $row;
            }
            oci_free_statement($stid);
            if ($value == NULL){
                echo "no data";
            } else {
                echo "</table>";
            }
            return $value;
        }


        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            echo "</br><legend> Search Results </legend>";
        	$valid = true;
            if($_POST['drilldown_year'] != NULL){
                #echo "Do drilldown";
                data_analysis_quarter($conn,$_POST['analysis_sid'],$_POST['drilldown_year']);
            }elseif($_POST['drilldown_quarter'] != NULL){
                #echo "Do drilldown quarter";
                data_analysis_month($conn,$_POST['analysis_sid'],$_POST['analysis_year'],$_POST['drilldown_quarter']);
            }elseif($_POST['drilldown_month'] != NULL){
                #echo "Do drilldown_month";
                data_analysis_week($conn,$_POST['analysis_sid'],$_POST['analysis_year'],$_POST['drilldown_month']);
            }elseif($_POST['drilldown_week'] != NULL){
                #echo "Do drilldown_week";
                data_analysis_day($conn,$_POST['analysis_sid'],$_POST['analysis_year'],$_POST['drilldown_week']);
            }else{
            	if($_POST['analysis_sid'] == NULL){
            		echo "<p>Error: Please select a valid sensor.</p><br>";
            		$valid = false;
            	}
            	if($_POST['analysis_range'] == NULL){
            		echo "<p>Error: Please select a time window.</p><br>";
            		$valid = false;
            	}
            	if($_POST['analysis_year'] == NULL){
            		echo "<p>Error: Please select a valid year.</p><br>";
            		$valid = false;
            	}
            	if($valid){
                    /*
    	            echo "<p>".$_POST['analysis_range']."</p>";
    	            echo "<p>".$_POST['analysis_year']."</p>";
    	            data_analysis_all($conn,$_POST['analysis_sid'],0);
    	            data_analysis_all($conn,$_POST['analysis_sid'],1);
    	            data_analysis_all($conn,$_POST['analysis_sid'],2);*/
                    echo "<br>";
                    if($_POST['analysis_range'] == 'y'){
                        //echo "<p>Report for Year: ".$_POST['analysis_year']."</p><br>";
                        data_analysis_year($conn,$_POST['analysis_sid'],$_POST['analysis_year']);
                    }
                    if($_POST['analysis_range'] == 'q'){
                        //echo "<p>Quarterly Report for Year: ".$_POST['analysis_year']."</p><br>";
                        data_analysis_quarter($conn,$_POST['analysis_sid'],$_POST['analysis_year']);
                    }
                    if($_POST['analysis_range'] == 'm'){
                        //echo "<p>Monthly Report for Year: ".$_POST['analysis_year']."</p><br>";
                        data_analysis_month($conn,$_POST['analysis_sid'],$_POST['analysis_year']);
                    }
                    if($_POST['analysis_range'] == 'w'){
                        //echo "<p>Weekly Report for Year: ".$_POST['analysis_year']."</p><br>";
                        data_analysis_week($conn,$_POST['analysis_sid'],$_POST['analysis_year']);
                    }
                    if($_POST['analysis_range'] == 'd'){
                        //echo "<p>Daily Report for Year: ".$_POST['analysis_year']."</p><br>";
                        data_analysis_day($conn,$_POST['analysis_sid'],$_POST['analysis_year']);
                    }
    	        }
            }
        }
    ?>
 
    <a href="MainPage.php" class="btn btn-primary">Back</a>
</form> 
</div> 
</body>
 
</html>