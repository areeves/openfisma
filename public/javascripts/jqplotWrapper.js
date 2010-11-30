/*
	bool chartObjData(obj)
	Creates a jgPlot chart based on the data in Obj

	Input
	   Obj[...]
	      Obj['uniqueid']		The name of the div for jqPlot create canvases inside of. The data within this div will be erased before chart plotting.
	      Obj['title']		The title to render above the chart
	      Obj['chartType']		String that must be "bar", "stackedbar", "line", or "pie"
	      Obj['chartData']		Array to pass to jqPlot as the data to plot (numbers).
	      Obj['chartDataText']	Array of labels (strings) for each data set in chartData (x-axis of bar charts)
	      Obj['concatXLabel']	Boolean that states if " (#)" should be concatinated at the end of each x-axis label (default=true)
	      Obj['chartLayerText']	Array of labels (strings) for each different line/layer in a milti-line-char or stacked-bar-chart
	      Obj['colors']		(optional) Array of colors for the chart to use across layers
	      Obj['links']		(optional) Array of links of which the browser should navigate to when a given data element is clicked
	      Obj['linksdebug']		(optional) Boolean, if set true, an alert box of what was clicked on will pop up instead of browser navigation based on Obj['links']

	Output
	   returns true on success, false on failure, or nothing if the success of the chart creation cannot be determind at that time (asynchronous mode)
*/
function createJQChart(param)
{

	// load in default values for paramiters, and replace it with any given params
	var defaultParams = {
			concatXLabel: false
		};
	param = jQuery.extend(true, defaultParams, param);

	// param validation
	if (document.getElementById(param['uniqueid']) == false) {
		alert('createJQChart Error - The target div/uniqueid does not exists');
		return false;
	}

	// clear the chart area
	document.getElementById(param['uniqueid']).innerHTML = '';

	// is the data being loaded from an external source? (Or is it all in the param obj?)
	if (param['externalSource']) {
		
		/*
		  If it is being loaded from an external source
		    setup a json request
		    have the json request return to createJQChart_asynchReturn
		    exit this function as createJQChart_asynchReturn will call this function again with the same param object with param['externalSource'] taken out
		*/

		document.getElementById(param['uniqueid']).innerHTML = 'Loading chart data...';

		var externalSource = param['externalSource'];
		delete param['externalSource'];
		
		var myDataSource = new YAHOO.util.DataSource(externalSource);
		myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
		myDataSource.responseSchema = {resultsList: "chart"};

		var callBackFunct = new Function ("requestNumber", "value", "exception", "createJQChart_asynchReturn(requestNumber, value, exception, " + JSON.stringify(param) + ");");

		var callback1 = {
			success : callBackFunct,
			failure : callBackFunct
		};
		myDataSource.sendRequest("", callback1);

		return;
	}

	// call the correct function based on chartType
	switch(param['chartType'])
	{
		case 'stackedbar':
			param['varyBarColor'] = false;
			param['showlegend'] = true;
			return createJQChart_StackedBar(param);
			break;
		case 'bar':
			param['chartData'] = [param['chartData']];
			param['links'] = [param['links']];
			param['varyBarColor'] = true;
			param['showlegend'] = false;
			return createJQChart_StackedBar(param);
			break;
		case 'line':
			return createChartJQStackedLine(param);
			break;
		case 'pie':
			param['links'] = [param['links']];
			return createChartJQPie(param);
			break;
		default:
			alert('createJQChart Error - chartType is invalid');
			return false;
	}
}

function createJQChart_asynchReturn(requestNumber, value, exception, param)
{
	if (value) {
		var joinedParam = jQuery.extend(true, param, value['results'][0],true);
		createJQChart(jQuery.extend(true, param, value));
	} else {
		alert('Error - Chart creation failed. Could not pull from data source.');
	}
}

function createChartJQPie2(param, labelsText, rawData)
{
	usedLabelsPie = param['chartDataText'];

	var dataSet = [];

	for (var x = 0; x < param['chartData'].length; x++) {
		param['chartDataText'][x] += ' (' + param['chartData'][x]  + ')';
		dataSet[dataSet.length] = [param['chartDataText'][x], param['chartData'][x]];
	}
	
	plot1 = $.jqplot('chart1', [dataSet], {
		grid: {
			drawBorder: false,
			drawGridlines: false,
			background: '#ffffff',
			shadow:false
		},
		axesDefaults: {

		},
		seriesDefaults:{
			renderer:$.jqplot.PieRenderer,
			rendererOptions: {
				showDataLabels: true
			}
		},
		legend: {
			show: true,
			rendererOptions: {
				numberRows: 1
			},
			location: 's'
		}

	});

	$('#chart1').bind('jqplotDataClick',
		function (ev, seriesIndex, pointIndex, data) {
			alert('You clicked on the section: ' + usedLabelsPie[pointIndex]);
		}
	);
}

function createChartJQPie(param)
{
	usedLabelsPie = param['chartDataText'];

	var dataSet = [];

	for (var x = 0; x < param['chartData'].length; x++) {
		param['chartDataText'][x] += ' (' + param['chartData'][x]  + ')';
		dataSet[dataSet.length] = [param['chartDataText'][x], param['chartData'][x]];
	}
	
	plot1 = $.jqplot(param['uniqueid'], [dataSet], {
		title: param['title'],
		seriesColors: param['colors'],
		grid: {
			drawBorder: false,
			drawGridlines: false,
			background: '#ffffff',
			shadow:true
		},
		seriesDefaults:{
			renderer:$.jqplot.PieRenderer,
			rendererOptions: {
				showDataLabels: true
			}
		},
		legend: {
			show: true,
			rendererOptions: {
				numberRows: 1
			},
			location: 's'
		}

	});

	// create an event handeling function that calls chartClickEvent while preserving the parm object
	var EvntHandler = new Function ("ev", "seriesIndex", "pointIndex", "data", "var thisChartParamObj = " + JSON.stringify(param) + "; chartClickEvent(ev, seriesIndex, pointIndex, data, thisChartParamObj);" );
	
	// use the created function as the click-event-handeler
	$('#' + param['uniqueid']).bind('jqplotDataClick', EvntHandler);

}

function createJQChart_StackedBar(param)
{
	var dataSet = [];
	var thisSum = 0;
	var maxSumOfAll = 0;

	for (var x = 0; x < param['chartDataText'].length; x++) {
	
		thisSum = 0;
		
		for (var y = 0; y < param['chartData'].length; y++) {
			thisSum += param['chartData'][y][x];
		}
		
		if (param['concatXLabel'] == true) { param['chartDataText'][x] += ' (' + thisSum  + ')'; }
		if (thisSum > maxSumOfAll) { maxSumOfAll = thisSum; }
	}

	var seriesParam = [];
	if (param['chartLayerText']) {
		for (x = 0; x < param['chartLayerText'].length; x++) {
			seriesParam[x] = {label: param['chartLayerText'][x]};
		}
	}

	var jPlotParamObj = {
		title: param['title'],
		seriesColors: param['colors'],
		stackSeries: true,
		series: seriesParam,
		seriesDefaults:{
			renderer: $.jqplot.BarRenderer,
			rendererOptions:{barMargin: 10, showDataLabels: true, varyBarColor: param['varyBarColor']},
			pointLabels:{show: true, location: 's'}
		},
		axes: {
			xaxis:{
				renderer: $.jqplot.CategoryAxisRenderer,
				ticks: param['chartDataText']
			},
			yaxis:{
				min: 0,
				max: Math.round(maxSumOfAll * 1.1)
			}

		},
		highlighter: { show: false },
		legend: {
					show: param['showlegend'],
					rendererOptions: {
						numberRows: 1
					},
					location: 'nw'
				}
	};

	plot1 = $.jqplot(param['uniqueid'], param['chartData'], jPlotParamObj);

	
	var EvntHandler = new Function ("ev", "seriesIndex", "pointIndex", "data", "var thisChartParamObj = " + JSON.stringify(param) + "; chartClickEvent(ev, seriesIndex, pointIndex, data, thisChartParamObj);" );
	$('#' + param['uniqueid']).bind('jqplotDataClick', EvntHandler);

}

function createChartJQStackedLine(param)
{
	var dataSet = [];
	var thisSum = 0;

	for (var x = 0; x < param['chartDataText'].length; x++) {
	
		thisSum = 0;
		
		for (var y = 0; y < ['chartData'].length; y++) {
			thisSum += ['chartData'][y][x];
		}
		
		param['chartDataText'][x] += ' (' + thisSum  + ')';
	}
    
	plot1 = $.jqplot(param['uniqueid'], ['chartData'], {
		title: param['title'],
		seriesColors: ["#F4FA58", "#FAAC58","#FA5858"],
		series: [{label: 'Open Findings', lineWidth:4, markerOptions:{style:'square'}}, {label: 'Closed Findings', lineWidth:4, markerOptions:{style:'square'}}, {lineWidth:4, markerOptions:{style:'square'}}],
		seriesDefaults:{
			fill:false,
			showMarker: true,
			showLine: true
		},
		axes: {
			xaxis:{
				renderer:$.jqplot.CategoryAxisRenderer,
				ticks:param['chartDataText']
			},
			yaxis:{
				min: 0
			}
		},
		highlighter: { show: false },
		legend: {
					show: true,
					rendererOptions: {
						numberRows: 1
					},
					location: 'nw'
				}
	});

	$('#' + param['uniqueid']).bind('jqplotDataClick',
		function (ev, seriesIndex, pointIndex, data) {
			alert('You clicked on bar-level ' + seriesIndex + ' in column: ' + pointIndex);
		}
	);

}

function chartClickEvent(ev, seriesIndex, pointIndex, data, paramObj) {
	
	var theLink = false;
	if (paramObj['links']) {
        if (typeof paramObj['links'] == 'string') {
            theLink = paramObj['links'];
        } else {
            if (paramObj['links'][seriesIndex]) {
                if (typeof paramObj['links'][seriesIndex] == "object") {
                    theLink = paramObj['links'][seriesIndex][pointIndex];
                } else {
                    theLink = paramObj['links'][seriesIndex];
                }
            }
        }
	}
    
    // Does the link contain a variable?
    if (theLink != false) {
        theLink = String(theLink).replace('#ColumnLabel#', paramObj['chartDataText'][pointIndex]);
    }
    
	if (paramObj['linksdebug'] == true) {
		var msg = "You clicked on layer " + seriesIndex + ", in column " + pointIndex + ", which has the data of " + data[1] + "\n";
		msg += "The link information for this element should be stored as a string in chartParamData['links'], or as a string in chartParamData['links'][" + seriesIndex + "][" + pointIndex + "]\n";
		if (theLink != false) { msg += "The link with this element is " + theLink; }
		alert(msg);
	} else {
		if (theLink != false) {
			document.location = theLink;
		}
	}
}