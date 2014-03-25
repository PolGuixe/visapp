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

$link=new mysqli($host, $user, $pass, $database);
if ($link->connect_errno)
{
	die("<p>Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error."</p>");
}

require_once("../include/GDGraph/gdgraph.php");
#GDGraph(width, height [, title [, red_bg [, green_bg [, blue_bg [,red_line [, green_line [, blue_line [red_font [, green_font [,blue_font [, legend [, legend_x [, legend_y [, legend_border [,transparent_background [, line_thickness]]]]]]]]]]]]]]]]);
$gdg = new GDGraph(800,300,$title);

$sql="select * from monitors where monitor='$mon'";
$result=$link->query($sql);
while ($row = $result->fetch_assoc())
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
$result=$link->query($sql);
$hour=0;
while ($row = $result->fetch_assoc())
	{
	$tm=strptime($row['tmstamp'], $format);
	$mins=$tm['tm_min'];
	if ($mins=="0")
		$mins="00";
	$hour=$tm['tm_hour'];
	$day=$tm['tm_wday'];
	if (($timescale <= 15000) & (((($mins==00)|($mins==15))|(($mins==30)|($mins==45))))) //up to 4 hours - every 15 mins
		{
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
	$arr = Array(
	'Temperature' => $ycontarr,
	'Dew Point' => $dewarr,
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
	

$lines = Array();
//print_r($arr);
//print_r($timestamp);
#line_graph(data, [, colors [, x_labels [, x_title [, y_title [,paint_dots [, lines_thickness [, x_lower_value [, x_upper_value [,y_lower_value [, y_upper_value]]]]]]]]]]);
if ($ycont=="temp")
	{
	$gdg->line_graph($arr, $colors,$timestamp,"","",FALSE,$lines,0,NULL,NULL,($min-5),($max+5)); //Line A
	}
if ($ycont=="humid")
	{
	$gdg->line_graph($arr, $colors,$timestamp,"","",FALSE,$lines,0,NULL,NULL,($min-20),($max+5)); //Line A
	}
?>
