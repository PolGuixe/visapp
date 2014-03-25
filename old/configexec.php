<?php

require_once("ini.php");
$config="environ.ini";

$old_array = readINIfile($config,"#");
$ini_array['server'] = $old_array['server'];

$host=$ini_array['server']['host'];
$chan=$ini_array['server']['chan'];
$database=$ini_array['server']['database'];
$user=$ini_array['server']['user'];
$pass=$ini_array['server']['password'];
$link=mysql_connect($host.":".$port, $user, $pass);
if (!$link)
	{
	die('<p>Not connected : ' . mysql_error().'</p>');
	}
$db_selected = mysql_select_db($database, $link);
//print_r($_POST);
$lognum=1;
while ($_POST['name_'.$lognum] != "")
	{
	$values = "'".$_POST['name_'.$lognum]."', '".$_POST['tah_'.$lognum]."', '".$_POST['twh_'.$lognum]."', '".$_POST['twl_'.$lognum]."', '".$_POST['tal_'.$lognum]."', '".$_POST['hah_'.$lognum]."', '".$_POST['hwh_'.$lognum]."', '".$_POST['hwl_'.$lognum]."', '".$_POST['hal_'.$lognum]."', '".$_POST['desc_'.$lognum]."'";
	if ($_POST['oldname_'.$lognum] == "")
		{
		$sql="CREATE TABLE ".$_POST['name_'.$lognum]." (tmstamp datetime NOT NULL default '0000-00-00 00:00:00',  temp float unsigned default NULL,  humid float unsigned default NULL,  dew float unsigned default NULL,  PRIMARY KEY (tmstamp) ) ENGINE=MyISAM";
		mysql_query($sql);
		$sql="INSERT INTO monitors (monitor,tempah,tempwh,tempwl,tempal,humidah,humidwh,humidwl,humidal,description) VALUES ($values)";
		mysql_query($sql);
		}
	else 
		{
		$sql="ALTER TABLE ".$_POST['oldname_'.$lognum]." RENAME TO ".$_POST['name_'.$lognum];
		mysql_query($sql);
		$sql="UPDATE monitors SET monitor='".$_POST['name_'.$lognum]."' WHERE monitor='".$_POST['oldname_'.$lognum]."'";
		mysql_query($sql);
		$sql="REPLACE INTO monitors (monitor,tempah,tempwh,tempwl,tempal,humidah,humidwh,humidwl,humidal,description) VALUES ($values)";
		mysql_query($sql);
		}
	$ini_array[$_POST['name_'.$lognum]] = Array('host' => $_POST['host_'.$lognum],
	                'chan' => $_POST['chan_'.$lognum],
									'email' => $_POST['email_'.$lognum],
									'active' => $_POST['active_'.$lognum],
									'hidden' => $_POST['hidden_'.$lognum],
                  'dew' => $_POST['dew_'.$lognum]);
	$lognum++;
	}

writeINIfile($config, $ini_array, "#", "");
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
//echo $host.$uri;
header("Location: http://$host$uri/index.php?page=config");
exit;

?>
