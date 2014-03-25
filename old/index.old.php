<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html>
 <head>
	<meta http-equiv="content-type" content="application/xhtml+xml;charset=utf-8" />
	<meta name="generator" content="PSPad editor, www.pspad.com">
	<title>VisApp</title>

	<style type="text/css">
		@import "/css/all.css"; /* just some basic formatting, no layout stuff */
		@import "/css/format.css";
	</style>
	
	
	<script type="text/javascript">
	var mon;
	window.onload = function()
	{
		setInterval("refreshimg()",60000);
	};

	function refreshimg()
	{
		var images = document.getElementsByName("status");
		var numim=images.length;
		var i=0;
		while (i < numim)
		{
			var imgsrc=images[i].src.split("&rand");
			images[i].src=imgsrc[0] + "&rand=" + Math.random();
			i++;
		}
	};
	
	function submitForm(action)
	{
		document.loginform.doThis.value = action;
		document.loginform.submit();
	};

	function getGraph(logger)
	{
		if (window.XMLHttpRequest)
			{
				// IE7+, Firefox, Chrome, Opera, Safari
				var request = new XMLHttpRequest();
			}
			else
			{
				// code for IE6, IE5
				var request = new ActiveXObject('Microsoft.XMLHTTP');
			}
			// load
			request.open('GET', "graph2.php?logger="+ logger, false);
			request.send();
		document.getElementById("graphcontent").innerHTML=request.responseText;
	}
  </script>

 </head>
 <?php
require_once("../aitcheck.php");
if ( strpos ($requri,"~") !== FALSE)
	{
	echo "<body style='background-color:#FFFFFF'>";
	}
else
	{
	echo "<body>";
	}
?>
	<div id="titlediv">
	<h1>VisApp</h1>
	</div>

	<div id="leftcontent">
	<div id="l1">
<?php
	$requri = substr ($requri, 0, strpos ($requri , "visapp") );
	echo "<p><a href='http://".$host.$requri."'>Home</a></p>";
	echo "<p><a href='http://".$host.$requri."visapp/'>VisApp Home</a></p>";
	?>
	<br />
	<?php
	
	/*------------------------------------------------------------------------------------------------
	/* I DON'T KNOW WHAT THE FOLLOWING CODE DOES*/
  require_once("../aitcheck.php");
	if ($_COOKIE['authorised']=="TRUE")
	{
		echo "\t<p><a href=\"".$_SERVER['PHP_SELF']."?page=mconfig\">Monitors Configuration</a></p>\n";
		echo "\t<br />\n";
	}
	else
	{
		echo "<form name=\"loginform\" action=\"authact.php\" method=\"post\">\n\t<input type=\"hidden\" name=\"doThis\" value=\"\" />\n";
		echo "\t<input type=\"hidden\" name=\"doThis\">\n";
		echo "\t<p><label for=\"user\">User Name:</label><input class=\"login\" id=\"user\" type=\"text\" name=\"user\" /></p>\n";
		echo "\t<p><label for=\"passwd\">Password:</label><input class=\"login\" id=\"passwd\" type=\"password\" name=\"passwd\" /></p>\n";
		echo "\t<p><a href=\"javascript:void(1);\" onClick=\"submitForm('Login');\">Login</a></p>\n</form>\n<br />";
			
	}
	$date="";
	$mon="";
 	/*
	if ($_GET)
	{
 		$date=$_GET['date'];
 		$mon=$_GET['mon'];
 		$page=$_GET['page'];
	}
	*/
		
	if ($date=="")
		$date=date("Y-m-d");

	require_once("ini.php");
	$config="logdata.ini";
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

/*	<form name="graphform" method="get" action="#">
	<div id="basic_container" onClick="setval();"></div>
	<input id="basic_container" type="text" value="This doesn't work"/>
	<input type="submit" value="Get this Graph" />
	</form>

	<div style="width:230px; height:200px; margin-left:auto; margin-right:auto;">
		<object type="application/x-shockwave-flash" data="http://swf.yowindow.com/yowidget3.swf" width="230" height="200">
			<param name="movie" value="http://swf.yowindow.com/yowidget3.swf"/>
			<param name="allowfullscreen" value="true"/>
			<param name="wmode" value="opaque"/>
			<param name="bgcolor" value="#FFFFFF"/>
			<param name="flashvars" 
			value="landscape=http://landscapes.vickimoans.co.uk/kepler/kepler.yml&amp;location_id=gn:2647793&amp;location_name=Guildford&amp;time_format=24&amp;unit_system=uk&amp;background=#FFFFFF&amp;copyright_bar=false"
		/>
			<a href="http://yowindow.com/weatherwidget.php"
			style="width:230px;height:200px;display: block;text-indent: -50000px;font-size: 0px;background:#DDF url(http://yowindow.com/img/logo.png) no-repeat scroll 50% 50%;"
			>HTML weather</a>
		</object>
	</div>
	<div style="width: 230px; height: 15px; font-size: 14px; font-family: Arial,Helvetica,sans-serif; display:none;">
		<span style="float:left;"><a target="_top" href="http://yowindow.com?client=widget&amp;link=copyright" style="color: #2fa900; font-weight:bold; text-decoration:none;" title="HTML weather">YoWindow.com</a></span>
		<span style="float:right; color:#888888;"><a href="http://yr.no" style="color: #2fa900; text-decoration:none;">yr.no</a></span>
	</div>*/
	?>


	</div>
	</div>
	

	
	<div id="graphcontent">
	
<h1>Test Chart</h1>
<p style="clear: both;">Just a test to see if I can plot anything.</p>

<h2>Satellite</h2>
	<form action="index.php" method="get">
	<select name="ShortName" onchange="this.form.submit()">
	<option value="">Select Satellite</option>
<?php
	$result=$link->query("SELECT Name, ShortName FROM HwLup_Satellites order by Name");
	//create 2 links.
	while ($row = $result->fetch_assoc())
	{
      echo "<option value='".$row['ShortName']."'";
      if ($ShortName==$row['ShortName']){echo " selected='selected'";}
      echo ">".$row['Name']."</option>";
	}
?>
  </select>
  </form>




    
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>amCharts Example</title> 
        <link rel="stylesheet" href="style.css" type="text/css">
		
		<script src="../include/amcharts/amcharts.js" type="text/javascript"></script>

	   <script src="javascript/amcharts.js" type="text/javascript"></script>
        
        <script type="text/javascript">
        var chart;
		var dataProvider;
        window.onload = function() {
			createChart();
            //loadCSV("data.txt");
			loadCSV("getdata.php?");
        }
        function loadCSV(file) {
            if (window.XMLHttpRequest) {
                // IE7+, Firefox, Chrome, Opera, Safari
                var request = new XMLHttpRequest();
            }
            else {
                // code for IE6, IE5
                var request = new ActiveXObject('Microsoft.XMLHTTP');
            }
            // load data. 'false' indicates that further script 

            // is not executed until data is loaded and parsed

            request.open('GET', file, false);
            request.send();
            parseCSV(request.responseText);
        }
        function parseCSV(data){
            //alert(data);
			//replace UNIX new lines
			data = data.replace (/\r\n/g, "\n");
			//replace MAC new lines
			data = data.replace (/\r/g, "\n");
			//split into rows
			var rows = data.split("\n");
			
			 dataProvider = [];
            
            // loop through all rows
            for (var i = 0; i < rows.length; i++){
                // this line helps to skip empty rows
                if (rows[i]) {                    
                    // our columns are separated by comma
                    var column = rows[i].split(",");  
                    
                    // column is array now 
                    // first item is date
                    var date = column[0];
                    // second item is value of the second column
                    var value1 = column[1];
                    // third item is value of the fird column 
                    var value2 = column[2];
                    
                    // create object which contains all these items:
                    var dataObject = {date:date, value1:value1, value2:value2};
                    // add object to dataProvider array
                    dataProvider.push(dataObject);
					
                }
            }
			// set data provider to the chart
			chart.dataProvider = dataProvider;
			// this will force chart to rebuild using new data            
			chart.validateData();
        }
		function createChart(){
			// chart variable is declared in the top
			chart = new AmCharts.AmSerialChart();
			// loading images to be used by amChart
			chart.pathToImages = "../include/amcharts/images/";
			// adding zoom out button
			chart.zoomOutButton = {
				backgroundColor: '#000000',
				backgroundAlpha: 0.15
			};
			// STYLING
			// length of the animation
			chart.startDuration = 0.25;
			// margins between graph and canvas border
			chart.marginLeft = 15;
			chart.marginRight = 30;
			chart.marginBottom = 40; 
			// here we tell the chart name of category field in our data provider.
			// we called it "date" (look at parseCSV method)
			chart.categoryField = "date";
    
			// AXES
				// category
				var categoryAxis = chart.categoryAxis;
				//categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
				//categoryAxis.minPeriod = ""; // our data is daily, so we set minPeriod to DD
				categoryAxis.dashLength = 1;
				categoryAxis.gridAlpha = 0.15;
				categoryAxis.axisColor = "#DADADA";
		// GRAPHS
			// chart must have a graph
			var graph = new AmCharts.AmGraph();
			// graph should know at what field from data provider it should get values.
			// let's assign value1 field for this graph
			graph.valueField = "value2";
			// and add graph to the chart
			chart.addGraph(graph);  
			

			// CURSOR
				chartCursor = new AmCharts.ChartCursor();
				chartCursor.cursorPosition = "mouse";
				chartCursor.categoryBalloonDateFormat = "MMM DD, YYYY JJ:NN UTC";
				chart.addChartCursor(chartCursor);			
				
				// LEGEND
				var legend = new AmCharts.AmLegend();
				legend.marginLeft = 110;
				chart.addLegend(legend, "legend");
                chart.addTitle("", 15);
				
				// SCROLLBAR
				var chartScrollbar = new AmCharts.ChartScrollbar();
				chartScrollbar.scrollbarHeight = 40;
				chartScrollbar.color = "#FFFFFF";
				chartScrollbar.autoGridCount = true;
				chart.addChartScrollbar(chartScrollbar);
				
			// 'chartdiv' is id of a container where our chart will be                        
			chart.write('chartdiv');
		}
        </script>
 
    <body style="background-color:#EEEEEE">
        <div id="chartdiv" style="width:100%; height:600px; background-color:#FFFFFF"></div>
    </body>


	
<?php
//if ($page == "mconfig")
//{
	//require_once("config.php");
//}
//else if ($mon != "")
//{	
	//include_once("graph2-in.php");
	// $sql="select monitor, description from monitors where monitor='$mon'";
	// $result=mysql_query($sql);
	// while ($row = mysql_fetch_assoc($result))
	// {
		// echo "<h1>".$row['description']." Histograms</h1>\n";
		// echo "<p style=\"clear: both;\">The limits that are displayed on the graphs are in accordance with ECSS-Q-ST-70-01C which is a requirement for the Galileo FOC project.</p>\n";
	// }
	// $now1=gmdate('Y-m-d H:i:s',(time()-(60*60*1)));
	// $now2=gmdate('Y-m-d H:i:s',(time()-(60*60*2)));
	// $now4=gmdate('Y-m-d H:i:s',(time()-(60*60*4)));
	// $now8=gmdate('Y-m-d H:i:s',(time()-(60*60*8)));
	// $now16=gmdate('Y-m-d H:i:s',(time()-(60*60*16)));
	// $now24=gmdate('Y-m-d H:i:s',(time()-(60*60*24)));
	// $threeday=gmdate('Y-m-d H:i:s',(time()-(60*60*24*3)));
	// $week=gmdate('Y-m-d H:i:s',(time()-(60*60*24*7)));
 	// echo "		<img id=\"graphimg\" src=\"graph.php?title=Temperature&mon=$mon&ycont=temp&date=$date\" alt=\"Temperature Graph\" title=\"AIT Environment\" />\n";
 	// echo "		<img id=\"graphimg\" src=\"graph.php?title=Humidity&mon=$mon&ycont=humid&date=$date\" alt=\"Humidity graph\" title=\"AIT Environment\" />\n";
 	// echo "		<p class=\"center\"><a href=\"".$_SERVER['PHP_SELF']."?date=$now1&mon=$mon\">Now -1hr</a>";
 	// echo "&#8195;<a href=\"".$_SERVER['PHP_SELF']."?date=$now2&mon=$mon\">Now -2hr</a>";
 	// echo "&#8195;<a href=\"".$_SERVER['PHP_SELF']."?date=$now4&mon=$mon\">Now -4hr</a>";
 	// echo "&#8195;<a href=\"".$_SERVER['PHP_SELF']."?date=$now8&mon=$mon\">Now -8hr</a>";
 	// echo "&#8195;<a href=\"".$_SERVER['PHP_SELF']."?date=$now16&mon=$mon\">Now -16hr</a>";
 	// echo "&#8195;<a href=\"".$_SERVER['PHP_SELF']."?date=$now24&mon=$mon\">Now -24hr</a></p>\n";
 	// echo "		<p class=\"center\"><a href=\"".$_SERVER['PHP_SELF']."?date=$today&mon=$mon\">Today</a>";
 	// echo "&#8195;<a href=\"".$_SERVER['PHP_SELF']."?date=$threeday&mon=$mon\">Last 3 days</a>\n";
 	// echo "&#8195;<a href=\"".$_SERVER['PHP_SELF']."?date=$week&mon=$mon\">Previous Week</a></p>\n<br />\n";
	//echo "		<p class=\"center\"><a href='#' onclick=\"javascript:window.open('getdata.cgi?Monitor=$mon', 'info', 'toolbar=Yes, menubar=no, locationbar=no, personalbar=no, statusbar=yes, resizable=yes, scrollbars=yes, width=400, height=400')\">Export data</a></p>";

//} 
/*else
{ 
?>

<?php
 
	$sql="select monitor, description from monitors order by monitor";
	$result=$link->query($sql);
	$current="";
	$new=0;
	while ($row = $result->fetch_assoc())
	{
		$mon=$row['monitor'];
		$desc=$row['description'];
		$hidden=$ini_array[$mon]['hidden'];
		$now=substr($mon,0,3);
		if ($current != $now)
		{
			if ($new != 0)
			{
				echo "</div>\n";
			}
			$new=1;
			$current = $now;
		}
		else
		{
			$new=2;
		}
		if (($hidden != "true"))# || ($allowed==TRUE)
		{
			if ($new==1)
			{
				echo "<div style=\"clear: both\">\n";
			}
			echo "<div id=\"estatus\">\n<h2>$desc Sensor</h2>";
			echo "<a href=\"".$_SERVER['PHP_SELF']."?&mon=$mon\"><img id=\"sensimg\" name=\"status\" src=\"status.php?mon=$mon\" title=\"Environment Monitor Screen. Updates every minute. Click for graph view\"/></a>\n</div>\n";
		}
	}
	if ($new != 0)
	{
		echo "</div>\n";
	}

 }*/
?>

	</div>

 </body>
</html>




