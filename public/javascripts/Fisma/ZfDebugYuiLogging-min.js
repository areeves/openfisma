(function(){YAHOO.util.Event.onDOMReady(function(){YAHOO.util.Event.throwErrors=true;var c=document.getElementById("zfdebug_yui_logging_tab");if(c){var b=new Fisma.ZfDebugYuiLogging(c)}});var a=function(b){var c=new YAHOO.widget.LogReader(b,{draggable:false,verboseOutput:false,width:"95%"});c.hideCategory("info");c.hideCategory("time");c.hideCategory("window");c.hideCategory("iframe")};Fisma.ZfDebugYuiLogging=a})();