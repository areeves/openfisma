Fisma.Search=function(){return{yuiDataTable:null,onSetTableCallback:null,testConfigurationActive:false,advancedSearchPanel:null,columnPreferencesSpinner:null,showDeletedRecords:false,testConfiguration:function(){if(Fisma.Search.testConfigurationActive){return}Fisma.Search.testConfigurationActive=true;var a=document.getElementById("testConfiguration");a.className="yui-button yui-push-button yui-button-disabled";var c=new Fisma.Spinner(a.parentNode);c.show();var b=document.getElementById("search_config");YAHOO.util.Connect.setForm(b);YAHOO.util.Connect.asyncRequest("POST","/config/test-search/format/json",{success:function(e){var d=YAHOO.lang.JSON.parse(e.responseText).response;if(d.success){message("Search configuration is valid","notice",true)}else{message(d.message,"warning",true)}a.className="yui-button yui-push-button";Fisma.Search.testConfigurationActive=false;c.hide()},failure:function(d){message("Error: "+d.statusText,"warning");c.hide()}})},handleSearchEvent:function(c){var e=Fisma.Search.yuiDataTable;var b={success:function(h,g,j){e.onDataReturnReplaceRows(h,g,j);var i=0;var k;do{k=e.getColumn(i);i++}while(k.formatter==Fisma.TableFormat.formatCheckbox);e.set("sortedBy",{key:k.key,dir:YAHOO.widget.DataTable.CLASS_ASC});e.get("paginator").setPage(1,true)},failure:e.onDataReturnReplaceRows,scope:e,argument:e.getState()};var d=this.getQuery(c);var a=this.convertQueryToPostData(d);e.showTableMessage("Loading...");var f=e.getDataSource();f.connMethodPost=true;f.sendRequest(a,b)},getQuery:function(c){var b=document.getElementById("searchType").value;var d={queryType:b};if("simple"==b){d.keywords=c.keywords.value}else{if("advanced"==b){var a=this.advancedSearchPanel.getQuery();d.query=YAHOO.lang.JSON.stringify(a)}else{throw"Invalid value for search type: "+b}}d.showDeleted=this.showDeletedRecords;d.csrf=document.getElementById("searchForm").csrf.value;return d},convertQueryToPostData:function(c){var b=Array();for(var d in c){var e=c[d];b.push(d+"="+encodeURIComponent(e))}var a=b.join("&");return a},exportToFile:function(b,i){var c=document.getElementById("searchForm");var k=Fisma.Search.yuiDataTable;var a=k.getDataSource();var f=a.liveData;var d=document.createElement("form");d.method="post";d.action=f+"/format/"+i;d.style.display="none";var g=Fisma.Search.getQuery(c);for(var j in g){var h=g[j];var e=document.createElement("input");e.type="hidden";e.name=j;e.value=h;d.appendChild(e)}document.body.appendChild(d);d.submit()},handleYuiDataTableEvent:function(c,e){var d=document.getElementById("searchType").value;var a="sort="+c.sortedBy.key+"&dir="+(c.sortedBy.dir=="yui-dt-asc"?"asc":"desc")+"&start="+c.pagination.recordOffset+"&count="+c.pagination.rowsPerPage+"&csrf="+document.getElementById("searchForm").csrf.value;if("simple"==d){a+="&queryType=simple&keywords="+document.getElementById("keywords").value}else{if("advanced"==d){var b=Fisma.Search.advancedSearchPanel.getQuery();a+="&queryType=advanced&query="+YAHOO.lang.JSON.stringify(b)}else{throw"Invalid value for search type: "+d}}a+="&showDeleted="+Fisma.Search.showDeletedRecords;e.getDataSource().connMethodPost=true;return a},highlightSearchResultsTable:function(d){var d=Fisma.Search.yuiDataTable;var c=d.getTbodyEl();var b=c.getElementsByTagName("td");var a="***";Fisma.Highlighter.highlightDelimitedText(b,a)},toggleAdvancedSearchPanel:function(){if(document.getElementById("advancedSearch").style.display=="none"){document.getElementById("advancedSearch").style.display="block";document.getElementById("keywords").style.visibility="hidden";document.getElementById("searchType").value="advanced"}else{document.getElementById("advancedSearch").style.display="none";document.getElementById("keywords").style.visibility="visible";document.getElementById("searchType").value="simple"}},toggleSearchColumnsPanel:function(){if(document.getElementById("searchColumns").style.display=="none"){document.getElementById("searchColumns").style.display="block"}else{document.getElementById("searchColumns").style.display="none"}},initializeSearchColumnsPanel:function(a,k){var j=document.getElementById("modelName").value;var l=j+"Columns";var b=YAHOO.util.Cookie.get(l);var i=0;for(var g in k){var n=k[g];if(n.hidden===true){continue}var m=n.initiallyVisible;if(b){m=(b&1<<i)!=0}i++;var h="Column is visible. Click to hide column.";var f="Column is hidden. Click to unhide column.";var c=new YAHOO.widget.Button({type:"checkbox",label:n.label,container:a,checked:m,onclick:{fn:function(q,r){this.set("title",this.get("checked")?h:f);var p=Fisma.Search.yuiDataTable;var o=p.getColumn(r);if(this.get("checked")){p.showColumn(o)}else{p.hideColumn(o)}Fisma.Search.saveColumnCookies()},obj:n.name}});c.set("title",m?h:f)}var e=document.createElement("div");e.style.marginLeft="20px";e.style.marginBottom="20px";e.style["float"]="right";var d=new YAHOO.widget.Button({type:"button",label:"Save Column Preferences",container:e,onclick:{fn:Fisma.Search.persistColumnCookie}});if(!Fisma.Search.columnPreferencesSpinner){Fisma.Search.columnPreferencesSpinner=new Fisma.Spinner(e)}a.appendChild(e)},toggleMoreButton:function(){if(document.getElementById("moreSearchOptions").style.display=="none"){document.getElementById("moreSearchOptions").style.display="block"}else{document.getElementById("moreSearchOptions").style.display="none"}},saveColumnCookies:function(){var f=Fisma.Search.yuiDataTable;var e=f.getColumnSet().keys;var c=0;var b=0;for(var d in e){if(e[d].formatter==Fisma.TableFormat.formatCheckbox){continue}if(!e[d].hidden){c|=1<<b}b++}var a=document.getElementById("modelName").value;var g=a+"Columns";YAHOO.util.Cookie.set(g,c,{path:"/",secure:location.protocol=="https"})},persistColumnCookie:function(){Fisma.Search.saveColumnCookies();var a=document.getElementById("modelName").value;var c=a+"Columns";var b=YAHOO.util.Cookie.get(c);Fisma.Search.columnPreferencesSpinner.show();YAHOO.util.Connect.asyncRequest("GET","/user/set-cookie/name/"+c+"/value/"+b+"/format/json",{success:function(e){Fisma.Search.columnPreferencesSpinner.hide();var d=YAHOO.lang.JSON.parse(e.responseText);if(d.success){message("Your column preferences have been saved","notice")}else{message(d.message,"warning")}},failure:function(d){Fisma.Search.columnPreferencesSpinner.hide();message("Error: "+d.statusText,"warning")}})},toggleShowDeletedRecords:function(){Fisma.Search.showDeletedRecords=!Fisma.Search.showDeletedRecords;var a=document.getElementById("searchForm");Fisma.Search.handleSearchEvent(a)},deleteSelectedRecords:function(){var c=[];var e=Fisma.Search.yuiDataTable;var f=e.getSelectedRows();for(var b=0;b<f.length;b++){var a=e.getRecord(f[b]);if(a){c.push(a.getData("id"))}}if(0==c.length){message("No records selected for deletion.","warning",true);return}if(!confirm("Delete "+c.length+" records?")){return}var d=Fisma.Search.yuiDataTable.getDataSource().liveData;var h=d.split("/");h[h.length-1]="multi-delete";var g=h.join("/");YAHOO.util.Connect.asyncRequest("POST",g,{success:function(l){var k=[];if(l.responseText!==undefined){var i=YAHOO.lang.JSON.parse(l.responseText);message(i.msg,i.status,true)}var j=document.getElementById("searchForm");Fisma.Search.handleSearchEvent(j)},failure:function(j){var i="An error occurred while trying to delete the records. The error has been logged for administrator review.";message(i,"warning",true)}},"records="+YAHOO.lang.JSON.stringify(c))},setTable:function(a){this.yuiDataTable=a;if(this.onSetTableCallback){this.onSetTableCallback()}},onSetTable:function(a){this.onSetTableCallback=a}}}();