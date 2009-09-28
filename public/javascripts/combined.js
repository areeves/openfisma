/*************************************************************************
 *
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenFISMA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenFISMA.  If not, see <http://www.gnu.org/licenses/>.
 *
 ******************************************************************************* 
 *
 * Main js file
 * @todo start migrating functionality out of this file. 
 * eventually this file needs to be removed 
 *
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id: fisma.js 2238 2009-09-16 22:24:04Z josh-boyd $
 *
 *******************************************************************************
 */

String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g,"");
}

var readyFunc = function () {
    var calendars = YAHOO.util.Selector.query('.date');
    for(var i = 0; i < calendars.length; i ++) {
        YAHOO.util.Event.on(calendars[i].getAttribute('id')+'_show', 'click', callCalendar, calendars[i].getAttribute('id'));
    }
    
    // switch Aging Totals or Date Opened and End 
    var searchAging = function (){
        if (document.getElementById('remediationSearchAging')) {
            var value = document.getElementById('remediationSearchAging').value.trim();
        } else {
            return ;
        }
        var dateBegin = document.getElementById('created_date_begin');
        var dateEnd = document.getElementById('created_date_end');
        if (value == '0') {
            dateBegin.disabled = false;
            dateEnd.disabled = false;
            document.getElementById('show1').disabled = false;
            document.getElementById('show2').disabled = false;
        } else {
            dateBegin.disabled = true;
            dateEnd.disabled = true;
            dateBegin.value = '';
            dateEnd.value = '';
            document.getElementById('show1').disabled = true;
            document.getElementById('show2').disabled = true;
        }
    }
    YAHOO.util.Event.on('remediationSearchAging', 'change', searchAging);
    searchAging();
    
    var searchAging1 = function (){
        if (document.getElementById('created_date_begin') 
                && document.getElementById('created_date_end')) {
            var value1 = document.getElementById('created_date_begin').value.trim();
            var value2 = document.getElementById('created_date_end').value.trim();
        } else {
            return ;
        } 
        if(value1 != '' || value2 != '') {
            document.getElementById('remediationSearchAging').disabled = true;
        } else {
            document.getElementById('remediationSearchAging').disabled = false;
        }
    }
    YAHOO.util.Event.on('created_date_begin', 'change', searchAging1);
    YAHOO.util.Event.on('created_date_end', 'change', searchAging1);
    searchAging1();
    
    var changeEncrypt = function () {
        if (document.getElementById('encrypt')) {
            var obj = document.getElementById('encrypt');
        } else {
            return;
        }
        var value = obj.value.trim();
        if (value == 'sha256') {
             obj.style.display = '';
        } else {
             obj.style.display = 'none';
        }
    }
    YAHOO.util.Event.on('encrypt', 'change', changeEncrypt);
    changeEncrypt();

    //
    YAHOO.util.Event.on('function_screen', 'change', search_function);
    search_function();
    //
    YAHOO.util.Event.on('add_function', 'click', function() {
        var options = new YAHOO.util.Selector.query('#available_functions option');
        for (var i = 0; i < options.length; i ++) {
            if (options[i].selected == true) {
                document.getElementById('exist_functions').appendChild(options[i]);
            }
        }
        return false;  
    });
    //
    YAHOO.util.Event.on('remove_function', 'click', function() {
        var options = YAHOO.util.Selector.query('#exist_functions option');
        for (var i = 0; i < options.length; i ++) {
            if (options[i].selected == true) {
                document.getElementById('available_functions').appendChild(options[i]);
            }
        }
        return false;
    });
    //
    YAHOO.util.Event.on(YAHOO.util.Selector.query('form[name=assign_right]'), 'submit', 
    function (){
        var options = YAHOO.util.Selector.query('#exist_functions option');
        for (var i = 0; i < options.length; i ++) {
            options[i].selected = true;
        }
    });
    //
    YAHOO.util.Event.on('searchAsset', 'click', searchAsset);
    //
    YAHOO.util.Event.on('search_product' ,'click', searchProduct);
    //
    YAHOO.util.Event.on(YAHOO.util.Selector.query('.confirm'), 'click', function(){
        var str = "DELETING CONFIRMATION!";
        if(confirm(str) == true){
            return true;
        }
        return false;
    });
    //
    asset_detail();
    //
    getProdId();
}

function search_function() {
    var trigger = YAHOO.util.Selector.query('select[name=function_screen]');
    if (trigger == ''){return;}
    var param = name = '';
    var options = YAHOO.util.Selector.query('select[name=function_screen] option');

    for (var i = 0; i < options.length; i++) {
        if (options[i].selected == true) {
            name = options[i].text;
        }
    }
    if('' != name){
        param += '/screen_name/'+name;
    }
    var kids = YAHOO.util.Selector.query('#exist_functions option');
    var exist_functions = '';
    for (var i=0;i < kids.length;i++) {
        if (i == 0) {
            exist_functions += kids[i].value;
        } else {
            exist_functions += ',' + kids[i].value;
        }
    }
    var url = document.getElementById('function_screen').getAttribute('url')
              + '/do/available_functions' + param + '/exist_functions/'+exist_functions;
    var request = YAHOO.util.Connect.asyncRequest('GET', url, 
        {success: function(o){
                   document.getElementById('available_functions').parentNode.innerHTML = '<select style="width: 250px;" name="available_functions" id="available_functions" size="20" multiple="">'+o.responseText+'</select>';
                },
        failure: handleFailure});
}
var handleFailure = function(o){alert('error');}

function upload_evidence() {
    if (!form_confirm(document.finding_detail, 'Upload Evidence')) {
        return false;
    }
    // set the encoding for a file upload
    document.finding_detail.enctype = "multipart/form-data";
    panel('Upload Evidence', document.finding_detail, '/remediation/upload-form');
    return false;
}

function ev_deny(formname){
    if (!form_confirm(document.finding_detail, 'deny the evidence')) {
        return false;
    }

    var content = document.createElement('div');
    var p = document.createElement('p');
    p.appendChild(document.createTextNode('Comments:'));
    content.appendChild(p);
    var dt = document.createElement('textarea');
    dt.rows = 5;
    dt.cols = 60;
    dt.id = 'dialog_comment';
    dt.name = 'comment';
    content.appendChild(dt);
    var div = document.createElement('div');
    div.style.height = '20px';
    content.appendChild(div);
    var button = document.createElement('input');
    button.type = 'button';
    button.id = 'dialog_continue';
    button.value = 'Continue';
    content.appendChild(button);

    panel('Evidence Denial', document.finding_detail, '', content.innerHTML);
    document.getElementById('dialog_continue').onclick = function (){
        var form2 = formname;
        if  (document.all) { // IE
            var comment = document.getElementById('dialog_comment').innerHTML;
        } else {// firefox
            var comment = document.getElementById('dialog_comment').value;
        }
        form2.elements['comment'].value = comment;
        form2.elements['decision'].value = 'DENIED';
        var submitMsa = document.createElement('input');
        submitMsa.type = 'hidden';
        submitMsa.name = 'submit_ea';
        submitMsa.value = 'DENIED';
        form2.appendChild(submitMsa);
        form2.submit();
    }
}

function ms_comment(formname){
    if (!form_confirm(document.finding_detail, 'deny the mitigation')) {
        return false;
    }

    var content = document.createElement('div');
    var p = document.createElement('p');
    var c_title = document.createTextNode('Comments:');
    p.appendChild(c_title);
    content.appendChild(p);
    var textarea = document.createElement('textarea');
    textarea.id = 'dialog_comment';
    textarea.name = 'comment';
    textarea.rows = 5;
    textarea.cols = 60;
    content.appendChild(textarea);
    var div = document.createElement('div');
    div.style.height = '20px';
    content.appendChild(div);
    var button = document.createElement('input');
    button.type = 'button';
    button.id = 'dialog_continue';
    button.value = 'Continue';
    content.appendChild(button);
    
    panel('Mitigation Strategy Denial', document.finding_detail, '', content.innerHTML);
    document.getElementById('dialog_continue').onclick = function (){
        var form2 = formname;
        if  (document.all) { // IE
            var comment = document.getElementById('dialog_comment').innerHTML;
        } else {// firefox
            var comment = document.getElementById('dialog_comment').value;
        }
        form2.elements['comment'].value = comment;
        form2.elements['decision'].value = 'DENIED';
        var submitMsa = document.createElement('input');
        submitMsa.type = 'hidden';
        submitMsa.name = 'submit_msa';
        submitMsa.value = 'DENIED';
        form2.appendChild(submitMsa);
        form2.submit();
    }
}

function getProdId(){
    var trigger= document.getElementById('productId');
    YAHOO.util.Event.on(trigger, 'change', function (){
        document.getElementById('productId').value = trigger.value;
    });
}

var searchProduct = function (){
    var trigger = document.getElementById('search_product');
    var url = trigger.getAttribute('url');
    
    var productInput = YAHOO.util.Selector.query('input.product');
    for(var i = 0;i < productInput.length; i++) {
        if (productInput[i].value != undefined && productInput[i].value != '') {
            url += '/' + productInput[i].name + '/' + productInput[i].value;
        }
    }
    YAHOO.util.Connect.asyncRequest('GET', url, 
    {success: function(o){
                document.getElementById('productId').parentNode.innerHTML = o.responseText;
                document.getElementById('productId').style.width = "400px";
                getProdId();
              },
    failure: handleFailure});
}

var searchAsset = function() {
    var trigger = new YAHOO.util.Element('orgSystemId');
    if(trigger.get('id') == undefined){
        return ;
    }
    var sys = trigger.get('value');
    var param =  '';
    if(0 != parseInt(sys)){
        param +=  '/system_id/' + sys;
    }
    var assetInput = YAHOO.util.Selector.query('input.assets');
    for(var i = 0;i < assetInput.length; i++) {
        if (assetInput[i].value != undefined && assetInput[i].value != '') {
            param += '/' + assetInput[i].name + '/' + assetInput[i].value;
        }
    }
    var url = document.getElementById('orgSystemId').getAttribute("url") + param;
    YAHOO.util.Connect.asyncRequest('GET', url, 
    {success:function (o){
        document.getElementById('assetId').options.length = 0;
        var records = YAHOO.lang.JSON.parse(o.responseText);
        records = records.table.records;
        for(var i=0;i < records.length;i++){
            document.getElementById('assetId').options.add(new Option(records[i].name, records[i].id));
        }
    },
    failure: handleFailure});
}

function asset_detail() {
    YAHOO.util.Event.on('assetId', 'change', function (){
        var url = this.getAttribute("url") + this.value;
        YAHOO.util.Connect.asyncRequest('GET', url, {
            success:function (o){
                document.getElementById('asset_info').innerHTML = o.responseText
            },
            failure: handleFailure});
    });
}

function message( msg ,model){
    if (document.getElementById('msgbar')) {
        var msgbar = document.getElementById('msgbar'); 
    } else {
        return;
    }
    msgbar.innerHTML = msg;
    msgbar.style.fontWeight = 'bold';
    
    if( model == 'warning')  {
        msgbar.style.color = 'red';
    } else {
        msgbar.style.color = 'green';
        msgbar.style.borderColor = 'green';
        msgbar.style.backgroundColor = 'lightgreen';
    }
    msgbar.style.display = 'block';
}

function toggleSearchOptions(obj) {
    var searchbox = document.getElementById('advanced_searchbox');
    if (searchbox.style.display == 'none') {
        searchbox.style.display = '';
        obj.value = 'Basic Search';
    } else {
        searchbox.style.display = 'none';
        obj.value = 'Advanced Search';
    }
}

function showJustification(){
    if (document.getElementById('ecd_justification')) {
        document.getElementById('ecd_justification').style.display = '';
    }
}

function addBookmark(title, url){
    if(window.sidebar){ // Firefox
        window.sidebar.addPanel(title, url,'');
    }else if(window.opera){ //Opera
        var a = document.createElement("A");
        a.rel = "sidebar";
        a.target = "_search";
        a.title = title;
        a.href = url;
        a.click();
    } else if(document.all){ //IE
        window.external.AddFavorite(url, title);
    }
}

/**
 * Highlights search results according to the keywords which were used to search
 *
 * @param node object
 * @param keyword string
 */ 
function highlight(node,keywords) {
    if (!keywords) {
        return true;
    }

    // Remove special chars
	keywords = keywords.split(' ');
	for (var i in keywords) {
		keyword = keywords[i];

		// Iterate into this nodes childNodes
		if (node && node.hasChildNodes) {
			var hi_cn;
			for (hi_cn=0;hi_cn<node.childNodes.length;hi_cn++) {
				highlight(node.childNodes[hi_cn],keyword);
			}
		}

		// And do this node itself
		if (node && node.nodeType == 3) { // text node
			tempNodeVal = node.nodeValue.toLowerCase();
			tempWordVal = keyword.toLowerCase();
			if (tempNodeVal.indexOf(tempWordVal) != -1) {
				pn = node.parentNode;
				if (pn.className != "highlight") {
					// keyword has not already been highlighted!
					nv = node.nodeValue;
					ni = tempNodeVal.indexOf(tempWordVal);
					// Create a load of replacement nodes
					before = document.createTextNode(nv.substr(0,ni));
					docWordVal = nv.substr(ni,keyword.length);
					after = document.createTextNode(nv.substr(ni+keyword.length));
					hiwordtext = document.createTextNode(docWordVal);
					hiword = document.createElement("span");
					hiword.className = "highlight";
					hiword.appendChild(hiwordtext);
					pn.insertBefore(before,node);
					pn.insertBefore(hiword,node);
					pn.insertBefore(after,node);
					pn.removeChild(node);
				}
			}
    	}
	}
}

/**
 * Remove the highlight attribute from the editable textarea on remediation detail page
 *
 * @param node object 
 */
function removeHighlight(node) {
	// Iterate into this nodes childNodes
	if (node.hasChildNodes) {
		var hi_cn;
		for (hi_cn=0;hi_cn<node.childNodes.length;hi_cn++) {
			removeHighlight(node.childNodes[hi_cn]);
		}
	}

	// And do this node itself
	if (node.nodeType == 3) { // text node
		pn = node.parentNode;
		if( pn.className == "highlight" ) {
			prevSib = pn.previousSibling;
			nextSib = pn.nextSibling;
			nextSib.nodeValue = prevSib.nodeValue + node.nodeValue + nextSib.nodeValue;
			prevSib.nodeValue = '';
			node.nodeValue = '';
		}
	}
}

function switchYear(step){
    if( !isFinite(step) ){
        step = 0;
    }
    var oYear = document.getElementById('gen_shortcut');
    var year = oYear.getAttribute('year');
    year = Number(year) + Number(step);
	oYear.setAttribute('year', year);
    var url = oYear.getAttribute('url') + year + '/';
    var tmp = YAHOO.util.Selector.query('#gen_shortcut span:nth-child(1)');
    tmp[0].innerHTML = year;
    tmp[0].parentNode.setAttribute('href', url);
    tmp[1].parentNode.setAttribute('href', url + 'q/1/');
    tmp[2].parentNode.setAttribute('href', url + 'q/2/');
    tmp[3].parentNode.setAttribute('href', url + 'q/3/');
    tmp[4].parentNode.setAttribute('href', url + 'q/4/');
}

/**
 * Check the form if has something changed but not saved
 * if nothing changes, then give a confirmation
 * @param dom check_form checking form
 * @param str user's current action
 */
function form_confirm (check_form, action) {
    var changed = false;
    
    elements = YAHOO.util.Selector.query("[name*='finding']");
    for (var i = 0;i < elements.length; i ++) {
        var tag_name = elements[i].tagName.toUpperCase();
        if (tag_name == 'INPUT') {
            var e_type = elements[i].type;
            if (e_type == 'text' || e_type == 'password') {
                var _v = elements[i].getAttribute('_value');
                if(typeof(_v) == 'undefined')   _v = '';
                if(_v != elements[i].value) changed = true;
            }
            if (e_type == 'checkbox' || e_type == 'radio') {
                var _v = elements[i].checked ? 'on' : 'off';  
                if(_v != elements[i].getAttribute('_value')) changed = true;  
            }
        } else if (tag_name == 'SELECT') {
            var _v = elements[i].getAttribute('_value');    
            if(typeof(_v) == 'undefined')   _v = '';    
            if(_v != elements[i].options[elements[i].selectedIndex].value) changed = true;  
        } else if (tag_name == 'TEXTAREA') {
            var _v = elements[i].getAttribute('_value');
            if(typeof(_v) == 'undefined')   _v = '';
            var textarea_val = elements[i].value ? elements[i].value : elements[i].innerHTML;
            if(_v != textarea_val) changed = true;
        }
    }

    if(changed) {
        if (confirm('WARNING: You have unsaved changes on the page. If you continue, these'
                  + ' changes will be lost. If you want to save your changes, click "Cancel"' 
                  + ' now and then click "Save Changes".')) {
            return true;
        }
    }
    
    if (confirm('WARNING: You are about to ' + action + '. This action cannot be undone.'
              + ' Please click "Ok" to confirm your action or click "Cancel" to stop.')) {
        return true;
    }

    return false;
}

function dump(arr) {
    var text = '' + arr;
    for (i in arr) {
        if ('function' != typeof(arr[i])) {
            text += i + " : " + arr[i] + "\n";
        }
    }
    alert(text);
} 

/* temporary helper function to fix a bug in evidence upload for IE6/IE7 */
function panel(title, parent, src, html, callback) {
    var newPanel = new YAHOO.widget.Panel('panel', {width:"540px", modal:true} );
    newPanel.setHeader(title);
    newPanel.setBody("Loading...");
    newPanel.render(parent);
    newPanel.center();
    newPanel.show();
    
    if (src != '') {
        // Load the help content for this module
        YAHOO.util.Connect.asyncRequest('GET', 
                                        src,
                                        {
                                            success: function(o) {
                                                // Set the content of the panel to the text of the help module
                                                o.argument.setBody(o.responseText);
                                                // Re-center the panel (because the content has changed)
                                                o.argument.center();
                                                
                                                callback();
                                            },
                                            failure: function(o) {alert('Failed to load the specified panel.');},
                                            argument: newPanel
                                        }, 
                                        null);
    } else {
        // Set the content of the panel to the text of the help module
        newPanel.setBody(html);
        // Re-center the panel (because the content has changed)
        newPanel.center();
    }
}

var e = YAHOO.util.Event;
e.onDOMReady(readyFunc);

function callCalendar(evt, ele) {
    showCalendar(ele, ele+'_show');
}

function showCalendar(block, trigger) {
    var Event = YAHOO.util.Event, Dom = YAHOO.util.Dom, dialog, calendar;

    var showBtn = Dom.get(trigger);
    
    var dialog;
    var calendar;
    
    // Lazy Dialog Creation - Wait to create the Dialog, and setup document click listeners, until the first time the button is clicked.
    if (!dialog) {
        function resetHandler() {
            Dom.get(block).value = '';
            closeHandler();
        }

        function closeHandler() {
            dialog.hide();
        }

        dialog = new YAHOO.widget.Dialog("container", {
            visible:false,
            context:[block, "tl", "bl"],
            draggable:true,
            close:true
        });
        
        dialog.setHeader('Pick A Date');
        dialog.setBody('<div id="cal"></div><div class="clear"></div>');
        dialog.render(document.body);

        dialogEl = document.getElementById('container');
        dialogEl.style.padding = "0px"; // doesn't format itself correctly in safari, for some reason

        dialog.showEvent.subscribe(function() {
            if (YAHOO.env.ua.ie) {
                // Since we're hiding the table using yui-overlay-hidden, we 
                // want to let the dialog know that the content size has changed, when
                // shown
                dialog.fireEvent("changeContent");
            }
        });
    }

    // Lazy Calendar Creation - Wait to create the Calendar until the first time the button is clicked.
    if (!calendar) {

        calendar = new YAHOO.widget.Calendar("cal", {
            iframe:false,          // Turn iframe off, since container has iframe support.
            hide_blank_weeks:true  // Enable, to demonstrate how we handle changing height, using changeContent
        });
        calendar.render();

        calendar.selectEvent.subscribe(function() {
            if (calendar.getSelectedDates().length > 0) {
                var selDate = calendar.getSelectedDates()[0];
                // Pretty Date Output, using Calendar's Locale values: Friday, 8 February 2008
                //var wStr = calendar.cfg.getProperty("WEEKDAYS_LONG")[selDate.getDay()];
                var dStr = (selDate.getDate() < 10) ? '0'+selDate.getDate() : selDate.getDate();
                var mStr = (selDate.getMonth()+1 < 10) ? '0'+(selDate.getMonth()+1) : (selDate.getMonth()+1);
                var yStr = selDate.getFullYear();

                Dom.get(block).value = yStr + '-' + mStr + '-' + dStr;
            } else {
                Dom.get(block).value = "";
            }
            dialog.hide();
            if ('finding[currentEcd]' == Dom.get(block).name) {
                validateEcd();
            }
        });

        calendar.renderEvent.subscribe(function() {
            // Tell Dialog it's contents have changed, which allows 
            // container to redraw the underlay (for IE6/Safari2)
            dialog.fireEvent("changeContent");
        });
    }

    var seldate = calendar.getSelectedDates();

    if (seldate.length > 0) {
        // Set the pagedate to show the selected date if it exists
        calendar.cfg.setProperty("pagedate", seldate[0]);
        calendar.render();
    }
    dialog.show();
}
/*****************************************************************************
 *
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenFISMA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenFISMA.  If not, see <http://www.gnu.org/licenses/>.
 *
 *******************************************************************************
 *
 * Used to generate a tree table
 *
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id: $
 *
 *******************************************************************************
 */

YAHOO.namespace ("fisma.TreeTable"); 

// Holds a reference to the tree which is being displayed.
// This only supports one tree table per instance.
YAHOO.fisma.TreeTable.treeRoot;

// How many tree levels to display, by default
YAHOO.fisma.TreeTable.defaultDisplayLevel = 2;

YAHOO.fisma.TreeTable.render = function (tableId, tree) {
    // Set the global tree root first, if necessary
    if (YAHOO.lang.isUndefined(YAHOO.fisma.TreeTable.treeRoot)) {
        YAHOO.fisma.TreeTable.treeRoot = tree;
    }
    var table = document.getElementById(tableId);

    // Render each node at this level
    for (var nodeId in tree) {
        var node = tree[nodeId];

        // Add two rows to the table for this node
        var firstRow = table.insertRow(table.rows.length);
        firstRow.id = node.nickname;
        var secondRow = table.insertRow(table.rows.length);
        secondRow.id = node.nickname + "2";
       
        // The first cell of the first row is the system label
        var firstCell = firstRow.insertCell(0);

        // Determine which set of counts to show initially (single or all)
        node.expanded = (node.level < YAHOO.fisma.TreeTable.defaultDisplayLevel - 1);
        var ontime = node.expanded ? node.single_ontime : node.all_ontime;
        var overdue = node.expanded ? node.single_overdue : node.all_overdue;
        node.hasOverdue = YAHOO.fisma.TreeTable.arraySum(overdue) > 0;

        // @todo convert to YUI and remove innerHTML if possible
        // general cleanup is needed too
        needsLink = node.children.length > 0;
        linkOpen = (needsLink ? "<a href='#' onclick='YAHOO.fisma.TreeTable.toggleNode(\"" + node.nickname + "\")'>" : "");
        linkClose = needsLink ? "</a>" : "";
        linkDivClass = needsLink ? " link" : "";
        controlImage = node.expanded ? "minus.png" : "plus.png";
        control = needsLink ? "<img class=\"control\" id=\"" + node.nickname + "Img\" src=\"/images/" + controlImage + "\">" : "<img class=\"control\" id=\"" + node.nickname + "Img\" src=\"/images/leaf_node.png\">";

        firstCell.innerHTML = "<div class=\"treeTable" + node.level + linkDivClass + "\">" + linkOpen + control + "<img class=\"icon\" src=\"/images/" + node.orgType + ".png\">" + node.label + '<br><i>' + node.orgTypeLabel + '</i>' + linkClose + '</div>';

        // The remaining cells on the first row are summary counts
        var i = 1; // b/c the system label is in the first cell
        for (var c in ontime) {
            count = ontime[c];
            cell = firstRow.insertCell(i++);
            if (c == 'CLOSED' || c == 'TOTAL') {
                // The last two colums don't have the ontime/overdue distinction
                cell.className = "noDueDate";
            } else {
                // The in between columns should have the ontime class
                cell.className = 'onTime';                
            }
            YAHOO.fisma.TreeTable.updateCellCount(cell, count, node.id, c, 'ontime');
        }

        // Now add cells to the second row
        for (var c in overdue) {
            count = overdue[c];
            cell = secondRow.insertCell(secondRow.childNodes.length);
            cell.className = 'overdue';
            YAHOO.fisma.TreeTable.updateCellCount(cell, count, node.id, c, 'overdue');
        }

        // Hide both rows by default
        firstRow.style.display = "none";
        secondRow.style.display = "none";

        // Selectively display one or both rows based on current level and whether it has overdues
        if (node.level < YAHOO.fisma.TreeTable.defaultDisplayLevel) {
            firstRow.style.display = '';  // set to default instead of 'table-row' to work around an IE6 bug
            if (node.hasOverdue) {
                firstRow.childNodes[0].rowSpan = "2";
                firstRow.childNodes[firstRow.childNodes.length - 2].rowSpan = "2";
                firstRow.childNodes[firstRow.childNodes.length - 1].rowSpan = "2";
                secondRow.style.display = '';  // set to default instead of 'table-row' to work around an IE6 bug
            }
        }
        
        // If this node has children, then recursively render the children
        if (node.children.length > 0) {
            YAHOO.fisma.TreeTable.render(tableId, node.children);
        }
    }
}

YAHOO.fisma.TreeTable.toggleNode = function (treeNode) {
    node = YAHOO.fisma.TreeTable.findNode(treeNode, YAHOO.fisma.TreeTable.treeRoot);
    if (node.expanded) {
        YAHOO.fisma.TreeTable.collapseNode(node, true);
        YAHOO.fisma.TreeTable.hideSubtree(node.children);
    } else {
        YAHOO.fisma.TreeTable.expandNode(node);
        YAHOO.fisma.TreeTable.showSubtree(node.children, false);
    }
}

YAHOO.fisma.TreeTable.expandNode = function (treeNode, recursive) {
    // When expanding a node, switch the counts displayed from the "all" counts to the "single"
    treeNode.ontime = treeNode.single_ontime;
    treeNode.overdue = treeNode.single_overdue;
    treeNode.hasOverdue = YAHOO.fisma.TreeTable.arraySum(treeNode.overdue) > 0;

    // Update the ontime row first
    var ontimeRow = document.getElementById(treeNode.nickname);    
    var i = 1; // start at 1 b/c the first column is the system name
    for (c in treeNode.ontime) {
        count = treeNode.ontime[c];
        YAHOO.fisma.TreeTable.updateCellCount(ontimeRow.childNodes[i], count, treeNode.id, c, 'ontime');
        i++;
    }
    
    // Then update the overdue row, or hide it if there are no overdues
    var overdueRow = document.getElementById(treeNode.nickname + "2");
    if (treeNode.hasOverdue) {
        // Do not hide the overdue row. Instead, update the counts
        var i = 0;
        for (c in treeNode.overdue) {
            count = treeNode.overdue[c];
            YAHOO.fisma.TreeTable.updateCellCount(overdueRow.childNodes[i], count, treeNode.id, c, 'overdue');
            i++;
        }
    } else {
        // Hide the overdue row and adjust the rowspans on the ontime row to compensate
        ontimeRow.childNodes[0].rowSpan = "1";
        ontimeRow.childNodes[ontimeRow.childNodes.length - 2].rowSpan = "1";
        ontimeRow.childNodes[ontimeRow.childNodes.length - 1].rowSpan = "1";
        overdueRow.style.display = 'none';
    }
    
    // Update the control image and internal status field
    if (treeNode.children.length > 0) {
        document.getElementById(treeNode.nickname + "Img").src = "/images/minus.png";
    }
    treeNode.expanded = true;
    
    // If the function is called recursively and this node has children, then
    // expand the children.
    if (recursive && treeNode.children.length > 0) {
        YAHOO.fisma.TreeTable.showSubtree(treeNode.children, false);
        for (var child in treeNode.children) {
            YAHOO.fisma.TreeTable.expandNode(treeNode.children[child], true);
        }
    }
}

YAHOO.fisma.TreeTable.collapseNode = function (treeNode, displayOverdue) {
    // When collapsing a node, switch the counts displayed from the "single" counts to the "all"
    treeNode.ontime = treeNode.all_ontime;
    treeNode.overdue = treeNode.all_overdue;
    treeNode.hasOverdue = YAHOO.fisma.TreeTable.arraySum(treeNode.overdue) > 0;

    // Update the ontime row first
    var ontimeRow = document.getElementById(treeNode.nickname);
    var i = 1; // start at 1 b/c the first column is the system name
    for (c in treeNode.ontime) {
        count = treeNode.ontime[c];
        YAHOO.fisma.TreeTable.updateCellCount(ontimeRow.childNodes[i], count, treeNode.id, c, 'ontime');
        i++;
    }
    
    // Update the overdue row. Display the row first if necessary.
    var overdueRow = document.getElementById(treeNode.nickname + "2");
    if (displayOverdue && treeNode.hasOverdue) {
        // Show the overdue row and adjust the rowspans on the ontime row to compensate
        ontimeRow.childNodes[0].rowSpan = "2";
        ontimeRow.childNodes[ontimeRow.childNodes.length - 2].rowSpan = "2";
        ontimeRow.childNodes[ontimeRow.childNodes.length - 1].rowSpan = "2";
        overdueRow.style.display = '';  // set to default instead of 'table-row' to work around an IE6 bug

        var i = 0;
        for (c in treeNode.all_overdue) {
            count = treeNode.all_overdue[c];
            YAHOO.fisma.TreeTable.updateCellCount(overdueRow.childNodes[i], count, treeNode.id, c, 'overdue');
            i++;
        }
    }

    // If the node has children, the hide those children
    if (treeNode.children.length > 0) {
        YAHOO.fisma.TreeTable.hideSubtree(treeNode.children);
    }
        
    document.getElementById(treeNode.nickname + "Img").src = "/images/plus.png";
    treeNode.expanded = false;
}

YAHOO.fisma.TreeTable.hideSubtree = function (nodeArray) {
    for (nodeId in nodeArray) {
        node = nodeArray[nodeId];

        // Now update this node
        ontimeRow = document.getElementById(node.nickname);
        ontimeRow.style.display = 'none';
        overdueRow = document.getElementById(node.nickname + "2");
        overdueRow.style.display = 'none';

        // Recurse through children
        if (node.children.length > 0) {
            YAHOO.fisma.TreeTable.collapseNode(node, false);
            YAHOO.fisma.TreeTable.hideSubtree(node.children);
        }
    }
}

YAHOO.fisma.TreeTable.showSubtree = function (nodeArray, recursive) {
    for (nodeId in nodeArray) {
        node = nodeArray[nodeId];

        // Recurse through the child nodes (if necessary)
        if (recursive && node.children.length > 0) {
            YAHOO.fisma.TreeTable.expandNode(node);
            YAHOO.fisma.TreeTable.showSubtree(node.children, true);            
        }

        // Now update this node
        ontimeRow = document.getElementById(node.nickname);
        ontimeRow.style.display = '';  // set to default instead of 'table-row' to work around an IE6 bug
        overdueRow = document.getElementById(node.nickname + "2");
        if (node.hasOverdue) {
            ontimeRow.childNodes[0].rowSpan = "2";
            ontimeRow.childNodes[ontimeRow.childNodes.length - 2].rowSpan = "2";
            ontimeRow.childNodes[ontimeRow.childNodes.length - 1].rowSpan = "2";
            overdueRow.style.display = '';  // set to default instead of 'table-row' to work around an IE6 bug
        }
    }   
}

YAHOO.fisma.TreeTable.collapseAll = function () {
    for (nodeId in YAHOO.fisma.TreeTable.treeRoot) {
        node = YAHOO.fisma.TreeTable.treeRoot[nodeId];
        YAHOO.fisma.TreeTable.collapseNode(node, true);
        YAHOO.fisma.TreeTable.hideSubtree(node.children);
    }
}

YAHOO.fisma.TreeTable.expandAll = function () {
    for (nodeId in YAHOO.fisma.TreeTable.treeRoot) {
        node = YAHOO.fisma.TreeTable.treeRoot[nodeId];
        YAHOO.fisma.TreeTable.expandNode(node, true);
    }
}

YAHOO.fisma.TreeTable.findNode = function (nodeName, tree) {
    for (var nodeId in tree) {
        node = tree[nodeId];
        if (node.nickname == nodeName) {
            return node;
        } else if (node.children.length > 0) {
            var foundNode = YAHOO.fisma.TreeTable.findNode(nodeName, node.children);
            if (foundNode != false) {
                return foundNode;
            }
        }
    }
    return false;
}

YAHOO.fisma.TreeTable.arraySum = function (a) {
    var sum = 0;
    for (var i in a) {
        sum += a[i];
    }
    return sum;
}

YAHOO.fisma.TreeTable.updateCellCount = function(cell, count, orgId, status, ontime) {
    if (!cell.hasChildNodes()) {
        // Initialize this cell
        if (count > 0) {
            var link = document.createElement('a');
            link.href = YAHOO.fisma.TreeTable.makeLink(orgId, status, ontime);
            link.appendChild(document.createTextNode(count));
            cell.appendChild(link);
        } else {
            cell.appendChild(document.createTextNode('-'));
        }
    } else {
        // The cell is already initialized, so we may need to add or remove child elements
        if (cell.firstChild.hasChildNodes()) {
            // The cell contains an anchor
            if (count > 0) {
                // Update the anchor text
                cell.firstChild.firstChild.nodeValue = count;
            } else {
                // Remove the anchor
                cell.removeChild(cell.firstChild);
                cell.appendChild(document.createTextNode('-'));
            }
        } else {
            // The cell contains just a text node
            if (count > 0) {
                // Need to add a new anchor
                cell.removeChild(cell.firstChild);
                var link = document.createElement('a');
                link.href = YAHOO.fisma.TreeTable.makeLink(orgId, status, ontime);
                link.appendChild(document.createTextNode(count));
                cell.appendChild(link);
            } else {
                // Update the text node value
                cell.firstChild.nodeValue = '-';
            }
        }
    }
}

YAHOO.fisma.TreeTable.makeLink = function(orgId, status, ontime) {
    var uri = '/panel/remediation/sub/search/ontime/'
            + ontime
            + '/orgId/'
            + orgId
            + '/status/' 
            + escape(status);
    return uri;
}

YAHOO.fisma.TreeTable.exportTable = function(format) {
    var uri = '/remediation/summary-data/format/'
            + format
            + YAHOO.fisma.TreeTable.listExpandedNodes(YAHOO.fisma.TreeTable.treeRoot, '');
    document.location = uri;
}

YAHOO.fisma.TreeTable.listExpandedNodes = function(nodes, visibleNodes) {
    for (var n in nodes) {
        var node = nodes[n];
        if (node.expanded) {
            visibleNodes += '/e/' + node.id;
            visibleNodes = YAHOO.fisma.TreeTable.listExpandedNodes(node.children, visibleNodes);
        } else {
            visibleNodes += '/c/' + node.id;
        }
    }
    return visibleNodes;
}

YAHOO.fisma.TreeTable.exportExcel = function() {
    YAHOO.fisma.TreeTable.exportTable('xls');
}

YAHOO.fisma.TreeTable.exportPdf = function() {
    YAHOO.fisma.TreeTable.exportTable('pdf');
}
/**
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify it under the terms of the GNU General Public 
 * License as published by the Free Software Foundation, either version 3 of the License, or (at your option) any later
 * version.
 *
 * OpenFISMA is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied 
 * warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more 
 * details.
 *
 * You should have received a copy of the GNU General Public License along with OpenFISMA.  If not, see 
 * <http://www.gnu.org/licenses/>.
 *
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 */

YAHOO.namespace("fisma.CheckboxTree");

YAHOO.fisma.CheckboxTree.rootNode;

/**
 * Handle a click on the checkbox tree. Clicking a nested node will select all nodes inside of it,
 * unless all of the subnodes are already selected, in which case it will deselect all subnodes.
 * Holding down the option key while clicking disables this behavior.
 *
 * The checkbox tree DOM looks like this:
 * <li><input type="checkbox" nestedLevel="0"><label></li>
 *  <li><input type="checkbox" nestedLevel="1"><label></li>
 *   <li><input type="checkbox" nestedLevel="2"><label></li>
 * etc...
 */
YAHOO.fisma.CheckboxTree.handleClick = function(clickedBox, event) 
{
    // If the option key is held down, then skip all of this logic.
    if (event.altKey) {
        return;
    }

    var topListItem = clickedBox.parentNode;

    // If there are no nested checkboxes, then there is nothing to do
    if (topListItem.nextSibling) {
        var minLevel = clickedBox.getAttribute('nestedlevel');
        var checkboxArray = new Array();
        var allChildNodesChecked = true;

        // Loop through all of the subnodes and see which ones are already checked
        var listItem = topListItem.nextSibling;
        var checkboxItem = listItem.childNodes[0];
        while (checkboxItem.getAttribute('nestedLevel') > minLevel) {
            if (!checkboxItem.checked) {
                allChildNodesChecked = false;
            }
            
            checkboxArray.push(checkboxItem);
            
            if (listItem.nextSibling) {
                listItem = listItem.nextSibling;
                checkboxItem = listItem.childNodes[0];
            } else {
                break;
            }
        }
        
        // Update the node which the user clicked on
        if (allChildNodesChecked) {
            clickedBox.checked = false;
        } else {
            clickedBox.checked = true;
        }
        
        // Now iterate through child nodes and update them
        for (var i in checkboxArray) {
            var checkbox = checkboxArray[i];
            
            if (allChildNodesChecked) {
                checkbox.checked = false;
            } else {
                checkbox.checked = true;
            }
        }
    }
}
/*************************************************************************
 *
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenFISMA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenFISMA.  If not, see <http://www.gnu.org/licenses/>.
 *
 *******************************************************************************
 *
 * When a form containing editable fields is loaded (such as the tabs on the
 * remediation detail page), this function is used to add the required click
 * handler to all of the editable fields.
 *
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id: $
 *
 *********************************************************************************
 */

function setupEditFields() {
    var editable = YAHOO.util.Selector.query('.editable');
    YAHOO.util.Event.on(editable, 'click', function (o){
        removeHighlight(document);
        var t_name = this.getAttribute('target');
        YAHOO.util.Dom.removeClass(this, 'editable'); 
        this.removeAttribute('target');
        if(t_name) {
             var target = document.getElementById(t_name);
             var name = target.getAttribute('name');
             var type = target.getAttribute('type');
             var url = target.getAttribute('href');
             var eclass = target.className;
             var cur_val = target.innerText ? target.innerText : target.textContent;
             var cur_html = target.innerHTML;
             if (type == 'text') {
                 target.outerHTML = '<input length="50" name="'+name+'" id="'+t_name+'" class="'+eclass+'" type="text" value="'+cur_val.trim()+'" />';
                 if (eclass == 'date') {
                     var target = document.getElementById(t_name);
                     target.onfocus = function () {showCalendar(t_name, t_name+'_show');};
                     calendarIcon = document.createElement('img');
                     calendarIcon.id = t_name + "_show";
                     calendarIcon.src = "/images/calendar.gif";
                     calendarIcon.alt = "Calendar";
                     target.parentNode.appendChild(calendarIcon);
                     YAHOO.util.Event.on(t_name+'_show', "click", function() {
                        showCalendar(t_name, t_name+'_show');
                     });
                 }
             } else if( type == 'textarea' ) {
                 var row = target.getAttribute('rows');
                 var col = target.getAttribute('cols');
                 target.outerHTML = '<textarea id="'+name+'" rows="'+row+'" cols="'+col+'" name="'+name+'">' + cur_html+ '</textarea>';
                 tinyMCE.execCommand("mceAddControl", true, name);
             } else {
                 YAHOO.util.Connect.asyncRequest('GET', url+'value/'+cur_val.trim(), {
                        success: function(o) {
                             if(type == 'select'){
                                 target.outerHTML = '<select name="'+name+'">'+o.responseText+'</select>';
                             }
                        },
                        failure: function(o) {alert('Failed to load the specified panel.');}
                    }, null);
             }
        }
    });
}

function validateEcd() {
    var obj = document.getElementById('expectedCompletionDate');
    var inputDate = obj.value;
    var oDate= new Date();
    var Year = oDate.getFullYear();
    var Month = oDate.getMonth();
    Month = Month + 1;
    if (Month < 10) {Month = '0'+Month;}
    var Day = oDate.getDate();
    if (Day < 10) {Day = '0' + Day;}
    if (inputDate.replace(/\-/g, "") <= parseInt(""+Year+""+Month+""+Day)) {
        alert("Warning: You entered an ECD date in the past.");
    }
}

if (window.HTMLElement) {
    HTMLElement.prototype.__defineSetter__("outerHTML",function(sHTML){
        var r=this.ownerDocument.createRange();
        r.setStartBefore(this);
        var df=r.createContextualFragment(sHTML);
        this.parentNode.replaceChild(df,this);
        return sHTML;
        });

    HTMLElement.prototype.__defineGetter__("outerHTML",function(){
    var attr;
        var attrs=this.attributes;
        var str="<"+this.tagName.toLowerCase();
        for(var i=0;i<attrs.length;i++){
            attr=attrs[i];
            if(attr.specified)
                str+=" "+attr.name+'="'+attr.value+'"';
            }
        if(!this.canHaveChildren)
            return str+">";
        return str+">"+this.innerHTML+"</"+this.tagName.toLowerCase()+">";
        });

    HTMLElement.prototype.__defineGetter__("canHaveChildren",function(){
    switch(this.tagName.toLowerCase()){
            case "area":
            case "base":
            case "basefont":
            case "col":
            case "frame":
            case "hr":
            case "img":
            case "br":
            case "input":
            case "isindex":
            case "link":
            case "meta":
            case "param":
            return false;
        }
        return true;
     });
}
/*****************************************************************************
 *
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenFISMA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenFISMA.  If not, see <http://www.gnu.org/licenses/>.
 *
 ******************************************************************************
 *
 * Helper function for the on-line help feature in OpenFISMA
 *
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id: $
 *
 ******************************************************************************
 */

var helpPanels = new Array();
function showHelp(event, helpModule) {
    if (helpPanels[helpModule]) {
        helpPanels[helpModule].show();
    } else {
        // Create new panel
        var newPanel = new YAHOO.widget.Panel('helpPanel', {width:"400px"} );
        newPanel.setHeader("Help");
        newPanel.setBody("Loading...");
        newPanel.render(document.body);
        newPanel.center();
        newPanel.show();
        
        // Load the help content for this module
        YAHOO.util.Connect.asyncRequest('GET', 
                                        '/help/help/module/' + helpModule, 
                                        {
                                            success: function(o) {
                                                // Set the content of the panel to the text of the help module
                                                o.argument.setBody(o.responseText);
                                                // Re-center the panel (because the content has changed)
                                                o.argument.center();
                                            },
                                            failure: function(o) {alert('Failed to load the help module.');},
                                            argument: newPanel
                                        }, 
                                        null);
        
        // Store this panel to be re-used on subsequent calls
        helpPanels[helpModule] = newPanel;
    }
}
/********************************************************************************
 *
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenFISMA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenFISMA.  If not, see <http://www.gnu.org/licenses/>.
 *
 *********************************************************************************
 *
 * This function is unsafe because it selects all checkboxes on the page, regardless
 * of what grouping they belong to.
 * @todo Write a safe version of this function called selectAll that takes some kind
 * of scope as a parameter so that it can be limited.
 *
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id: $
 *
 ***********************************************************************************
 */

function selectAllUnsafe() {
    var checkboxes = YAHOO.util.Dom.getElementsBy(
        function (el) {
            return (el.tagName == 'INPUT' && el.type == 'checkbox')
        }
    );
    for (i in checkboxes) {
        checkboxes[i].checked = 'checked';
    }
}

function selectAll() {
    alert("Not implemented");
}

function selectNoneUnsafe() {
    var checkboxes = YAHOO.util.Dom.getElementsBy(
        function (el) {
            return (el.tagName == 'INPUT' && el.type == 'checkbox')
        }
    );
    for (i in checkboxes) {
        checkboxes[i].checked = '';
    }
}

function selectNone() {
    alert("Not implemented");
}

function elDump(el) {
    props = '';
    for (prop in el) {
        props += prop + ' : ' + el[prop] + '\n';
    }
    alert(props);
}
/*******************************************************************************
 *
 * Copyright (c) 2008 Endeavor Systems, Inc.
 *
 * This file is part of OpenFISMA.
 *
 * OpenFISMA is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OpenFISMA is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OpenFISMA.  If not, see <http://www.gnu.org/licenses/>.
 *
 ********************************************************************************
 *
 * Used to present the user an alert box asking them if they are sure they want to 
 * delete the item they selected, the entryname should be defined in the form.
 * If the user selects ok the function returns true, if the user selects cancel the 
 * function returns false
 *
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id: $
 *
 ********************************************************************************
 */

function delok(entryname)
{
    var str = "Are you sure that you want to delete this " + entryname + "?";
    if(confirm(str) == true){
        return true;
    }
    return false;
}
