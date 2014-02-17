<?php
require_once("ini.php");
$config="environ.ini";

$old_array = readINIfile($config,"#");
$ini_array['server'] = $old_array['server'];

$host=$ini_array['server']['host'];
$database=$ini_array['server']['database'];
$user=$ini_array['server']['user'];
$pass=$ini_array['server']['password'];
//	print_r($_POST);
if ($_POST['doThis']=="Logout")
	{
	print_r($_POST);
	setcookie('authorised', '');
	}
else
	{
	$link=mysql_connect($host.":".$port, $user, $pass);
	if (!$link)
		{
		die('<p>Not connected : ' . mysql_error().'</p>');
		}
	$db_selected = mysql_select_db($database, $link);
	$user=$_POST['user'];
	$passwd=$_POST['passwd'];
	
	$sql="SELECT * FROM users WHERE user='".$user."'";
	$result=mysql_query($sql);
#	echo $result;
	$row=mysql_fetch_assoc($result);
	if ($row['passwd'] == $passwd)
		{
		setcookie('authorised', 'TRUE');
		}
	else
		{
		setcookie('authorised', 'FALSE');
		}
#		echo "db password ".$row['passwd'];
#		echo "fm password ".$passwd;
	
	}
$host  = $_SERVER['HTTP_HOST'];
$uri   = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
//echo $host.$uri;
header("Location: http://$host$uri/");
exit;

?>
