<?php
date_default_timezone_set('UTC');
$start=$_GET['start'];
$end=$_GET['end'];
$table=$_GET['table'];
if ($table ==  "")
{
	$table = "tbl_tlm_log_pribus_n12";
}
require_once("ini.php");
$config="logdata.ini";
$ini_array = readINIfile($config, "#");
$host=$ini_array['server']['host'];
$port=$ini_array['server']['port'];
$database=$ini_array['server']['database'];
$user=$ini_array['server']['user'];
$pass=$ini_array['server']['password'];
$dew=$ini_array[$table]['dew'];
$link=mysql_connect($host.":".$port, $user, $pass);

unset($ini_array['server']);
$sql = "select * from tlm_confi";//
if (!$link)
{
	die('<p>Not connected : ' . mysql_error().'</p>');
}
/*
$db_selected = mysql_select_db($database, $link);
$result=mysql_query($sql);
$tlm_confi="";
while ($row = mysql_fetch_assoc($result))
{*/
	$tlm_confi = array(
		'Test Data' => $row['description'],
		'10' => $row['max'],
		'-10' => $row['min'],
		'3' => $row['average'],
		
	);
	
	echo $tlm_confi[1]['max'];
	/*
}
*/
#json_encode($ini_data);

$startT = strtotime('Last Sunday');
$start = gmdate('Y-m-d', $startT);
$end = gmdate('Y-m-d', (strtotime('+8 days', $startT)));
?>
		<script src="../include/amcharts/amcharts.js" type="text/javascript"></script>        
		<script type="text/javascript">
			var ini_array = <?php echo json_encode($ini_array); ?>;
			//var tlm_confi = <?php echo json_encode($tlm_confi); ?>;
			var chart, tempAxis, humidAxis, tempguide, humguide;
			var chartData = new Array();
			var chartCursor;
			var start = "<?php echo $start; ?>";
			var end = "<?php echo $end; ?>";
			var logger = "<?php echo $table; ?>";
			var text = "<pre>";
			var sensRB = "";
			var t = start.split(/[- :]/);
			var today = new Date();
			var date = new Date(t[0], t[1]-1, t[2]);
			var pdate= new Date(date.getTime());
			var ndate= new Date(date.getTime());
			date.setDate(date.getDate()+8);
			pdate.setDate(pdate.getDate()-7);
			ndate.setDate(ndate.getDate()+7);
			var prevweek=pdate.getFullYear()+"-"+(pdate.gettableth()+1)+"-"+pdate.getDate();
			var nextweek=ndate.getFullYear()+"-"+(ndate.gettableth()+1)+"-"+ndate.getDate();
			window.onload = function()
			{
				createChart();
				for (x in ini_array)
				{
					text = text + x + "\n";
					if (x != "server")
					{
						sensRB = sensRB + "<input type='radio' name='sensor'"
						if (x == logger)
						{
							sensRB = sensRB + " checked = 'true' ";
							chart.titles[0].text = ini_array[x]['name'];
							setupAxes(x)
						}
						sensRB = sensRB + "value='" + x + "'onclick='updateSensor(\"" + x + "\")'>" + tlm_confi[x]['description'] + " ";
						for (y in ini_array[x])
						{
							text = text + "  " + y + "\n";
						}
					}
				}
				loadCSV("getdata.php?logger="+logger+"&start="+start+"&end="+end);
				document.getElementById("sensors").innerHTML=sensRB;
				if (ndate > today)
				{
					document.getElementById("nweek").disabled=true;
				}
			}
			
            function createChart()
			{
				// SERIAL CHART    
				chart = new AmCharts.AmSerialChart();
				chart.pathToImages = "../include/amcharts/images/";
				chart.zoomOutButton = {
					backgroundColor: '#000000',
					backgroundAlpha: 0.15
				};
                chart.categoryField = "tmstamp";
//				chart.fontFamily = "Courier";

				// listen for "dataUpdated" event (fired when chart is rendered) and call zoomChart method when it happens
//				chart.addListener("dataUpdated", zoomChart);

				
				// AXES
				// category
				var categoryAxis = chart.categoryAxis;
				categoryAxis.parseDates = true; // as our data is date-based, we set parseDates to true
				categoryAxis.minPeriod = "mm"; // our data is daily, so we set minPeriod to DD
				categoryAxis.dashLength = 1;
				categoryAxis.gridAlpha = 0.15;
				categoryAxis.axisColor = "#DADADA";

				// Temp value
				tempAxis = new AmCharts.ValueAxis();
				tempAxis.axisColor = "#FF5050";
				tempAxis.axisThickness = 2;
				tempAxis.gridAlpha = 0;
				tempAxis.title = "Temperature";
				tempAxis.unit = "°C";
				chart.addValueAxis(tempAxis);

				// second value axis (on the right) 
				humidAxis = new AmCharts.ValueAxis();
				humidAxis.position = "right"; // this line makes the axis to appear on the right
				humidAxis.axisColor = "#4d4dff";
				humidAxis.title = "Relative Humidity";
				humidAxis.unit = "%";
				humidAxis.gridAlpha = 0;
				humidAxis.axisThickness = 2;
				chart.addValueAxis(humidAxis);
	
	//Guides
				tempguide = new AmCharts.Guide();
				tempguide.fillColor='#ffa3a3';
				tempAxis.addGuide(tempguide);
				
				humguide = new AmCharts.Guide();
				humguide.fillColor='#66ffff';
				humidAxis.addGuide(humguide);

				// GRAPHS
				// Temperature Graph
				var tempGraph = new AmCharts.AmGraph();
				tempGraph.title = "Temperature";
				tempGraph.valueField = "temp";
				tempGraph.valueAxis = tempAxis;
				tempGraph.lineThickness = 2;
				tempGraph.lineColor = "#ff5050";
				tempGraph.negativeLineColor = "#0352b5";
				tempGraph.connect=false;
				chart.addGraph(tempGraph);

				// Humidity Graph
				var humidGraph = new AmCharts.AmGraph();
				humidGraph.title = "Humidity";
				humidGraph.valueField = "humid";
				humidGraph.valueAxis = humidAxis;
				humidGraph.lineThickness = 2;
				humidGraph.lineColor = "#00a300";
				humidGraph.connect=false;
				chart.addGraph(humidGraph);

				// Dew Graph
				var dewGraph = new AmCharts.AmGraph();
				dewGraph.title = "Dew Point";
				dewGraph.valueField = "dew";
				dewGraph.valueAxis = tempAxis;
				dewGraph.hidden=true;
				dewGraph.lineThickness = 2;
				dewGraph.lineColor = "#cc00cc";
				dewGraph.connect=false;
				chart.addGraph(dewGraph);

				
				// CURSOR
				chartCursor = new AmCharts.ChartCursor();
				chartCursor.cursorPosition = "mouse";
				chartCursor.categoryBalloonDateFormat = "MMM DD, YYYY JJ:NN UTC";
				chart.addChartCursor(chartCursor);

				// SCROLLBAR
				var chartScrollbar = new AmCharts.ChartScrollbar();
				chartScrollbar.scrollbarHeight = 40;
				chartScrollbar.color = "#FFFFFF";
				chartScrollbar.autoGridCount = true;
				chart.addChartScrollbar(chartScrollbar);

				// LEGEND
				var legend = new AmCharts.AmLegend();
				legend.marginLeft = 110;
				chart.addLegend(legend, "legend");
                chart.addTitle("", 15);

				// WRITE
				chart.write("chartdiv");
			};

			function loadCSV(file)
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
				request.open('GET', file, false);
				request.send();
				parseCSV(request.responseText);
			}
			
			// parse the CSV
			function parseCSV(data)
			{
				//replace UNIX new lines
				data = data.replace (/\r\n/g, "\n");
				//replace MAC new lines
				data = data.replace (/\r/g, "\n");
				//split into rows
				var rows = data.split("\n");
				
				// loop through all rows
				for (var i = 0; i < rows.length; i++)
				{
					// this line helps to skip empty rows
					if (rows[i])
					{                    
						// our columns are separated by comma
						var column = rows[i].split(",");  
						// column is array now 
						// first item is date
						var tmstamp = parseDate(column[0]);
						// second item is value of the second column
						var temp = column[1];
						// third item is value of the fird column 
						var humid = column[2];
						// third item is value of the fird column 
						if (temp!=0 && humid <= 0.1)
							dew=dewlast;
						else
							var dew = column[3];
						// create object which contains all these items:
//						var dataObject = {tmstamp:tmstamp, temp:temp}; //, humid:humid, dew:dew
						if (temp!=0 || dew!=0 || humid!=0)
						{
							chartData.push({
									dew: dew,
									humid: humid,
									temp: temp,
									tmstamp: tmstamp
							});
						}
						else
						{
							chartData.push({
									tmstamp: tmstamp
							});
						}
						var dewlast = dew
					}
				}
				chart.dataProvider = chartData;
				chart.validateData();
			}
			
			// parse the MySQL datestamp, create Date object
			function parseDate(dateString)
			{
				// split the string get each field
				var t = dateString.split(/[- :]/);
				// now lets create a new Date instance, using year, tableth and day as parameters
				// tableth count starts with 0, so we have to convert the tableth number
				return new Date(t[0], t[1]-1, t[2], t[3] || 0, t[4] || 0, t[5] || 0);
			}
			
            // this method is called when chart is first inited as we listen for "dataUpdated" event
            function zoomChart()
			{
                // different zoom methods can be used - zoomToIndexes, zoomToDates, zoomToCategoryValues
                chart.zoomToIndexes(chartData.length - 40, chartData.length - 1);
            }
            
            // changes cursor mode from pan to select
            function setPanSelect()
			{
				if (document.getElementById("rb1").checked)
				{
					chartCursor.pan = false;
					chartCursor.zoomable = true;
                }
				else
				{
					chartCursor.pan = true;
				}
				chart.validateNow();
			}            
			
			function updateData(w)
			{
				chartData=new Array();
				if (w == "p")
				{
					start=prevweek;
					date.setDate(date.getDate()-7);
					end=date.getFullYear()+"-"+(date.gettableth()+1)+"-"+date.getDate();
					pdate.setDate(pdate.getDate()-7);
					ndate.setDate(ndate.getDate()-7);
					prevweek=pdate.getFullYear()+"-"+(pdate.gettableth()+1)+"-"+pdate.getDate();
					nextweek=ndate.getFullYear()+"-"+(ndate.gettableth()+1)+"-"+ndate.getDate();
				}
				if (w == "n")
				{
					start=nextweek;
					date.setDate(date.getDate()+7);
					pdate.setDate(pdate.getDate()+7);
					ndate.setDate(ndate.getDate()+7);
					prevweek=pdate.getFullYear()+"-"+(pdate.gettableth()+1)+"-"+pdate.getDate();
					nextweek=ndate.getFullYear()+"-"+(ndate.gettableth()+1)+"-"+ndate.getDate();
					end=date.getFullYear()+"-"+(date.gettableth()+1)+"-"+date.getDate();
				}
				loadCSV("getdata.php?logger="+logger+"&start="+start+"&end="+end);
//				chart.validateNow();
//				chart.zoomOut();
				if (ndate > today)
				{
					document.getElementById("nweek").disabled=true;
				}
				else
				{
					document.getElementById("nweek").disabled=false;
				}
			}

			function setupAxes(sensor)
			{
				tempguide.value = tlm_confi[sensor]['tempal'];
				tempguide.toValue = tlm_confi[sensor]['tempah'];
				humguide.value = tlm_confi[sensor]['humidal'];
				humguide.toValue = tlm_confi[sensor]['humidah'];
				tempAxis.maximum = parseInt(tlm_confi[sensor]['tempah']) + 5;
				tempAxis.minimum = parseInt(tlm_confi[sensor]['tempal']) - 5;
				humidAxis.maximum = parseInt(tlm_confi[sensor]['humidah']) + 5;

				if ( tlm_confi[sensor]['humidal'] == 0)
					humidAxis.minimum = 0;
				else
					humidAxis.minimum = parseInt(tlm_confi[sensor]['humidal']) - 5;
				
				if (ini_array[sensor]['dew']=="true")
				{
					tempguide.fillAlpha=.0;
					humguide.fillAlpha=0;
				}
				else
				{
					tempguide.fillAlpha=.5;
					humguide.fillAlpha=.25;
				}
			}
			function updateSensor(sensor)
			{
//				chart.clearLabels();
				chartData=new Array();
//				alert(sensor);
				logger = sensor;
				chart.titles[0].text = ini_array[sensor]['name'];
				setupAxes(sensor);
				loadCSV("getdata.php?logger="+logger+"&start="+start+"&end="+end);
			}
			</script>

        <div id="chartdiv" style="width: 100%; height: 600px;"></div>
		<div id="legend"></div>
        <div style="margin-left:35px;">
            <input type="radio" checked="true" name="group" id="rb1" onclick="setPanSelect()">Select
            <input type="radio" name="group" id="rb2" onclick="setPanSelect()">Pan
            <input type="button" value="Prev Week" id="pweek" onclick="updateData('p')" />
            <input type="button" value="Next Week" id="nweek" onclick="updateData('n')" />
		</div>        
        <div style="margin-left:35px;" id="sensors">
		</div>
		<pre>
			<?php # print_r($tlm_confi); ?>
		</pre>
