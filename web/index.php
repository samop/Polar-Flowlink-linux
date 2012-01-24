<html>
  <head>

 <style type="text/css">
/*   * {
    margin:0;
    padding:0;
   } */
   #test {
    text-align:center;
   }  
   #test div {
    display:inline-block;
    text-align:left;
   }
  </style>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
    <!--Load the AJAX API-->
    <script type="text/javascript" src="https://www.google.com/jsapi"></script>
<!--
<script src="prototype.js" type="text/javascript"></script>
<script src="functionality.js" type="text/javascript"></script>
-->

    <script type="text/javascript">


      // Load the Visualization API and the piechart package.
      google.load('visualization', '1.0', {'packages':['corechart']});

      // Set a callback to run when the Google Visualization API is loaded.
      google.setOnLoadCallback(drawCharts);

      // Callback that creates and populates a data table,
      // instantiates the pie chart, passes in the data and
      // draws it.
      function drawCharts() {
	drawPieZone('chart1_div',0);
	drawPieZone('chart2_div',1);
	drawPieZone('chart3_div',-1);
	drawBar('chart4_div',1);
	drawBar('chart5_div',0);
	drawBar('chart6_div',2);
	drawLoad('chart7_div',2);
	drawLoadLine('chart8_div',2);
	}
	
      function drawPieZone(div,week){

        // Create the data table.
        var data = new google.visualization.DataTable();
        data.addColumn('string', 'Zone');
        data.addColumn('number', 'Percentage in zone');
	if(week==0){
<?php
	echo 'data.addRows(';
	include 'get_cweek_zones.php';
	add_zones(0);
	echo ');';
?>
        var options = {'title':'Current week training review by zone',
		      'colors':['blue','green','red','gray'],
                       'width':400,
                       'height':300};
	} else if(week==1){
<?php
	echo 'data.addRows(';
	add_zones(1);
	echo ');';
?>
        var options = {'title':'Previous week training review by zone',
		      'colors':['blue','green','red','gray'],
                       'width':400,
                       'height':300};
} else {
<?php
	echo 'data.addRows(';
	add_zones(-1);
	echo ');';
?>
        var options = {'title':'Total training review by zone',
		      'colors':['blue','green','red','gray'],
                       'width':400,
                       'height':300};

}

        // Instantiate and draw our chart, passing in some options.
        var chart = new google.visualization.PieChart(document.getElementById(div));
        chart.draw(data, options);
}

function drawBar(div, group){

    var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
        data.addColumn('number', 'Zone 1');
        data.addColumn({type:'string', role:'tooltip'});
        data.addColumn('number', 'Zone 2');
        data.addColumn({type:'string', role:'tooltip'});
        data.addColumn('number', 'Zone 3');
        data.addColumn({type:'string', role:'tooltip'});
        data.addColumn('number', 'Out of zone');
        data.addColumn({type:'string', role:'tooltip'});
        data.addColumn('number', 'Average HR');
        data.addColumn('number', 'Maximal HR');
        data.addColumn('number', 'Calories');
        data.addColumn('number', 'Calories Est.');
	if(group==0){
      data.addRows(
<?php
	include_once "get_ctraining_duration.php";
	get_training_duration(0);
?>
      );
        var options = {
		      'colors':['gray', 'blue','green','red','yellow','orange','black'],	
          width: 1200, height: 300,
	  isStacked: true,
          title: 'Overview of training',
          hAxis: {title: 'Date'},
	   seriesType: "bars",
           series: {4: {targetAxisIndex:1,
			type: "line"},
		    5: {targetAxisIndex:1,
			type: "line"},
		    6: {targetAxisIndex:2,
			type: "line"},
		    7: {targetAxisIndex:2,
			type: "line"}
		},
	   vAxes: {1: {textPosition: "in"} 
		}
       };
	} else if(group==1){
      data.addRows(
<?php
	include_once "get_ctraining_duration.php";
	get_training_duration(1);
?>
      );
        var options = {
		      'colors':['gray', 'blue','green','red','yellow','orange','black'],	
          width: 1200, height: 300,
	  isStacked: true,
          title: 'Daily training overview',
          hAxis: {title: 'Date'},
	   seriesType: "bars",
           series: {4: {targetAxisIndex:1,
			type: "line"},
		    5: {targetAxisIndex:1,
			type: "line"},
		    6: {targetAxisIndex:2,
			type: "line"},
		    7: {targetAxisIndex:2,
			type: "line"}
		} ,
	   vAxes: {1: {textPosition: "in"} 
		}
       };
       } else {
      data.addRows(
<?php
	include_once "get_ctraining_duration.php";
	get_training_duration(2);
?>
      );
        var options = {
		      'colors':['gray', 'blue','green','red','yellow','orange','black'],	
          width: 1200, height: 300,
	  isStacked: true,
          title: 'Weekly training overview',
          hAxis: {title: 'Week'},
	   seriesType: "bars",
           series: {4: {targetAxisIndex:1,
			type: "line"},
		    5: {targetAxisIndex:1,
			type: "line"},
		    6: {targetAxisIndex:2,
			type: "line"},
		    7: {targetAxisIndex:2,
			type: "line"}

		} ,
	   vAxes: {1: {textPosition: "in"} 
		}
       };
       }

        var chart = new google.visualization.ComboChart(document.getElementById(div));
        chart.draw(data, options);


}

function drawLoad(div){

    var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
        data.addColumn('number', 'Load');
        data.addColumn('number', 'Calories');
        data.addColumn('number', 'Calories Est.');
      data.addRows(
<?php
	include_once "get_ctraining_load.php";
	get_training_load(1);
?>
      );
        var options = {
		      'colors':['black', 'yellow','gray'],	
          width: 1200, height: 300,
	  isStacked: false,
          title: 'Overview of training',
          hAxis: {title: 'Date'},
	   seriesType: "bars",
           series: {1: {targetAxisIndex:1,
			type: "line"},
		    2: {targetAxisIndex:1,
			type: "line"}
		},
	   vAxes: {1: {textPosition: "out"} 
		}
       };

        var chart = new google.visualization.ComboChart(document.getElementById(div));
        chart.draw(data, options);


}

function drawLoadLine(div){

    var data = new google.visualization.DataTable();
        data.addColumn('string', 'Date');
        data.addColumn('number', 'Low-Medium Load level');
        data.addColumn('number', 'Medium-High Load level');
        data.addColumn('number', 'Load');
      data.addRows(
<?php
	include_once "get_ctraining_loadcurve2.php";
	get_training_loadcurve(1);
?>
      );
        var options = {
		      'colors':['yellow','red','black'],	
          width: 1200, height: 300,
	  isStacked: false,
          title: 'Overview of training',
          hAxis: {title: 'Date'}
       };

        var chart = new google.visualization.LineChart(document.getElementById(div));
        chart.draw(data, options);


}

    </script>
  </head>

  <body>
    <!--Div that will hold the pie chart-->
<h1>HR Training overview</h1>
	<div id="test">
    <div id="chart1_div"></div>
    <div id="chart2_div"></div>
    <div id="chart3_div"></div>
	</div>
    <div id="chart4_div"></div>
    <div id="chart5_div"></div>
    <div id="chart6_div"></div>
	<font size=-2>Estimated calories spent use formula from: Keytel LR, Goedecke JH, Noakes TD, Hiiloskorpi H, Laukkanen R, van der Merwe L, Lambert EV. Prediction of energy expenditure from heart rate monitoring during submaximal exercise. J Sports Sci. 2005 Mar;23(3):289-97. PubMed PMID: 15966347.</font>
    <div id="chart7_div"></div>
    <div id="chart8_div"></div>
<font size=-2>Explained at <a href="http://westperformance.blogspot.com/2011/02/training-load-what-is-it-why-is-it.html">http://westperformance.blogspot.com/2011/02/training-load-what-is-it-why-is-it.html</a>, fitted by observing polarpersonaltrainer.com and finally adapted to closely match results in polarpersonalrtainer.</font>

  </body>
</html>
