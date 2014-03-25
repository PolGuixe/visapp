<!-- WEB Inisialitzation ------------------------------------------------------------------------------->

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
<!--WEB Inisialitzation Finished ---------------------------------------------------------------------------->

<!--TITLE ------------------------------------------------------------------------------------------------>
<div id="titlediv">
	<h1>VisApp</h1>
	</div>
	
<!-- Left Content Column --------------------------------------------------------------------------------->
	<div id="leftcontent">
	<div id="l1">

<?php
	$requri = substr ($requri, 0, strpos ($requri , "visapp") );
	echo "<p><a href='http://".$host.$requri."'>Home</a></p>";
	echo "<p><a href='http://".$host.$requri."visapp/'>VisApp Home</a></p>";
?>
<br />
	
<!--Graph Content------------------------------------------------------------------------------------>	

	</div>
	</div>
	
<body>		

<div id="graphcontent">
	
<h1>Test Chart</h1>
<p style="clear: both;">Just a test to see if I can plot anything.</p>

<!-- Drop Down Menus ---------------------------------------------------------------------------------->

<!-- Select Satellite -------------------------------------------------------------------------------->
<h2>Satellite</h2>
	<form action="index.php" method="get">
	<select name="SatelliteName" onchange="get_modulename();" >
	<option value="">Select Satellite</option>

	
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

	$result=$satname_link->query("SELECT SatelliteName, TableName, LogDB FROM satellites order by SatelliteName");



	while ($row = $result->fetch_assoc())
	{
      echo "<option value='".$row['SatelliteName']."'";
      if ($SatelliteName==$row['SatelliteName']){echo " selected='selected'";}
      echo ">".$row['SatelliteName']."</option>";
	}
	
?>
 </select>
  </form> 
  
  function get_modulename() {
  
  
<!-- Select TLM Node  -------------------------------------------------------------------------------->
  
  <h2>TLM Node</h2>
	<form action="index.php" method="get">
	<select name="NodeID" onchange="this.form.submit()">
	<option value="">Select TLMNode</option>
<?php
	//Creating Database Link
	require_once("ini.php");
	$config="AITdb.ini";
	$ini_array = readINIfile($config, "#");
	$host=$ini_array['server']['host'];
	$database=$satname_query['TableName'];//This is not a string is an ARRAY!!!                    //$ini_array['server']['database'];
	$user=$ini_array['server']['user'];
	$pass=$ini_array['server']['password'];
	$ttcsat_link=new mysqli($host, $user, $pass, $database);
	if ($ttcsat_link->connect_errno)
	{
		die("<p>Failed to connect to MySQL: (" . $ttcsat_link->connect_errno . ") " . $ttcsat_link->connect_error."</p>");
	}		

	$ttcsat_query=$ttcsat_link->query("SELECT NodeID, Name  FROM ttcnode order by NodeID");

	while ($row = $ttcsat_query->fetch_assoc())
	{
      echo "<option value='".$row['NodeID']."'";
      if ($SatelliteName==$row['NodeID']){echo " selected='selected'";}
      echo ">".$row['Name']."</option>";
	}
?>
  </select>
  </form>


    <!-- amChart module----------------------------------------------------------------------->
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>amCharts</title> 
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

	
	</div>

 </body>
</html>