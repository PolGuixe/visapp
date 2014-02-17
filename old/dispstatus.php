<?php
header("Content-type: image/png");
header("Expires: 0"); 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
$image= @imagecreatetruecolor(720,480);

require_once("ini.php");
$config="environ.ini";
$ini_array = readINIfile($config, "#");
$host=$ini_array['server']['host'];
$database=$ini_array['server']['database'];
$user=$ini_array['server']['user'];
$pass=$ini_array['server']['password'];
$link=new mysqli($host, $user, $pass, $database);
if ($link->connect_errno)
{
	die("<p>Failed to connect to MySQL: (" . $link->connect_errno . ") " . $link->connect_error."</p>");
}

$background_color = imagecolorallocate($image, 0, 0, 0);
$text_red = imagecolorallocate($image, 255, 51, 0);
$text_black = imagecolorallocate($image, 0, 0, 0);
$text_amber =  imagecolorallocate($image, 255, 204, 0);
$text_green =  imagecolorallocate($image, 0, 187, 0);
$text_cyan =  imagecolorallocate($image, 0, 187, 180);
$grey = imagecolorallocate($image, 100, 100, 100);
$black = imagecolorallocate($image, 0, 0, 0);
$grey2 = imagecolorallocate($image, 200, 200, 200);
imagefill($image , 0,0 , $black);
if ($_GET)
{
	$mon=$_GET['mon'];
	$dewact=$ini_array[$mon]['dew'];
	$sql="SELECT * FROM monitors WHERE monitor='$mon'";
	$result=$link->query($sql);
	while ($row=$result->fetch_assoc())
	{
		$tempah=$row['tempah'];
		$tempwh=$row['tempwh'];
		$tempwl=$row['tempwl'];
		$tempal=$row['tempal'];
		$humidah=$row['humidah'];
		$humidwh=$row['humidwh'];
		$humidwl=$row['humidwl'];
		$humidal=$row['humidal'];
		$sql="select * from $mon where tmstamp=(select MAX(tmstamp) from $mon)";
		$result2=$link->query($sql);
		while ($row2 = $result2->fetch_assoc())
		{
			$temp=$row2['temp'];
			$humid=$row2['humid'];
			$dew=$row2['dew'];
			$tempstat = 0;
			$humstat = 0;
			if ($dewact=="true")
			{
				$ty=140;
				$dy=300;
				$hy=460;
			}
			else
			{
				$ty=300;
				$hy=460;
			}
			$tw = imageftbbox(99,0,"/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $temp."°C");
			$tx = ceil((720-$tw[2])/2);
			$hw = imageftbbox(99,0, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $humid."%");
			$hx = ceil((720-$hw[2])/2);
#Temperature Checks
			if ((($tempah > $temp) & ($temp >= $tempwh)) || (($tempal < $temp) & ($temp <= $tempwl)))
			{
				#Temperature Warning
				imagefttext($image, 99, 0, $tx, $ty, $text_amber, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $temp."°C");
				$tempstat=1;
			}
			else if (($tempah <= $temp)||($tempal >= $temp))
			{
				#Temperature Alarm
				imagefttext($image, 99, 0, $tx, $ty, $text_red, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $temp."°C");
				$tempstat=2;
			}
			else
				imagefttext($image, 99, 0, $tx, $ty, $text_green, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $temp."°C");

#Humidity Checks
			if ((($humidah > $humid) & ($humid >= $humidwh)) || (($humidal < $humid) & ($humid <= $humidwl)))
			{
			#Humidity Warning
				imagefttext($image, 99, 0, $hx, $hy, $text_amber, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $humid."%");
				$humstat=1;
			}
			else if (($humidah <= $humid) ||($humidal >= $humid))
			{
			#Humidity Alarm
				imagefttext($image, 99, 0, $hx, $hy, $text_red, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $humid."%");
				$humstat=2;
			}
			else
				imagefttext($image, 99, 0, $hx, $hy, $text_green, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $humid."%");
			
			if ($dewact == "true")
			{
				imagefttext($image, 99, 0, $tx, $dy, $text_cyan, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $dew."°C");
			}
			else
			{
				if (($humstat == 2) || ($tempstat == 2))
				{
					imagefilledrectangle ($image, 20, 20, 700, 168, $text_red);
				}
				elseif (($humstat == 1) || ($tempstat == 1))
				{
					imagefilledrectangle ($image, 20, 20, 700, 168, $text_amber);
				}
				else
				{
					imagefilledrectangle ($image, 20, 20, 700, 168, $text_green);
				}
			}
		}
	}
}
else
	{
	imagefttext($image, 24, 0, 10, 80, $text_red, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", "ERROR");
	}
imagepng($image);
imagedestroy($image);

?>
