Fisma.Search=function(){return{yuiDataTable:null,onSetTableCallback:null,testConfigurationActive:false,advancedSearchPanel:null,columnPreferencesSpinner:null,showDeletedRecords:false,testConfiguration:function(){if(Fisma.Search.testConfigurationActive){return}Fisma.Search.testConfigurationActive=true;var a=document.getElementById("testConfiguration");a.className="yui-button yui-push-button yui-button-disabled";var c=new Fisma.Spinner(a.parentNode);c.show();var b=document.getElementById("search_config");YAHOO.util.Connect.setForm(b);YAHOO.util.Connect.asyncRequest("POST","/config/test-search/format/json",{success:function(e){var d=YAHOO.lang.JSON.parse(e.responseText).response;if(d.success){message("Search configuration is valid","notice",true)}else{message(d.message,"warning",true)}a.className="yui-button yui-push-button";Fisma.Search.testConfigurationActive=false;c.hide()},failure:function(d){message("Error: "+d.statusText,"warning");c.hide()}})},handleSearchEvent:function(d){if(document.getElementById("advancedSearch").style.display=="none"){document.getElementById("searchType").value="simple"}document.getElementById("msgbar").style.display="none";var f=Fisma.Search.yuiDataTable;var b={success:function(i,h,k){f.onDataReturnReplaceRows(i,h,k);var j=0;var l;do{l=f.getColumn(j);j++}while(l.formatter==Fisma.TableFormat.formatCheckbox);f.set("sortedBy",{key:l.key,dir:YAHOO.widget.DataTable.CLASS_ASC});f.get("paginator").setPage(1,true)},failure:f.onDataReturnReplaceRows,scope:f,argument:f.getState()};try{var e=this.getQuery(d);var a=this.convertQueryToPostData(e);f.showTableMessage("Loading...");var g=f.getDataSource();g.connMethodPost=true;g.sendRequest(a,b)}catch(c){if("string"==typeof c){alert(c)}}},getQuery:function(c){var b=document.getElementById("searchType").value;var d={queryType:b};if("simple"==b){d.keywords=c.keywords.value}else{if("advanced"==b){var a=this.advancedSearchPanel.getQuery();d.query=YAHOO.lang.JSON.stringify(a)}else{throw"Invalid value for search type: "+b}}d.showDeleted=this.showDeletedRecords;d.csrf=document.getElementById("searchForm").csrf.value;return d},convertQueryToPostData:function(c){var b=Array();for(var d in c){var e=c[d];b.push(d+"="+encodeURIComponent(e))}var a=b.join("&");return a},exportToFile:function(b,i){var c=document.getElementById("searchForm");var k=Fisma.Search.yuiDataTable;var a=k.getDataSource();var f=a.liveData;var d=document.createElement("form");d.method="post";d.action=f+"/format/"+i;d.style.display="none";var g=Fisma.Search.getQuery(c);for(var j in g){var h=g[j];var e=document.createElement("input");e.type="hidden";e.name=j;e.value=h;d.appendChild(e)}document.body.appendChild(d);d.submit()},handleYuiDataTableEvent:function(c,f){var e=document.getElementById("searchType").value;if(document.getElementById("advancedSearch").style.display=="none"){e="simple"}document.getElementById("msgbar").style.display="none";var a="sort="+c.sortedBy.key+"&dir="+(c.sortedBy.dir=="yui-dt-asc"?"asc":"desc")+"&start="+c.pagination.recordOffset+"&count="+c.pagination.rowsPerPage+"&csrf="+document.getElementById("searchForm").csrf.value;try{if("simple"==e){a+="&queryType=simple&keywords=";a+=encodeURIComponent(document.getElementById("keywords").value)}else{if("advanced"==e){var b=Fisma.Search.advancedSearchPanel.getQuery();a+="&queryType=advanced&query=";a+=encodeURIComponent(YAHOO.lang.JSON.stringify(b))}else{throw"Invalid value for search type: "+e}}}catch(d){if("string"==typeof d){message(d,"warning",true)}}a+="&showDeleted="+Fisma.Search.showDeletedRecords;f.getDataSource().connMethodPost=true;return a},highlightSearchResultsTable:function(d){var c=d.getTbodyEl();var b=c.getElementsByTagName("td");var a="***";Fisma.Highlighter.highlightDelimitedText(b,a)},toggleAdvancedSearchPanel:function(){if(document.getElementById("advancedSearch").style.display=="none"){document.getElementById("advancedSearch").style.display="block";document.getElementById("keywords").style.visibility="hidden";document.getElementById("searchType").value="advanced"}else{document.getElementById("advancedSearch").style.display="none";document.getElementById("keywords").style.visibility="visible";document.getElementById("searchType").value="simple";document.getElementById("msgbar").style.display="none"}},toggleSearchColumnsPanel:function(){if(document.getElementById("searchColumns").style.display=="none"){document.getElementById("searchColumns").style.display="block"}else{document.getElementById("searchColumns").style.display="none"}},initializeSearchColumnsPanel:function(a,j,l){var i=document.getElementById("modelName").value,n=new Fisma.Search.TablePreferences(i),h="Column is visible. Click to hide column.",f="Column is hidden. Click to unhide column.";for(var g in j){var m=j[g],b=m.name;if(m.hidden===true){continue}var k=n.getColumnVisibility(b,l[b]);var c=new YAHOO.widget.Button({type:"checkbox",label:m.label,container:a,checked:k,onclick:{fn:function(r,s){var q=Fisma.Search.yuiDataTable,o=q.getColumn(s.name),p=this.get("checked");this.set("title",p?h:f);if(p){q.showColumn(o)}else{q.hideColumn(o)}s.prefs.setColumnVisibility(s.name,p)},obj:{name:b,prefs:n}}});c.set("title",k?h:f)}var e=document.createElement("div");var d=new YAHOO.widget.Button({type:"button",label:"Save Column Preferences",container:e,onclick:{fn:Fisma.Search.persistColumnPreferences}});if(!Fisma.Search.columnPreferencesSpinner){Fisma.Search.columnPreferencesSpinner=new Fisma.Spinner(e)}a.appendChild(e)},toggleMoreButton:function(){if(document.getElementById("moreSearchOptions").style.display=="none"){document.getElementById("moreSearchOptions").style.display="block"}else{document.getElementById("moreSearchOptions").style.display="none"}},persistColumnPreferences:function(){var a=document.getElementById("modelName").value,b=new Fisma.Search.TablePreferences(a);Fisma.Search.columnPreferencesSpinner.show();b.persist({success:function(c,d){Fisma.Search.columnPreferencesSpinner.hide();if(d.status==="ok"){message("Your column preferences have been saved","notice",true)}else{message(d.status,"warning",true)}},failure:function(c){Fisma.Search.columnPreferencesSpinner.hide();message("Error: "+c.statusText,"warning",true)}})},toggleShowDeletedRecords:function(){Fisma.Search.showDeletedRecords=!Fisma.Search.showDeletedRecords;var a=document.getElementById("searchForm");Fisma.Search.handleSearchEvent(a)},deleteSelectedRecords:function(){var j=[];var b=Fisma.Search.yuiDataTable;var d=b.getSelectedRows();for(var f=0;f<d.length;f++){var g=b.getRecord(d[f]);if(g){j.push(g.getData("id"))}}if(0===j.length){message("No records selected for deletion.","warning",true);return}if(!confirm("Delete "+j.length+" records?")){return}var h=Fisma.Search.yuiDataTable.getDataSource().liveData;var a=h.split("/");a[a.length-1]="multi-delete";var c=a.join("/");var e={success:function(l,i,n){b.onDataReturnReplaceRows(l,i,n);var m=0;var o;do{o=b.getColumn(m);m++}while(o.formatter==Fisma.TableFormat.formatCheckbox);b.set("sortedBy",{key:o.key,dir:YAHOO.widget.DataTable.CLASS_ASC});b.get("paginator").setPage(1,true)},failure:b.onDataReturnReplaceRows,scope:b,argument:b.getState()};var k="csrf=";k+=document.getElementById("searchForm").csrf.value;k+="&records=";k+=YAHOO.lang.JSON.stringify(j);YAHOO.util.Connect.asyncRequest("POST",c,{success:function(q){var m=[];if(q.responseText!==undefined){var l=YAHOO.lang.JSON.parse(q.responseText);message(l.msg,l.status,true)}var n=Fisma.Search.getQuery(document.getElementById("searchForm"));var i=Fisma.Search.convertQueryToPostData(n);b.showTableMessage("Loading...");var p=b.getDataSource();p.connMethodPost=true;p.sendRequest(i,e)},failure:function(l){var i="An error occurred while trying to delete the records.";i+=" The error has been logged for administrator review.";message(i,"warning",true)}},k)},setTable:function(a){this.yuiDataTable=a;if(this.onSetTableCallback){this.onSetTableCallback()}},onSetTable:function(a){this.onSetTableCallback=a}}}();