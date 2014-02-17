<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
  <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>amCharts Example</title> 
        <link rel="stylesheet" href="style.css" type="text/css">
        
		<script src="../include/amcharts/amcharts.js" type="text/javascript"></script>
		
		<script src="javascript/amcharts.js" type="text/javascript"></script>
        
        <script type="text/javascript">
        
        // declaring variables
        var chart;
        var dataProvider;
        
        // this method called after all page contents are loaded
        window.onload = function() {
            createChart(); 
			//loadCSV("getdata.php?");
           loadCSV("data.txt");                                    
        }
        
        // method which loads external data
        function loadCSV(file) {
            if (window.XMLHttpRequest) {
                // IE7+, Firefox, Chrome, Opera, Safari
                var request = new XMLHttpRequest();
            }
            else {
                // code for IE6, IE5
                var request = new ActiveXObject('Microsoft.XMLHTTP');
            }
            // load
            request.open('GET', file, false);
            request.send();
            parseCSV(request.responseText);
        }
        
        // method which parses csv data
        function parseCSV(data){ 
		
			alert(data);
			
            //replace UNIX new lines
            data = data.replace (/\r\n/g, "\n");
            //replace MAC new lines
            data = data.replace (/\r/g, "\n");
            //split into rows
            var rows = data.split("\n");
            // create array which will hold our data:
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
					//alert(date);
                }
            }
            // set data provider to the chart
            chart.dataProvider = dataProvider;
            // this will force chart to rebuild using new data            
            chart.validateData();
			
        }
        
        // method which creates chart
        function createChart(){
            // chart variable is declared in the top
            chart = new AmCharts.AmSerialChart();
            // here we tell the chart name of category 
            // field in our data provider.
            // we called it "date" (look at parseCSV method)
            chart.categoryField = "date";
            
            // chart must have a graph
            var graph = new AmCharts.AmGraph();
            // graph should know at what field from data
            // provider it should get values.
            // let's assign value1 field for this graph
            graph.valueField = "value1";
            // and add graph to the chart
            chart.addGraph(graph);            
            // 'chartdiv' is id of a container 
            // where our chart will be                        
            chart.write('chartdiv');
        }
        </script>
  </head>
	
   <body style="background-color:#EEEEEE">
        <div id="chartdiv" style="width:600px; height:400px; background-color:#FFFFFF"></div>
   </body>
</html>
