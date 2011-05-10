Fisma.Search.Criteria=function(b,a){this.fields=a;this.searchPanel=b};Fisma.Search.Criteria.prototype={container:null,currentQueryType:null,fields:null,currentField:null,searchPanel:null,queryFieldContainer:null,queryTypeContainer:null,queryInputContainer:null,buttonsContainer:null,removeButton:null,enumValues:null,render:function(e,a,b){this.container=document.createElement("div");this.containerForm=document.createElement("form");this.containerForm.action="JavaScript: Fisma.Search.handleSearchEvent(this);";this.containerForm.enctype="application/x-www-form-urlencoded";this.containerForm.method="post";this.container.className="searchCriteria";this.buttonsContainer=document.createElement("span");this.buttonsContainer.className="searchQueryButtons";this.renderButtons(this.buttonsContainer);this.container.appendChild(this.buttonsContainer);this.queryFieldContainer=document.createElement("span");this.renderQueryField(this.queryFieldContainer,e);this.containerForm.appendChild(this.queryFieldContainer);this.queryTypeContainer=document.createElement("span");this.renderQueryType(this.queryTypeContainer,a);this.containerForm.appendChild(this.queryTypeContainer);this.queryInputContainer=document.createElement("span");this.renderQueryInput(this.queryInputContainer,b);this.containerForm.appendChild(this.queryInputContainer);var d=document.createElement("div");d.className="clear";this.containerForm.appendChild(d);var c=document.createElement("input");c.type="hidden";c.name="searchType";c.value="advanced";this.containerForm.appendChild(c);this.container.appendChild(this.containerForm);return this.container},renderQueryField:function(a,h){var f=this;var e=new Array();var c;var d=function(n,l,o){var m=o.cfg.getProperty("text");for(var k in f.fields){var p=f.fields[k];if(o.value==p.name){var j=true;var i=true;if(f.getCriteriaDefinition(p)==f.getCriteriaDefinition(f.currentField)){j=false}if("enum"==p.type){i=true}f.currentField=p;f.enumValues=p.enumValues;if(j){f.renderQueryType(f.queryTypeContainer)}if(i){f.renderQueryInput(f.queryInputContainer)}break}}c.set("label",p.label)};for(var b in this.fields){var g=this.fields[b];e.push({text:g.label,value:g.name,onclick:{fn:d}})}this.currentField=this.getField(h);c=new YAHOO.widget.Button({type:"menu",label:this.currentField.label,menu:e,container:a})},renderQueryType:function(b,c){if(b.firstChild){while(b.hasChildNodes()){b.removeChild(b.firstChild)}}var g=this;var d=function(n,l,o){var m=o.cfg.getProperty("text");var p=g.getCriteriaDefinition(g.currentField);var j=p[g.currentQueryType].renderer;var k=p[o.value].renderer;g.currentQueryType=o.value;if(j!=k||"enum"==g.currentField.type){g.renderQueryInput(g.queryInputContainer)}h.set("label",m)};var i=this.getCriteriaDefinition(this.currentField);var a=new Array();for(var f in i){var e=i[f];menuItem={text:e.label,value:f,onclick:{fn:d}};a.push(menuItem);if(e.isDefault){this.currentQueryType=f}}if(c){this.currentQueryType=c}var h=new YAHOO.widget.Button({type:"menu",label:i[this.currentQueryType].label,menu:a,container:b})},renderQueryInput:function(a,d){if(a.firstChild){while(a.hasChildNodes()){a.removeChild(a.firstChild)}}var c=this.getCriteriaDefinition(this.currentField);var b=c[this.currentQueryType].renderer;var e=Fisma.Search.CriteriaRenderer[b];if("enum"==this.currentField.type){e(a,d,this.currentField.enumValues)}else{e(a,d)}},renderButtons:function(a){var d=this;var b=new YAHOO.widget.Button({container:a});b._button.className="searchAddCriteriaButton";b._button.title="Click to add another search criteria";b.on("click",function(){d.searchPanel.addCriteria(d.container)});var c=new YAHOO.widget.Button({container:a});c._button.className="searchRemoveCriteriaButton";c._button.title="Click to remove this search criteria";c.on("click",function(){d.searchPanel.removeCriteria(d.container)});this.removeButton=c},getQuery:function(){return{field:this.currentField.name,operator:this.currentQueryType,operands:this.getOperands()}},getCriteriaDefinition:function(d){var c=d.type;if("datetime"==c){c="date"}else{if("text"==c){if(d.sortable){c="sortableText"}else{c="nonSortableText"}}}var b=Fisma.Search.CriteriaDefinition[c];if(d.extraCriteria){for(var a in d.extraCriteria){b[a]=d.extraCriteria[a]}}return b},setRemoveButtonEnabled:function(a){this.removeButton.set("disabled",!a)},getField:function(c){for(var a in this.fields){var b=this.fields[a];if(b.name==c){return b}}throw"No field found with this name: "+c},getOperands:function(){var b=this.getCriteriaDefinition(this.currentField);var a=b[this.currentQueryType].query;var c=Fisma.Search.CriteriaQuery[a];return c(this.queryInputContainer)},hasBlankOperands:function(){var a=this.getOperands();for(var b in a){if(""===$P.trim(a[b])){return true}}return false}};