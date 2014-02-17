<?php
require_once("ini.php");
$config="environ.ini";
$ini_array = readINIfile($config, "#");
$dbhost=$ini_array['server']['host'];
$database=$ini_array['server']['database'];
$user=$ini_array['server']['user'];
$pass=$ini_array['server']['password'];
$link=mysql_connect($dbhost, $user, $pass);
if (!$link)
	{
	die('<p>Not connected : ' . mysql_error().'</p>');
	}
$db_selected = mysql_select_db($database, $link);

echo "<h1>Monitors Configuration</h1>\n";

echo "<form name=\"configfrm\" method=\"post\" action=\"configexec.php\">\n";
require_once("ini.php");
$config="environ.ini";
$ini_array = readINIfile($config, "#");
//	print_r($ini_array);
$lognum=1;
while (list($key, $val) = each($ini_array))
	{
	if ($key != "server")
		{
		$sql="SELECT * FROM monitors WHERE monitor='".$key."'";
		$result=mysql_query($sql);
		$row = mysql_fetch_assoc($result);

		echo "\t<h2>Monitor ".$lognum."</h2>\n";
		echo "\t\t<p>Name<input class=\"config\" type=\"text\" name=\"name_".$lognum."\" value=\"".$key."\" /></p>\n";
		echo "\t\t<input type=\"hidden\" name=\"oldname_".$lognum."\" value=\"".$key."\" /></p>\n";
		echo "\t\t<p>Host<input class=\"config\" type=\"text\" name=\"host_".$lognum."\" value=\"".$val['host']."\" /> <span style=\"position: absolute; left: 270px;\"> Channel 1<input type=\"radio\" name=\"chan_".$lognum."\" value=\"1\"";
           if ($val['chan']=="1")
           {echo "checked";}
           echo " /> Channel 2<input type=\"radio\" name=\"chan_".$lognum."\" value=\"2\"";
           if ($val['chan']=="2")
           {echo "checked";}
           echo " /></span></p>";
		echo "\t\t<p>Description<input class=\"config\" type=\"text\" name=\"desc_".$lognum."\" value=\"".$row['description']."\" /></p>\n";
		echo "\t\t<p>Temperature Limits<br />\n";
		echo "\t\tAlarm High <input class=\"limit\" type=\"text\" name=\"tah_".$lognum."\" value=\"".$row['tempah']."\" />&#8195;";
		echo "\t\tWarning High <input class=\"limit\" type=\"text\" name=\"twh_".$lognum."\" value=\"".$row['tempwh']."\" />&#8195;";
		echo "\t\tWarning Low <input class=\"limit\" type=\"text\" name=\"twl_".$lognum."\" value=\"".$row['tempwl']."\" />&#8195;";
		echo "\t\tAlarm Low <input class=\"limit\" type=\"text\" name=\"tal_".$lognum."\" value=\"".$row['tempal']."\" /></p>\n";
		echo "\t\t<p>Humidity Limits<br />\n";
		echo "\t\tAlarm High <input class=\"limit\" type=\"text\" name=\"hah_".$lognum."\" value=\"".$row['humidah']."\" />&#8195;";
		echo "\t\tWarning High <input class=\"limit\" type=\"text\" name=\"hwh_".$lognum."\" value=\"".$row['humidwh']."\" />&#8195;";
		echo "\t\tWarning Low <input class=\"limit\" type=\"text\" name=\"hwl_".$lognum."\" value=\"".$row['humidwl']."\" />&#8195;";
		echo "\t\tAlarm Low <input class=\"limit\" type=\"text\" name=\"hal_".$lognum."\" value=\"".$row['humidal']."\" /></p>\n";
		echo "\t\t<p>Email Notices to <input class=\"config\" type=\"text\" name=\"email_".$lognum."\" value=\"".$val['email']."\" /></p>\n";
		echo "\t\t<p>Email Active <input class=\"limit\" type=\"checkbox\" name=\"active_".$lognum."\" value=\"true\"";
		if ($val['active']=="true")
			echo " checked ";
		echo "/> Hidden <input class=\"limit\" type=\"checkbox\" name=\"hidden_".$lognum."\" value=\"true\"";
		if ($val['hidden']=="true")
			echo " checked ";
		echo "/> Dew Point Plot <input class=\"limit\" type=\"checkbox\" name=\"dew_".$lognum."\" value=\"true\"";
		if ($val['dew']=="true")
			echo " checked ";
		echo "/></p>\n";
		$lognum++;
		}
	}
echo "\t<h2>Monitor ".$lognum."</h2>\n";
echo "\t\t<p>Name<input class=\"config\" type=\"text\" name=\"name_".$lognum."\" /></p>\n";
echo "\t\t<input type=\"hidden\" name=\"oldname_".$lognum."\" value=\"\" /></p>\n";
echo "\t\t<p>Host<input class=\"config\" type=\"text\" name=\"host_".$lognum."\" /> <span style=\"position: absolute; left: 270px;\"> Channel 1<input type=\"radio\" name=\"chan_".$lognum."\" value=\"1\" checked /> Channel 2<input type=\"radio\" name=\"chan_".$lognum."\" value=\"2\" /></span></p>";
echo "\t\t<p>Description<input class=\"config\" type=\"text\" name=\"desc_".$lognum."\" value=\"".$val['host']."\" /></p>\n";
echo "\t\t<p>Temperature Limits<br />\n";
echo "\t\tAlarm High <input class=\"limit\" type=\"text\" name=\"tah_".$lognum."\" value=\"\" />&#8195;";
echo "\t\tWarning High <input class=\"limit\" type=\"text\" name=\"twh_".$lognum."\" value=\"\" />&#8195;";
echo "\t\tWarning Low <input class=\"limit\" type=\"text\" name=\"twl_".$lognum."\" value=\"\" />&#8195;";
echo "\t\tAlarm Low <input class=\"limit\" type=\"text\" name=\"tal_".$lognum."\" value=\"\" /></p>\n";
echo "\t\t<p>Humidity Limits<br />\n";
echo "\t\tAlarm High <input class=\"limit\" type=\"text\" name=\"hah_".$lognum."\" value=\"\" />&#8195;";
echo "\t\tWarning High <input class=\"limit\" type=\"text\" name=\"hwh_".$lognum."\" value=\"\" />&#8195;";
echo "\t\tWarning Low <input class=\"limit\" type=\"text\" name=\"hwl_".$lognum."\" value=\"\" />&#8195;";
echo "\t\tAlarm Low <input class=\"limit\" type=\"text\" name=\"hal_".$lognum."\" value=\"\" /></p>\n";
echo "\t\t<p>Email Notices to <input class=\"config\" type=\"text\" name=\"email_".$lognum."\" value=\"".$val['email']."\" /></p>\n";
echo "\t\t<p>Email Active <input class=\"limit\" type=\"checkbox\" name=\"active_".$lognum."\" value=\"".$val['active']."\" />\n";
echo " Hidden <input class=\"limit\" type=\"checkbox\" name=\"hidden_".$lognum."\" value=\"true\" />\n";
echo " Dew Point Plot <input class=\"limit\" type=\"checkbox\" name=\"dew_".$lognum."\" value=\"true\" /></p>";
echo "\t\t<p><input type=\"submit\" /></p>";
echo "</form>\n";
?>
