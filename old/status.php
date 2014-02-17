<?php
header("Content-type: image/png");
header("Expires: 0"); 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
$image= @imagecreatetruecolor(225,150);

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

$background_color = imagecolorallocate($image, 170, 187, 187);
imagefill($image , 0,0 , $background_color);
$text_red = imagecolorallocate($image, 255, 51, 0);
$text_black = imagecolorallocate($image, 0, 0, 0);
$text_amber =  imagecolorallocate($image, 255, 204, 0);
$text_green =  imagecolorallocate($image, 0, 187, 0);
if ($_GET)
	{
	imagefttext($image, 24, 0, 5, 40, $text_black, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", "T");
	imagefttext($image, 24, 0, 5, 80, $text_black, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", "RH");
	imagefttext($image, 24, 0, 5, 120, $text_black, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", "DP");

	$mon=$_GET['mon'];
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
			if ((($tempah > $temp) & ($temp >= $tempwh)) || (($tempal < $temp) & ($temp <= $tempwl)))
				imagefttext($image, 24, 0, 75, 40, $text_amber, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $temp."°C");
			else if (($tempah <= $temp)||($tempal >= $temp))
				imagefttext($image, 24, 0, 75, 40, $text_red, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $temp."°C");
			else
				imagefttext($image, 24, 0, 75, 40, $text_green, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $temp."°C");
			if ((($humidah > $humid) & ($humid >= $humidwh)) || (($humidal < $humid) & ($humid <= $humidwl)))
				imagefttext($image, 24, 0, 75, 80, $text_amber, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $humid."%");
			else if (($humidah <= $humid) ||($humidal >= $humid))
				imagefttext($image, 24, 0, 75, 80, $text_red, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $humid."%");
			else
				imagefttext($image, 24, 0, 75, 80, $text_green, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $humid."%");
			imagefttext($image, 24, 0, 75, 120, $text_black, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $dew."°C");
			}
		}
	}
else
	{
	imagefttext($image, 24, 0, 10, 80, $text_red, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", "ERROR");
	}
$time=gmdate('H:i');
$today=gmdate('d/m/y');
//imagefttext($image, 16, 0, 10, 140, $text_green, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", "@");
//imagefttext($image, 16, 0, 30, 140, $text_amber, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", "@");
//imagefttext($image, 16, 0, 50, 140, $text_red, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", "@");
imagefttext($image, 8, 0, 80, 140, $text_black, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $today);
imagefttext($image, 8, 0, 150, 140, $text_black, "/usr/share/fonts/truetype/ttf-dejavu/DejaVuSans-Bold.ttf", $time);
imagepng($image);
imagedestroy($image);

?>
