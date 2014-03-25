<?php
$format = '%Y-%m-%d %H:%M:%S';
$now = date('Y-m-d H:i:s');
$week=Array("Sun","Mon","Tue","Wed","Thur","Fri","Sat");
if ($_GET)
	{
	$mon=$_GET['mon'];
	$ycont=$_GET['ycont'];
	$title=$_GET['title'];
	$date=$_GET['date'];
	}
else if ($_SERVER['argv'])
	{
	$args = $_SERVER['argv'];
	$mon=$args['1'];
	$ycont=$args['2'];
	$title=$args['3'];
	$date=$args['4'];
	}
	
$timescale=strtotime($now)-strtotime($date);

//Database Conection
require_once("ini.php");
$config="environ.ini";
$ini_array = readINIfile($config, "#");
$host=$ini_array['server']['host'];
$database=$ini_array['server']['database'];
$user=$ini_array['server']['user'];
$pass=$ini_array['server']['password'];
$dew=$ini_array[$mon]['dew'];
$link=mysql_connect($host.":".$port, $user, $pass);

if (!$link)
	{
	die('<p>Not connected : ' . mysql_error().'</p>');
	}
$db_selected = mysql_select_db($database, $link);


include_once("../include/pChart/pChart.class");
include_once("../include/pChart/pData.class");
$DataSet = new pData();
$gdg = new pChart(800,400);

$sql="select * from monitors where monitor='$mon'";
$result=mysql_query($sql);
while ($row = mysql_fetch_assoc($result))
	{
	$maxt=$ycont."ah";
	$mint=$ycont."al";
	$max=$row[$maxt];
	$min=$row[$mint];
	}


if ($ycont=="temp")
{
  $sql="select tmstamp, $ycont vals, dew from $mon where tmstamp > \"$date\" order by tmstamp";
}
else
{
  $sql="select tmstamp, $ycont vals from $mon where tmstamp > \"$date\" order by tmstamp";
}

//echo "<p>$sql</p>\n";
$result=mysql_query($sql);
$hour=0;
$x=0;
while ($row = mysql_fetch_assoc($result))
{
	$tm=strptime($row['tmstamp'], $format);
	$mins=$tm['tm_min'];
	if ($mins=="0")
		$mins="00";
	$hour=$tm['tm_hour'];
	$day=$tm['tm_wday'];
	$tmstamp = mktime($tm['tm_hour'],$tm['tm_min'],$tm['tm_sec'],1+$tm['tm_mon'],$tm['tm_mday'],1900+$tm['tm_year']);
#	$timestamp[] = $tmstamp;
#	if ($x == 0)
#	{
#		$minX = $tmstamp;
#		$x = 1;
#	}
#	$maxX=$tmstamp;
	if (($timescale <= 15000) & (((($mins==00)|($mins==15))|(($mins==30)|($mins==45))))) //up to 4 hours - every 15 mins
	{
#		$gdg->setDateFormat("H:i:s");
		$timestamp[]="$hour:$mins";
	}
	else if (($timescale <= 57600) & ($mins==00)) //up to 16 hours - every hour
	{
		$timestamp[]="$hour:$mins";
	}
	else if (($timescale <= 87000) & (($mins==00) & ( $hour % 4 == 0))) //up to 24 hours, every 4 hours
	{
		$timestamp[]="$hour:$mins";
	}
	else if (($timescale > 87000) & (($mins==00)&($hour==0))) //over 24 hours, every day
	{
		$timestamp[]=$week[$day];
	}
	else
		$timestamp[]="";
	$ycontarr[]=$row['vals'];
	if ($ycont=="temp")
	{
		$dewarr[]=$row['dew'];
	}

	$minarr[]=$min;
	$maxarr[]=$max;
}


if (($ycont=="temp") && ($dew=="true"))
{
#	$arr = Array(
#	'Temperature' => $ycontarr,
#	'Dew Point' => $dewarr,
#	'Max' => $maxarr,
#	'Min' => $minarr
#	);
#	$colors = Array(
#	'Temperature' => Array(50,50,250),
#	'Dew Point' => Array(50,255,0),
#	'Max' => Array(255,102,102),
#	'Min' => Array(204,51,255)
#	);
	$DataSet->AddPoint($timestamp,"Name");
	$DataSet->AddPoint($ycontarr,"Temp");
	$DataSet->AddPoint($dewarr,"Dew");
	$DataSet->AddPoint($maxarr,"Max");
	$DataSet->AddPoint($minarr,"Min");
	$DataSet->SetSerieName("Temperature","Temp");
	$DataSet->SetSerieName("Dew Point","Dew");
	$DataSet->SetSerieName("Max","Max");
	$DataSet->SetSerieName("Min","Min");
	$DataSet->SetXAxisFormat("date");
	$DataSet->AddAllSeries();
}

if (($ycont=="temp") && ($dew==""))
{
	$arr = Array(
	'Temperature' => $ycontarr,
	'Max' => $maxarr,
	'Min' => $minarr
	);
	$colors = Array(
	'Temperature' => Array(50,50,250),
	'Dew Point' => Array(50,255,0),
	'Max' => Array(255,102,102),
	'Min' => Array(204,51,255)
	);
}
	
if ($ycont=="humid")
{
	$arr = Array(
	'Humidity' => $ycontarr,
	'Max' => $maxarr,
	'Min' => $minarr
	);
	$colors = Array(
	'Humidity' => Array(50,50,250),
	'Max' => Array(255,102,102),
	'Min' => Array(204,51,255)
	);
}
	
$gdg->reportWarnings("GD");
$gdg->setFontProperties("../include/Fonts/tahoma.ttf",8);
$gdg->setGraphArea(40,10,790,360);
$gdg->drawFilledRoundedRectangle(5,5,795,395,5,240,240,240);
#$gdg->drawRoundedRectangle(5,5,695,225,5,230,230,230);
$gdg->drawGraphArea(255,255,255,FALSE);
$gdg->setFontProperties("../include/Fonts/tahoma.ttf",6);


//$lines = Array();
//print_r($arr);
//print_r($timestamp);
if ($dew=="true")
{
	$gdg->setFixedScale(-70,60,13,0,0,10);
	$gdg->drawTitle(100,15,$maxX,0,0,0);
	$gdg->setColorPalette(0,50,50,250);#Temp
	$gdg->setColorPalette(1,50,255,0);#Dew
	$gdg->setColorPalette(2,255,102,102);#Max
	$gdg->setColorPalette(3,204,51,255);#Min
	$gdg->drawXYScale($DataSet->GetData(),$DataSet->GetDataDescription(),"Temp","Name",150,150,150);
	$gdg->drawXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Temp","Name",0);
	$gdg->drawXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Dew","Name",1);
	$gdg->drawXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Max","Name",2);
	$gdg->drawXYGraph($DataSet->GetData(),$DataSet->GetDataDescription(),"Min","Name",3);
}
if ($ycont=="temp")
{
#	$gdg->line_graph($arr, $colors,$timestamp,"","",FALSE,$lines,0,NULL,NULL,($min-5),($max+5)); //Line A
}
if ($ycont=="humid")
{
#	$gdg->line_graph($arr, $colors,$timestamp,"","",FALSE,$lines,0,NULL,NULL,($min-20),($max+5)); //Line A
}
$gdg->drawTreshold(0,143,55,72,FALSE,FALSE);
$gdg->drawLegend(50,30,$DataSet->GetDataDescription(),255,255,255);
$gdg->Stroke();

?>
