/*
	bool chartObjData(obj)
	Creates a jgPlot chart based on the data in Obj

	Input
	   Obj[...]
	      Obj['uniqueid']		The name of the div for jqPlot create canvases inside of. The data within this div will be erased before chart plotting.
	      Obj['externalSource'] An internal URL to load any of, or the rest of, the elements of this object. The content of the target URL given is expected to be a json responce. Any and all elements within the "chart" variable/object will be imported into this one.
	      Obj['title']          The title to render above the chart
	      Obj['chartType']      String that must be "bar", "stackedbar", "line", or "pie"
	      Obj['chartData']	    Array to pass to jqPlot as the data to plot (numbers).
	      Obj['chartDataText']	Array of labels (strings) for each data set in chartData (x-axis of bar charts)
	      Obj['concatXLabel']   Boolean that states if " (#)" should be concatinated at the end of each x-axis label (default=true)
	      Obj['chartLayerText']	Array of labels (strings) for each different line/layer in a milti-line-char or stacked-bar-chart
	      Obj['colors']         (optional) Array of colors for the chart to use across layers
	      Obj['links']          (optional) Array of links of which the browser should navigate to when a given data element is clicked
	      Obj['linksdebug']     (optional) Boolean, if set true, an alert box of what was clicked on will pop up instead of browser navigation based on Obj['links']

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

	// Ensure the load spinner is visible
	makeElementVisible(param['uniqueid'] + 'loader');

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
		if (!param['oldExternalSource']) { param['oldExternalSource'] = externalSource; }
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

	// clear the chart area
	document.getElementById(param['uniqueid']).innerHTML = '';
	document.getElementById(param['uniqueid']).className = '';

	// handel aliases and short-cut vars
	if (typeof param['barMargin'] != 'undefined') {
		param = jQuery.extend(true, param, {'seriesDefaults': {'rendererOptions': {'barMargin': param['barMargin']}}});
		delete param['barMargin'];
	}
	if (typeof param['legendLocation'] != 'undefined') {
		param = jQuery.extend(true, param, {'legend': {'location': param['legendLocation'] }});
		delete param['legendLocation'];
	}
	if (typeof param['legendRowCount'] != 'undefined') {
		param = jQuery.extend(true, param, {'legend': {'rendererOptions': {'numberRows': param['legendRowCount']}}});
		delete param['legendRowCount'];
	}
	
	// make sure the numbers to be plotted in param['chartData'] are infact numbers and not an array of strings of numbers
	param['chartData'] = forceIntegerArray(param['chartData']);

	// hide the loading spinner and show the canvas target
	document.getElementById(param['uniqueid'] + 'holder').style.display = '';
	fadeOut(param['uniqueid'] + 'holder', 0);

	fadeOut(param['uniqueid'] + 'loader', 500);
	document.getElementById(param['uniqueid'] + 'loader').style.position = 'absolute';
	document.getElementById(param['uniqueid'] + 'loader').finnishFadeCallback = new Function ("fadeIn('" + param['uniqueid'] + "holder', 500);");

	// call the correct function based on chartType
	switch(param['chartType'])
	{
		case 'stackedbar':
			param['varyBarColor'] = false;
			param['showlegend'] = true;
			var rtn = createJQChart_StackedBar(param);
			break;
		case 'bar':

			// Is this a simple-bar chart (not-stacked-bar) with multiple series?
			if (typeof param['chartData'][0] =='object') {

				// the chartData is already a multi dimensional array, and the chartType is bar, not stacked bar. So we assume it is a simple-bar chart with multi series
				// thus we will leave the chartData array as is (as opposed to forcing it to a 2 dim array, and claming it to be a stacked bar chart with no other layers of bars (a lazy but functional of creating a regular bar charts from the stacked-bar chart renderer)

				param['varyBarColor'] = false;
				param['showlegend'] = true;

			} else {
				param['chartData'] = [param['chartData']];	// force to 2 dimensional array
				param['links'] = [param['links']];
				param['varyBarColor'] = true;
				param['showlegend'] = false;
			}
			
			param['stackSeries'] = false;
			var rtn = createJQChart_StackedBar(param);
			break;

		case 'line':
			var rtn = createChartJQStackedLine(param);
			break;
		case 'pie':
			param['links'] = [param['links']];
			var rtn = createChartJQPie(param);
			break;
		default:
			alert('createJQChart Error - chartType is invalid');
			return false;
	}


	applyChartBackground(param);
	applyChartWidgets(param);

	return rtn;
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

function createChartJQPie(param)
{
	usedLabelsPie = param['chartDataText'];

	var dataSet = [];

	for (var x = 0; x < param['chartData'].length; x++) {
		param['chartDataText'][x] += ' (' + param['chartData'][x]  + ')';
		dataSet[dataSet.length] = [param['chartDataText'][x], param['chartData'][x]];
	}
	

	var jPlotParamObj = {
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
				sliceMargin: 2,
				showDataLabels: true,
				shadowAlpha: 0.15,
				shadowOffset: 3
			}
		},
                legend: {
			show: true,
			rendererOptions: {
				numberRows: 1
			},
			location: 's'
		}



	}

	// merge any jqPlot direct param-arguments into jPlotParamObj from param
	jPlotParamObj = jQuery.extend(true, jPlotParamObj, param);

	plot1 = $.jqplot(param['uniqueid'], [dataSet], jPlotParamObj);

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
			rendererOptions:{barMargin: 10, showDataLabels: true, varyBarColor: param['varyBarColor'], shadowAlpha: 0.15, shadowOffset: 3},
			pointLabels:{show: true, location: 's'}
		},
		axesDefaults: {
			tickRenderer: $.jqplot.CanvasAxisTickRenderer,
			tickOptions: {
				angle: -30,
				fontSize: '10pt'
			}
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

	// merge any jqPlot direct param-arguments into jPlotParamObj from param
	jPlotParamObj = jQuery.extend(true, jPlotParamObj, param);

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

function forceIntegerArray(inptArray) {
	for (var x = 0; x < inptArray.length; x++) {
		if (typeof inptArray[x] == 'object') {
			inptArray[x] = forceIntegerArray(inptArray[x]);
		} else {
			inptArray[x] = inptArray[x] * 1;	// make sure this is an int, and not a string of a number
		}
	}

	return inptArray;
}

function applyChartBackground(param) {

	var targDiv = document.getElementById(param['uniqueid']);

	// Dont display a background? Defined in either nobackground or background.nobackground
	if (param['nobackground']) {
		if (param['nobackground'] == true) { return; }
	}
	if (param['background']) {
		if (param['background']['nobackground']) {
			if (param['background']['nobackground'] == true) { return; }
		}
	}
	
	// What is the HTML we should inject?
	var backURL = '/images/logoShark.png'; // default location
	if (param['background']) { if (param['background']['URL']) { backURL = param['background']['URL']; } }
	var injectHTML = '<img height="100%" src="' + backURL + '" style="opacity:0.15;filter:alpha(opacity=15);opacity:0.15" />';

	// But wait, is there an override issued for the HTML of the background to inject?
	if (param['background']) {
		if (param['background']['overrideHTML']) {
			backURL = param['background']['overrideHTML'];
		}
	}

	// Where do we inject the background in the DOM? (different for differnt chart rederers)
	if (param['chartType'] == 'pie') {
		var cpy = targDiv.childNodes[3];
		var insertBeforeChild = targDiv.childNodes[4];
	} else {	
		var cpy = targDiv.childNodes[6];
		var insertBeforeChild = targDiv.childNodes[5];
	}

	var cpyStyl = cpy.style;

	injectedBackgroundImg = document.createElement('span');
	injectedBackgroundImg.setAttribute('align', 'center');
	injectedBackgroundImg.setAttribute('style' , 'position: absolute; left: ' + cpyStyl.left + '; top: ' + cpyStyl.top + '; width: ' + cpy.width + 'px; height: ' + cpy.height + 'px;');

	var inserted = targDiv.insertBefore(injectedBackgroundImg, insertBeforeChild);
	inserted.innerHTML = injectHTML;
}

function applyChartWidgets(param) {

	if (param['widgets']) {

		var wigSpace = document.getElementById(param['uniqueid'] + 'WidgetSpace');
		wigSpace.innerHTML = '';

		for (var x = 0; x < param['widgets'].length; x++) {

			var addHTML = '';
			var thisWidget = param['widgets'][x];
			
			// create a widget id if one is not explicitly given
			if (!thisWidget['uniqueid']) {
				thisWidget['uniqueid'] = param['uniqueid'] + '_widget' + x;
				param['widgets'][x]['uniqueid'] = thisWidget['uniqueid'];
			}

			// print the label text to be displayed to the left of the widget if one is given
			if (thisWidget['label']) {
				addHTML += thisWidget['label'] + ' ';
			}

			switch(thisWidget['type']) {
				case 'combo':

					addHTML += '<select id="' + thisWidget['uniqueid'] + '" onChange="widgetEvent(' + JSON.stringify(param).replace(/"/g, "'") + ');">';

					for (var y = 0; y < thisWidget['options'].length; y++) {
						
						addHTML += '<option value="' + thisWidget['options'][y] + '">' + thisWidget['options'][y] + '</option><br/>';
					}
					
					addHTML += '</select>';

					break;

				case 'text':
	
					addHTML += '<input onKeyDown="if(event.keyCode==13){widgetEvent(' + JSON.stringify(param).replace(/"/g, "'") + ');};" type="textbox" id="' + thisWidget['uniqueid'] + '" />'
					break;

				default:
					alert('Error - Widget ' + x + "'s type (" + thisWidget['type'] + ') is not a known widget type');
					return false;
			}

			// add this widget HTML to the DOM
			wigSpace.innerHTML += addHTML;
			
			// load the value for widget
			var thisWigInDOM = document.getElementById(thisWidget['uniqueid']);
			var thisWigValue = getCookie(thisWidget['uniqueid']);
			if (param['value']) {
				// this widget value is forced to a certain value upon every load/reload
				thisWigInDOM.value = param['value'];
			} else {
				if (thisWigValue != '') {
					// the value has been coosen in the past and is stored as a cookie
					thisWigInDOM.value = thisWigValue;
				} else {
					// no saved value/cookie. Is there a default given in the param object
					if (thisWidget['valuedefault']) {
						thisWigInDOM.value = thisWidget['valuedefault'];
					}
				}
			}
		}
	}

}

function widgetEvent(param) {

	// first, save the widget values (as cookies) so they can be retained later when the widgets get redrawn
	if (param['widgets']) {
		for (var x = 0; x < param['widgets'].length; x++) {
			var thisWidgetName = param['widgets'][x]['uniqueid'];
			var thisWidgetValue = document.getElementById(thisWidgetName).value;
			setCookie(thisWidgetName,thisWidgetValue,400);
		}
	}
	
	param['externalSource'] = param['oldExternalSource'];
	delete param['oldExternalSource'];

	// re-create chart entirly
	document.getElementById(param['uniqueid'] + 'holder').finnishFadeCallback = new Function ("makeElementVisible('" + param['uniqueid'] + "loader'); createJQChart(" + JSON.stringify(param) + "); this.finnishFadeCallback = '';");
	fadeOut(param['uniqueid'] + 'holder', 300);

}

function makeElementVisible(eleId) {
	var ele = document.getElementById(eleId);
	ele.style.opacity = '1';
	ele.style.filter = 'alpha(opacity = 100';
}

function makeElementInvisible(eleId) {
	var ele = document.getElementById(eleId);
	ele.style.opacity = '0';
	ele.style.filter = 'alpha(opacity = 0';
}

function fadeIn(eid, TimeToFade) {

	var element = document.getElementById(eid);
	if (element == null) return;

	delete element.FadeState;
	delete element.FadeTimeLeft;
	element.style.opacity = '0';
	element.style.filter = "alpha(opacity = '0')";

	fade(eid, TimeToFade);
}

function fadeOut(eid, TimeToFade) {

	var element = document.getElementById(eid);
	if (element == null) return;

	delete element.FadeState;
	delete element.FadeTimeLeft;
	element.style.opacity = '1';
	element.style.filter = "alpha(opacity = '100')";

	fade(eid, TimeToFade);
}

function fade(eid, TimeToFade) {

	var element = document.getElementById(eid);
	if (element == null) return;

	element.style = '';

	if(element.FadeState == null)
	{
		if(element.style.opacity == null || element.style.opacity == '' || element.style.opacity == '1')
		{
			element.FadeState = 2;
		} else {
			element.FadeState = -2;
		}
	}

	if(element.FadeState == 1 || element.FadeState == -1) {
		element.FadeState = element.FadeState == 1 ? -1 : 1;
		element.FadeTimeLeft = TimeToFade - element.FadeTimeLeft;
	} else {
		element.FadeState = element.FadeState == 2 ? -1 : 1;
		element.FadeTimeLeft = TimeToFade;
		setTimeout("animateFade(" + new Date().getTime() + ",'" + eid + "'," + TimeToFade + ")", 33);
	}  
}


function animateFade(lastTick, eid, TimeToFade)
{  
	var curTick = new Date().getTime();
	var elapsedTicks = curTick - lastTick;

	var element = document.getElementById(eid);

	if(element.FadeTimeLeft <= elapsedTicks)
	{
		element.style.opacity = element.FadeState == 1 ? '1' : '0';
		element.style.filter = 'alpha(opacity = ' + (element.FadeState == 1 ? '100' : '0') + ')';
		element.FadeState = element.FadeState == 1 ? 2 : -2;
		if (element.finnishFadeCallback) { element.finnishFadeCallback(); }
		return;
	}

	element.FadeTimeLeft -= elapsedTicks;
	var newOpVal = element.FadeTimeLeft/TimeToFade;
	if(element.FadeState == 1) newOpVal = 1 - newOpVal;

	element.style.opacity = newOpVal;
	element.style.filter = 'alpha(opacity = ' + (newOpVal*100) + ')';

	setTimeout("animateFade(" + curTick + ",'" + eid + "'," + TimeToFade + ")", 33);
}

function setCookie(c_name,value,expiredays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate()+expiredays);
	document.cookie = c_name + "=" + escape(value)+((expiredays==null) ? "" : ";expires="+exdate.toUTCString());
}

function getCookie(c_name)
{
	if (document.cookie.length>0) {

		c_start=document.cookie.indexOf(c_name + "=");

		if (c_start!=-1) {
			c_start = c_start + c_name.length + 1;
			c_end = document.cookie.indexOf(";",c_start);
			if (c_end==-1) c_end=document.cookie.length;
			return unescape(document.cookie.substring(c_start,c_end));
		}
	}

	return '';
}


