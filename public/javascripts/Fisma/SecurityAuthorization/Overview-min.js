(function(){var a=function(b){this._steps=b;a.superclass.constructor.call(this)};YAHOO.lang.extend(a,Object,{_steps:null,render:function(b){var f=document.createElement("table");f.className="saOverview";var l=document.createElement("tr");var h=document.createElement("th");h.appendChild(document.createTextNode("Step"));l.appendChild(h);var k=document.createElement("th");k.appendChild(document.createTextNode("Status"));l.appendChild(k);f.appendChild(l);for(var j in this._steps){var d=this._steps[j];var e=document.createElement("tr");var c=document.createElement("td");var g=document.createElement("a");g.href="#";g.appendChild(document.createTextNode(d.name));c.appendChild(g);e.appendChild(c);g.onclick=(function(i){return function(){Fisma.tabView.selectTab(i)}})(d.stepNumber);c=this._renderStatusCellForStep(d);e.appendChild(c);f.appendChild(e);d.statusTd=c}b.appendChild(f)},updateStepProgress:function(b,d,g){var f=this._steps[b-1];if(YAHOO.lang.isUndefined(f)){throw"Could not find step #"+b}if(YAHOO.lang.isValue(d)){f.numerator=d}if(YAHOO.lang.isValue(g)){f.denominator=g}var c=this._renderStatusCellForStep(f);var e=f.statusTd.parentNode;e.replaceChild(c,f.statusTd);f.statusTd=c},incrementStepNumerator:function(b,d){var c=this._steps[b-1];var d=d||1;this.updateStepProgress(b,c.numerator+d,null)},incrementStepDenominator:function(b,d){var c=this._steps[b-1];var d=d||1;this.updateStepProgress(b,null,c.denominator+d)},_renderStatusCellForStep:function(d){var g=document.createElement("td");var c=document.createElement("span");var e=d.numerator;var b=d.denominator;if(b==0){e=0;b=1}c.appendChild(document.createTextNode(e+"/"+b));jQuery(c).peity("pie");g.appendChild(c);var f=" "+d.completed;if(d.stepNumber==3||d.stepNumber==4){f+=" ("+d.numerator+" of "+d.denominator+")"}g.appendChild(document.createTextNode(f));return g}});Fisma.SecurityAuthorization.Overview=a})();