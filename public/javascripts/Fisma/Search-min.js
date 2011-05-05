Fisma.Search=function(){return{yuiDataTable:null,onSetTableCallback:null,testConfigurationActive:false,advancedSearchPanel:null,columnPreferencesSpinner:null,showDeletedRecords:false,testConfiguration:function(){if(Fisma.Search.testConfigurationActive){return}Fisma.Search.testConfigurationActive=true;var a=document.getElementById("testConfiguration");a.className="yui-button yui-push-button yui-button-disabled";var c=new Fisma.Spinner(a.parentNode);c.show();var b=document.getElementById("search_config");YAHOO.util.Connect.setForm(b);YAHOO.util.Connect.asyncRequest("POST","/config/test-search/format/json",{success:function(e){var d=YAHOO.lang.JSON.parse(e.responseText).response;if(d.success){message("Search configuration is valid","notice",true)}else{message(d.message,"warning",true)}a.className="yui-button yui-push-button";Fisma.Search.testConfigurationActive=false;c.hide()},failure:function(d){message("Error: "+d.statusText,"warning");c.hide()}})},executeSearch:function(d){if(document.getElementById("advancedSearch").style.display=="none"){document.getElementById("searchType").value="simple"}document.getElementById("msgbar").style.display="none";var e=Fisma.Search.yuiDataTable;var b={success:function(h,g,j){e.onDataReturnReplaceRows(h,g,j);var i=0;var k;do{k=e.getColumn(i);i++}while(k.formatter==Fisma.TableFormat.formatCheckbox);e.set("sortedBy",{key:k.key,dir:YAHOO.widget.DataTable.CLASS_ASC});e.get("paginator").setPage(1,true)},failure:e.onDataReturnReplaceRows,scope:e,argument:e.getState()};try{var a=this.buildPostRequest(e.getState());e.showTableMessage("Loading...");var f=e.getDataSource();f.connMethodPost=true;f.sendRequest(a,b)}catch(c){if("string"==typeof c){alert(c)}}},handleSearchEvent:function(a){var c=YAHOO.util.Dom;var f=new Fisma.Search.QueryState(a.modelName.value);var g=a.searchType.value;f.setSearchType(g);if(g==="simple"){f.setKeywords(a.keywords.value)}else{if(g==="advanced"){var h=Fisma.Search.advancedSearchPanel.criteria;var b={};for(var d in h){var j=h[d];b[j.currentField.name]=j.currentQueryType}var e=Fisma.Search.advancedSearchPanel.getQuery();f.setAdvancedFields(b);f.setAdvancedQuery(e)}}Fisma.Search.executeSearch(a)},getQuery:function(c){var b=document.getElementById("searchType").value;var d={queryType:b};if("simple"==b){d.keywords=c.keywords.value}else{if("advanced"==b){var a=this.advancedSearchPanel.getQuery();d.query=YAHOO.lang.JSON.stringify(a)}else{throw"Invalid value for search type: "+b}}d.showDeleted=this.showDeletedRecords;d.csrf=document.getElementById("searchForm").csrf.value;return d},convertQueryToPostData:function(c){var b=Array();for(var d in c){var e=c[d];b.push(d+"="+encodeURIComponent(e))}var a=b.join("&");return a},exportToFile:function(b,i){var c=document.getElementById("searchForm");var k=Fisma.Search.yuiDataTable;var a=k.getDataSource();var f=a.liveData;var d=document.createElement("form");d.method="post";d.action=f+"/format/"+i;d.style.display="none";var g=Fisma.Search.getQuery(c);for(var j in g){var h=g[j];var e=document.createElement("input");e.type="hidden";e.name=j;e.value=h;d.appendChild(e)}document.body.appendChild(d);d.submit()},generateRequest:function(b,e){var d=document.getElementById("searchType").value;if(document.getElementById("advancedSearch").style.display=="none"){d="simple"}document.getElementById("msgbar").style.display="none";var a="";try{a=Fisma.Search.buildPostRequest(b)}catch(c){if("string"==typeof c){message(c,"warning",true)}}e.getDataSource().connMethodPost=true;return a},buildPostRequest:function(c){var d=document.getElementById("searchType").value;var a={sort:c.sortedBy.key,dir:(c.sortedBy.dir=="yui-dt-asc"?"asc":"desc"),start:c.pagination.recordOffset,count:c.pagination.rowsPerPage,csrf:document.getElementById("searchForm").csrf.value,showDeleted:Fisma.Search.showDeletedRecords,queryType:d};if("simple"==d){a.keywords=document.getElementById("keywords").value}else{if("advanced"==d){var b=Fisma.Search.advancedSearchPanel.getQuery();a.query=YAHOO.lang.JSON.stringify(b)}else{throw"Invalid value for search type: "+d}}var f=[];for(var e in a){f.push(e+"="+encodeURIComponent(a[e]))}return f.join("&")},highlightSearchResultsTable:function(d){var c=d.getTbodyEl();var b=c.getElementsByTagName("td");var a="***";Fisma.Highlighter.highlightDelimitedText(b,a)},toggleAdvancedSearchPanel:function(){var b=YAHOO.util.Dom;var c=YAHOO.widget.Button.getButton("advanced");var a=b.get("advancedSearch");if(a.style.display=="none"){a.style.display="block";b.get("keywords").style.visibility="hidden";b.get("searchType").value="advanced";c.set("checked",true)}else{a.style.display="none";b.get("keywords").style.visibility="visible";b.get("searchType").value="simple";c.set("checked",false);b.get("msgbar").style.display="none"}},toggleSearchColumnsPanel:function(){if(document.getElementById("searchColumns").style.display=="none"){document.getElementById("searchColumns").style.display="block"}else{document.getElementById("searchColumns").style.display="none"}},initializeSearchColumnsPanel:function(a){var k=document.getElementById("modelName").value,m=new Fisma.Search.TablePreferences(k),e=Fisma.Search.yuiDataTable.getColumnSet().keys,j="Column is visible. Click to hide column.",h="Column is hidden. Click to unhide column.";for(var i in e){var d=e[i],b=d.key;if(b==="deleteCheckbox"){continue}var l=!d.hidden;var c=new YAHOO.widget.Button({type:"checkbox",label:d.label,container:a,checked:l,onclick:{fn:function(q,r){var p=Fisma.Search.yuiDataTable,n=p.getColumn(r.name),o=this.get("checked");this.set("title",o?j:h);if(o){p.showColumn(n)}else{p.hideColumn(n)}r.prefs.setColumnVisibility(r.name,o)},obj:{name:b,prefs:m}}});c.set("title",l?j:h)}var g=document.createElement("div");var f=new YAHOO.widget.Button({type:"button",label:"Save Column Preferences",container:g,onclick:{fn:Fisma.Search.persistColumnPreferences}});if(!Fisma.Search.columnPreferencesSpinner){Fisma.Search.columnPreferencesSpinner=new Fisma.Spinner(g)}a.appendChild(g)},toggleMoreButton:function(){if(document.getElementById("moreSearchOptions").style.display=="none"){document.getElementById("moreSearchOptions").style.display="block"}else{document.getElementById("moreSearchOptions").style.display="none"}},persistColumnPreferences:function(){var a=document.getElementById("modelName").value,b=new Fisma.Search.TablePreferences(a);Fisma.Search.columnPreferencesSpinner.show();b.persist({success:function(c,d){Fisma.Search.columnPreferencesSpinner.hide();if(d.status==="ok"){message("Your column preferences have been saved","notice",true)}else{message(d.status,"warning",true)}},failure:function(c){Fisma.Search.columnPreferencesSpinner.hide();message("Error: "+c.statusText,"warning",true)}})},toggleShowDeletedRecords:function(){Fisma.Search.showDeletedRecords=!Fisma.Search.showDeletedRecords;var a=document.getElementById("searchForm");Fisma.Search.handleSearchEvent(a)},deleteSelectedRecords:function(){var j=[];var b=Fisma.Search.yuiDataTable;var d=b.getSelectedRows();for(var f=0;f<d.length;f++){var g=b.getRecord(d[f]);if(g){j.push(g.getData("id"))}}if(0===j.length){message("No records selected for deletion.","warning",true);return}if(!confirm("Delete "+j.length+" records?")){return}var h=Fisma.Search.yuiDataTable.getDataSource().liveData;var a=h.split("/");a[a.length-1]="multi-delete";var c=a.join("/");var e={success:function(l,i,n){b.onDataReturnReplaceRows(l,i,n);var m=0;var o;do{o=b.getColumn(m);m++}while(o.formatter==Fisma.TableFormat.formatCheckbox);b.set("sortedBy",{key:o.key,dir:YAHOO.widget.DataTable.CLASS_ASC});b.get("paginator").setPage(1,true)},failure:b.onDataReturnReplaceRows,scope:b,argument:b.getState()};var k="csrf=";k+=document.getElementById("searchForm").csrf.value;k+="&records=";k+=YAHOO.lang.JSON.stringify(j);YAHOO.util.Connect.asyncRequest("POST",c,{success:function(q){var m=[];if(q.responseText!==undefined){var l=YAHOO.lang.JSON.parse(q.responseText);message(l.msg,l.status,true)}var n=Fisma.Search.getQuery(document.getElementById("searchForm"));var i=Fisma.Search.convertQueryToPostData(n);b.showTableMessage("Loading...");var p=b.getDataSource();p.connMethodPost=true;p.sendRequest(i,e)},failure:function(l){var i="An error occurred while trying to delete the records.";i+=" The error has been logged for administrator review.";message(i,"warning",true)}},k)},setTable:function(a){this.yuiDataTable=a;if(this.onSetTableCallback){this.onSetTableCallback()}},onSetTable:function(a){this.onSetTableCallback=a}}}();