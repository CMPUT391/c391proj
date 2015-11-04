<html>
    <body>
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
            echo "<form name='submit1' method='POST' action='subscribeModule.php'>";
            $pid = 1; //grab it from jimmy's stuff
           	               
           	              
            $rows = get_sensors($conn);
            if ($_SERVER['REQUEST_METHOD'] === 'POST'){
                $submit = $_POST['sensors'];
                
                $i = 0;                
                
                foreach($rows as $row){
                    if(in_array($row['SENSOR_ID'],$submit)){
                        add_subscription($conn,$row['SENSOR_ID'],$pid);
                    } else {
                        remove_subscription($conn,$row['SENSOR_ID'],$pid);
                    }
                    $i++;
                }
            }
    
            
            
            foreach($rows as $row){
                if(is_subscribed($conn,$row['SENSOR_ID'],$pid)){
                    echo "<input type='checkbox' name='sensors[]' value='".$row['SENSOR_ID']."' checked>".$row['SENSOR_ID'].' '.$row['LOCATION'].'<br>';
                } else {
                    echo "<input type='checkbox' name='sensors[]' value='".$row['SENSOR_ID']."'>".$row['SENSOR_ID'].' '.$row['LOCATION'].'<br>';
                }
            }
            oci_close($conn);
            echo "<input type='submit' name='submit' value='submit'></form>";
	
	    ?>
    </body>
</html>

