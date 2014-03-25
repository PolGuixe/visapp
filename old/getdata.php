<?php
//$start=$_GET['start'];
//$end=$_GET['end'];
//$logger=$_GET['logger'];
require_once("ini.php");
$config="logdata.ini";
$ini_array = readINIfile($config, "#");
$host=$ini_array['server']['host'];
$port=$ini_array['server']['port'];
$database=$ini_array['server']['database'];
$user=$ini_array['server']['user'];
$pass=$ini_array['server']['password'];
$dew=$ini_array[$logger]['dew'];

$link=new mysqli($host, $user, $pass, $database);
#debug
#$start="2013-03-01";
#$end="2013-04-1";
#$logger="ev1"
if ($link->connect_errno)
{
	die("<p>Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error."</p>");
}

$sql = "SELECT tlm_rxtime, tlm_rawval, tlm_engval from tbl_tlm_log_pribus_n12 where tlmid=566 and tlm_rxtime < '10:30:00'";
$result=$link->query($sql);
$data="";
while ($row = $result->fetch_assoc())
{
	$data .= $row['tlm_rxtime'].",".$row['tlm_rawval'].",".$row['tlm_engval']."\n";
}
echo $data;
?>