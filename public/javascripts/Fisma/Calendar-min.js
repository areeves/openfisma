Fisma.Calendar=function(){return{addCalendarPopupToTextField:function(d){var b=document.createElement("div");b.style.position="absolute";b.style.zIndex=99;d.parentNode.appendChild(b);var c=YAHOO.util.Dom.getRegion(d);var f=[c.left,c.bottom+5];YAHOO.util.Dom.setXY(b,f);var e=new YAHOO.widget.Calendar(b,{close:true});e.hide();setTimeout(function(){e.render()},0);d.onfocus=function(){e.show()};var a=function(k,h,m){var j=h[0][0];var i=j[0],l=""+j[1],g=""+j[2];if(1==l.length){l="0"+l}if(1==g.length){g="0"+g}d.value=i+"-"+l+"-"+g;e.hide()};e.selectEvent.subscribe(a,e,true)}}}();