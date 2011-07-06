Fisma.Search=function(){return{yuiDataTable:null,onSetTableCallback:null,testConfigurationActive:false,advancedSearchPanel:null,columnPreferencesSpinner:null,showDeletedRecords:false,searchPreferences:null,updateSearchPreferences:false,testConfiguration:function(){if(Fisma.Search.testConfigurationActive){return}Fisma.Search.testConfigurationActive=true;var b=document.getElementById("testConfiguration");YAHOO.util.Dom.addClass(b,"yui-button-disabled");var c=new Fisma.Spinner(b.parentNode);c.show();var a="csrf="+document.getElementById("csrfToken").value;YAHOO.util.Connect.asyncRequest("POST","/config/test-search/format/json",{success:function(e){var d=YAHOO.lang.JSON.parse(e.responseText).response;if(d.success){message("Search configuration is valid","notice",true)}else{message(d.message,"warning",true)}YAHOO.util.Dom.removeClass(b,"yui-button-disabled");Fisma.Search.testConfigurationActive=false;c.hide()},failure:function(d){message("Error: "+d.statusText,"warning");c.hide()}},a)},executeSearch:function(d,g){var e=Fisma.Search.yuiDataTable;var b={success:function(i,h,k){if(g){k.pagination.recordOffset=0}e.onDataReturnReplaceRows(i,h,k);var j=0;var l;do{l=e.getColumn(j);j++}while(l.formatter==Fisma.TableFormat.formatCheckbox);if(!YAHOO.lang.isUndefined(d.search)&&"Search"===d.search.value){e.get("paginator").setPage(1)}},failure:e.onDataReturnReplaceRows,scope:e,argument:e.getState()};try{var a=this.buildPostRequest(e.getState(),g);e.showTableMessage("Loading...");var f=e.getDataSource();f.connMethodPost=true;f.sendRequest(a,b)}catch(c){if("string"==typeof c){Fisma.Util.showAlertDialog(c)}}},handleSearchEvent:function(g){try{var d=new Fisma.Search.QueryState(g.modelName.value);var c={type:g.searchType.value};if(c.type==="advanced"){var b=Fisma.Search.advancedSearchPanel.getPanelState();var a={};for(var f in b){a[b[f].field]=b[f].operator}c.fields=a}Fisma.Search.updateSearchPreferences=true;Fisma.Search.searchPreferences=c;Fisma.Search.updateQueryState(d,g)}catch(h){message(h)}finally{Fisma.Search.executeSearch(g,true)}},updateQueryState:function(b,d){var c=YAHOO.util.Dom;var a=d.searchType.value;b.setSearchType(a);if(a==="simple"){b.setKeywords(d.keywords.value)}else{if(a==="advanced"){b.setAdvancedQuery(Fisma.Search.advancedSearchPanel.getPanelState())}}},getQuery:function(c){var b=document.getElementById("searchType").value;var d={queryType:b};if("simple"==b){d.keywords=c.keywords.value}else{if("advanced"==b){var a=this.advancedSearchPanel.getQuery();d.query=YAHOO.lang.JSON.stringify(a)}else{throw"Invalid value for search type: "+b}}d.showDeleted=this.showDeletedRecords;d.csrf=document.getElementById("searchForm").csrf.value;return d},convertQueryToPostData:function(c){var b=Array();for(var d in c){var e=c[d];b.push(d+"="+encodeURIComponent(e))}var a=b.join("&");return a},exportToFile:function(b,i){var c=document.getElementById("searchForm");var k=Fisma.Search.yuiDataTable;var a=k.getDataSource();var f=a.liveData;var d=document.createElement("form");d.method="post";d.action=f+"/format/"+i;d.style.display="none";var g=Fisma.Search.getQuery(c);for(var j in g){var h=g[j];var e=document.createElement("input");e.type="hidden";e.name=j;e.value=h;d.appendChild(e)}document.body.appendChild(d);d.submit()},generateRequest:function(b,d){var a="";try{a=Fisma.Search.buildPostRequest(b)}catch(c){if("string"==typeof c){message(c,"warning",true)}}d.getDataSource().connMethodPost=true;return a},buildPostRequest:function(b,f){var c=document.getElementById("searchType").value;var a={sort:b.sortedBy.key,dir:(b.sortedBy.dir=="yui-dt-asc"?"asc":"desc"),start:(f?0:b.pagination.recordOffset),count:b.pagination.rowsPerPage,csrf:document.getElementById("searchForm").csrf.value,showDeleted:Fisma.Search.showDeletedRecords,queryType:c};if("simple"==c){a.keywords=document.getElementById("keywords").value}else{if("advanced"==c){a.query=YAHOO.lang.JSON.stringify(Fisma.Search.advancedSearchPanel.getQuery())}else{throw"Invalid value for search type: "+c}}if(Fisma.Search.updateSearchPreferences){a.queryOptions=YAHOO.lang.JSON.stringify(Fisma.Search.searchPreferences)}var e=[];for(var d in a){e.push(d+"="+encodeURIComponent(a[d]))}return e.join("&")},highlightSearchResultsTable:function(d){var c=d.getTbodyEl();var b=c.getElementsByTagName("td");var a="***";Fisma.Highlighter.highlightDelimitedText(b,a)},toggleAdvancedSearchPanel:function(){var b=YAHOO.util.Dom;var c=YAHOO.widget.Button.getButton("advanced");var a=b.get("advancedSearch");if(a.style.display=="none"){a.style.display="block";b.get("keywords").style.visibility="hidden";b.get("searchType").value="advanced";c.set("checked",true)}else{a.style.display="none";b.get("keywords").style.visibility="visible";b.get("searchType").value="simple";c.set("checked",false);b.get("msgbar").style.display="none"}},toggleSearchColumnsPanel:function(){if(document.getElementById("searchColumns").style.display=="none"){document.getElementById("searchColumns").style.display="block"}else{document.getElementById("searchColumns").style.display="none"}},initializeSearchColumnsPanel:function(a){var k=document.getElementById("modelName").value,m=new Fisma.Search.TablePreferences(k),e=Fisma.Search.yuiDataTable.getColumnSet().keys,j="Column is visible. Click to hide column.",h="Column is hidden. Click to unhide column.";for(var i in e){var d=e[i],b=d.key;if(b==="deleteCheckbox"){continue}var l=!d.hidden;var c=new YAHOO.widget.Button({type:"checkbox",label:d.label,container:a,checked:l,onclick:{fn:function(q,r){var p=Fisma.Search.yuiDataTable,n=p.getColumn(r.name),o=this.get("checked");this.set("title",o?j:h);if(o){p.showColumn(n)}else{p.hideColumn(n)}r.prefs.setColumnVisibility(r.name,o)},obj:{name:b,prefs:m}}});c.set("title",l?j:h)}var g=document.createElement("div");var f=new YAHOO.widget.Button({type:"button",label:"Save Column Preferences",container:g,onclick:{fn:Fisma.Search.persistColumnPreferences}});if(!Fisma.Search.columnPreferencesSpinner){Fisma.Search.columnPreferencesSpinner=new Fisma.Spinner(g)}a.appendChild(g)},toggleMoreButton:function(){if(document.getElementById("moreSearchOptions").style.display=="none"){document.getElementById("moreSearchOptions").style.display="block"}else{document.getElementById("moreSearchOptions").style.display="none"}},persistColumnPreferences:function(){var a=document.getElementById("modelName").value,b=new Fisma.Search.TablePreferences(a);Fisma.Search.columnPreferencesSpinner.show();b.persist({success:function(c,d){Fisma.Search.columnPreferencesSpinner.hide();if(d.status==="ok"){message("Your column preferences have been saved","notice",true)}else{message(d.status,"warning",true)}},failure:function(c){Fisma.Search.columnPreferencesSpinner.hide();message("Error: "+c.statusText,"warning",true)}})},toggleShowDeletedRecords:function(){Fisma.Search.showDeletedRecords=!Fisma.Search.showDeletedRecords;var a=document.getElementById("searchForm");Fisma.Search.handleSearchEvent(a)},deleteSelectedRecords:function(){var g=[];var a=Fisma.Search.yuiDataTable;var c=a.getSelectedRows();for(var d=0;d<c.length;d++){var f=a.getRecord(c[d]);if(f){g.push(f.getData("id"))}}if(0===g.length){message("No records selected for deletion.","warning",true);return}var k=[];k.push(YAHOO.lang.JSON.stringify(g));var j="";if(1===g.length){j="Delete 1 record?"}else{j="Delete "+g.length+" records?"}var b={text:j,func:"Fisma.Search.doDelete",args:k};var h=null;Fisma.Util.showConfirmDialog(h,b)},doDelete:function(c){var e=Fisma.Search.yuiDataTable;var d=Fisma.Search.yuiDataTable.getDataSource().liveData;var g=d.split("/");g[g.length-1]="multi-delete";var f=g.join("/");var a={success:function(i,h,k){e.onDataReturnReplaceRows(i,h,k);var j=0;var l;do{l=e.getColumn(j);j++}while(l.formatter==Fisma.TableFormat.formatCheckbox);e.set("sortedBy",{key:l.key,dir:YAHOO.widget.DataTable.CLASS_ASC});e.get("paginator").setPage(1)},failure:e.onDataReturnReplaceRows,scope:e,argument:e.getState()};var b="csrf=";b+=document.getElementById("searchForm").csrf.value;b+="&records=";b+=c;YAHOO.util.Connect.asyncRequest("POST",f,{success:function(m){var j=[];if(m.responseText!==undefined){var i=YAHOO.lang.JSON.parse(m.responseText);message(i.msg,i.status,true)}var k=Fisma.Search.getQuery(document.getElementById("searchForm"));var h=Fisma.Search.convertQueryToPostData(k);e.showTableMessage("Loading...");var l=e.getDataSource();l.connMethodPost=true;l.sendRequest(h,a)},failure:function(i){var h="An error occurred while trying to delete the records.";h+=" The error has been logged for administrator review.";message(h,"warning",true)}},b)},setTable:function(a){this.yuiDataTable=a;if(this.onSetTableCallback){this.onSetTableCallback()}},onSetTable:function(a){this.onSetTableCallback=a;if(YAHOO.lang.isObject(this.yuiDataTable)){this.onSetTableCallback()}}}}();