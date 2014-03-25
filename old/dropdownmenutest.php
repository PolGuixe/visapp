

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">

<html>
 <body>
 
 <?php
	//Creating Database Link
	require_once("ini.php");
	$config="AITdb.ini";
	$ini_array = readINIfile($config, "#");
	$host=$ini_array['server']['host'];
	$database=satellite;   //$ini_array['server']['database'];
	$user=$ini_array['server']['user'];
	$pass=$ini_array['server']['password'];
	$satname_link=new mysqli($host, $user, $pass, $database);
	if ($satname_link->connect_errno)
	{
		die("<p>Failed to connect to MySQL: (" . $satname_link->connect_errno . ") " . $satname_link->connect_error."</p>");
	}		

	$satname_query=$satname_link->query("SELECT SatelliteName, TableName, LogDB FROM satellites order by SatelliteName");

 
//<?php $sql = "SELECT * FROM country";
//$result = mysql_query($sql); 
//?>

<select id="country" name='country' onchange="get_states();">
<option value=''>Select</option>

<?php while ($row = mysql_fetch_array($result)) {
    echo "<option value='" . $row['country_id'] . "'>" . $row['country_name'] . "</option>";}
?>
</select>
<div id="get_state"></div> // Sub will be appended here using ajax

<script type="text/javascript">

function get_states() { // Call to ajax function
    var country = $('#country').val();
    var dataString = "country="+country;
    $.ajax({
        type: "POST",
        url: "getstates.php", // Name of the php files
        data: dataString,
        success: function(html)
        {
            $("#get_state").html(html);
        }
    });
}
</script>

if ($_POST) {
    $country = $_POST['country'];
    if ($country != '') {
       $sql1 = "SELECT * FROM state WHERE country=" . $country;
       $result1 = mysql_query($sql1);
       echo "<select name='state'>";
       echo "<option value=''>Select</option>"; 
       while ($row = mysql_fetch_array($result1)) {
          echo "<option value='" . $row['state_id'] . "'>" . $row['state_name'] . "</option>";}
       echo "</select>";
    }
    else
    {
        echo  '';
    }
}


 </body>
</html>