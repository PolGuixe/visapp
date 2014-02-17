<?php
include ('aitDataBaseConfig.php');
//*****************//
//Support functions//
//*****************//
function connect($host, $user, $pass , $database){
	/*
	$host = $server['host'];
	$user = $server['user'];
	$pass = $server['pass'];
	
	/*
	echo '<option value="">'.$host.'</option>';
	echo '<option value="">'.$user.'</option>';
	echo '<option value="">'.$pass.'</option>';
	echo '<option value="">'.$database.'</option>';
	*/
	//Create link
	$link=mysqli_connect($host,$user,$pass,$database);
	/*
	if (mysqli_connect_errno()) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}
*/
	//Check link
	/*
	if (!$link) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	echo '<option value="">'.$link.'</option>';
	*/
	return $link;
	/*
	mysql_connect($host, $user, $pass) or die(mysql_error());
	mysql_select_db($database) or die(mysql_error());
	*/
}

//************************************
//Page load dropdown list//
//************************************




function select_0($link){
	//connect($aitDataBaseServerAdmin, 'satellite');
	/*
	$result= mysql_query("select satellitename, tablename, logdb from satellites order by satellitename") 
	or die(mysql_error());
	}
	
	  while($row = mysql_fetch_array( $result )) 
  
		{
		   echo '<option value="'.$row['satellitename'].'">'.$row['satellitename'].'</option>';
		}
	*/
 
	//$link=mysqli_connect("10.1.206.10","egseadmin","enterprise","satellite");
	
	$result = $link->query("select satellitename, tablename from satellites order by satellitename"); 
	mysqli_close(£link);
	$counter = 0;
	$resultArray = array();
	while ($row = mysqli_fetch_assoc($result)){//mysqli_fetch_assoc($result)
		/*
		$resultObj.=$row->satellitename;
		$resultObj.=$row->tablename;
		$resultObj.=$row->logdb;
		*/
		$resultArray[$counter] = $row;
		$counter=$counter + 1;
		
	}

			//echo count($resultArray[0]);
			//echo $results1[0]['satellitename'];
			//Fetching results $query1->fetch_assoc()
	$counter=0;
	foreach($resultArray as $resultSingle){
		
		echo "<option value='" . $resultSingle['tablename'] . "'>" . $resultSingle['satellitename'] . "</option>";
		$counter=$counter + 1;
	}
	
}

//**********************************
//First selection results
//**********************************
if($_GET['func'] == "select_1" && isset($_GET['func'])) { 
   select_1($_GET['selection']); 
}

function select_1($selection){
	global $satelliteDB;
	$satelliteDB = $selection;
	
	//return $GLOBALS;//['databases']['satelliteDB'];
	//$link=mysqli_connect("10.1.206.10","egseadmin","enterprise",$selection);
	//$link = connect("10.1.206.10","egseadmin","enterprise",$selection);
	$link = mysqli_connect($GLOBALS['aitDataBaseServerAdmin']['host'], $GLOBALS['aitDataBaseServerAdmin']['user'],$GLOBALS['aitDataBaseServerAdmin']['pass'], $satelliteDB);
	$sql = "select name, nodeid from ttcnode order by name";
	$result = $link->query($sql);
	//echo $GLOBALS['aitDataBaseServerAdmin']['host'];
	echo '<select name="select_2" id="select_2">
		  <option value="" disable="disabled" selected="selected">Choose</option>';
		  
		  while($row = mysqli_fetch_assoc( $result )) 
			{
			  echo '<option value="'.$row['nodeid'].'">'.$row['name'].'</option>';
			}
		  
	echo '</select>';
	
	echo "<script type=\"text/javascript\">
		$('#wait_2').hide();
		$('#select_2').change(function(){
		$('#wait_2').show();
		$('#result_2').hide();
		$.get(\"func.php\", {
		func: \"select_2\",
		selection: $('#select_2').val(),
		database: $('#select_1').val()
      }, function(response){
        $('#result_2').fadeOut();
        setTimeout(\"finishAjax_tier_three('result_2', '\"+escape(response)+\"')\", 500);
      });
    	return false;
	});
	</script>";
	
	

}

//**********************************
//First selection results
//**********************************

if($_GET['func'] == "select_2" && isset($_GET['func'])) { 
   select_2($_GET['selection'], $_GET['database']); 
}

function select_2($selection, $database){
	
	echo $satelliteDB;
	
	//$link=mysqli_connect("10.1.206.10","egseadmin","enterprise","rapideye3");
	$link = connect($GLOBALS['aitDataBaseServerAdmin']['host'], $GLOBALS['aitDataBaseServerAdmin']['user'],$GLOBALS['aitDataBaseServerAdmin']['pass'], $database);
	$sql = "select name, tlmid from ttctlm where nodeid=" . $selection . " order by name"; //
	$result = $link->query($sql);
	//echo $GLOBALS['aitDataBaseServerAdmin']['host'];
	echo '<select name="drop_3" id="drop_3">
		  <option value="" disable="disabled" selected="selected">Choose</option>';
		
		 
		  while($row = mysqli_fetch_assoc( $result )) 
			{
			  echo '<option value="'.$row['tlmid'].'">'.$row['name'].'</option>';
			}
		  
	echo '</select>';
	
	echo "<script type=\"text/javascript\">
$('#wait_2').hide();
	$('#drop_2').change(function(){
	  $('#wait_2').show();
	  $('#result_2').hide();
      $.get(\"func.php\", {
		func: \"drop_2\",
		drop_var: $('#drop_2').val()
      }, function(response){
        $('#result_2').fadeOut();
        setTimeout(\"finishAjax_tier_three('result_2', '\"+escape(response)+\"')\", 400);
      });
    	return false;
	});
</script>";
	
	

}


function select(){
	echo "Hello";
	// Create link
	//AITDatabaseLink('satellite');
//Define a database link
	require_once("ini.php");
	$config="AITdb.ini";
	$ini_array = readINIfile($config, "#");
	$host=$ini_array['server']['host'];
	//$database=satellite;   //$ini_array['server']['database'];
	$user=$ini_array['server']['user'];
	$pass=$ini_array['server']['password'];
	
	//Create link
	$link=mysqli_connect($host,$user,$pass,$database);
	
	//Check link
	if (!$link) {
		echo "Failed to connect to MySQL: " . mysqli_connect_error();
	}
	//Doing query
	$query1=$link->query("select satellitename, tablename, logdb from satellites order by satellitename");
	//Checking query
	if (!$query1) {
		die('Query not valid: ' . mysql_error());
	}
			//$message=$row['satellitename'];
			//echo $message;
			//echo "<script type='text/javascript'>alert($message);</script>";
			//Saving results into a multdimensional array
			
	$counter = 0;
	$results1 = array();
	while ($line = mysqli_fetch_assoc($query1)){
		$results1[$counter] = $line;
		$counter=$counter + 1;
	}
			//echo count($results1[37]);
			//echo $results1[0]['satellitename'];
			//Fetching results $query1->fetch_assoc()
	$counter=0;
	foreach($results1 as $result){
		echo "<option value='" . $counter . "'>" . $result['satellitename'] . "</option>";
		$counter=$counter + 1;
	}
			/*
			while ($row = $query1->fetch_assoc()) {
				echo "<option value='" . $row['satellitename'] . "'>" . $row['satellitename'] . "</option>";
			}
			*/
}
		?>