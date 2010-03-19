/**
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
 * along with OpenFISMA.  If not, see {@link http://www.gnu.org/licenses/}.
 *
 * @fileoverview Main js file
 *
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license   http://www.openfisma.org/content/license
 * @version   $Id$
 *
 * @todo      Start migrating functionality out of this file. 
 *            Eventually this file needs to be removed 
 */

// Required for AC_RunActiveContent
// @TODO Move into own file

var requiredMajorVersion = 9;
var requiredMinorVersion = 0;
var requiredRevision = 45;

var Fisma = {};

$P = new PHP_JS();

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
        var options = new YAHOO.util.Selector.query('#availableFunctions option');
        for (var i = 0; i < options.length; i ++) {
            if (options[i].selected == true) {
                document.getElementById('existFunctions').appendChild(options[i]);
            }
        }
        return false;  
    });
    //
    YAHOO.util.Event.on('remove_function', 'click', function() {
        var options = YAHOO.util.Selector.query('#existFunctions option');
        for (var i = 0; i < options.length; i ++) {
            if (options[i].selected == true) {
                document.getElementById('availableFunctions').appendChild(options[i]);
            }
        }
        return false;
    });
    //
    YAHOO.util.Event.on(YAHOO.util.Selector.query('form[name=assign_right]'), 'submit', 
    function (){
        var options = YAHOO.util.Selector.query('#existFunctions option');
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
    var kids = YAHOO.util.Selector.query('#existFunctions option');
    var existFunctions = '';
    for (var i=0;i < kids.length;i++) {
        if (i == 0) {
            existFunctions += kids[i].value;
        } else {
            existFunctions += ',' + kids[i].value;
        }
    }
    var url = document.getElementById('function_screen').getAttribute('url')
              + '/do/availableFunctions' + param + '/existFunctions/'+existFunctions;
    var request = YAHOO.util.Connect.asyncRequest('GET', url, 
        {success: function(o){
                   document.getElementById('availableFunctions').parentNode.innerHTML = '<select style="width: 250px;" name="availableFunctions" id="availableFunctions" size="20" multiple="">'+o.responseText+'</select>';
                },
        failure: handleFailure});
}
var handleFailure = function(o){alert('error');}

function upload_evidence() {
    if (!form_confirm(document.finding_detail, 'Upload Evidence')) {
        return false;
    }
    Fisma.UrlPanel.showPanel('Upload Evidence', '/remediation/upload-form', upload_evidence_form_init);
    return false;
}

function upload_evidence_form_init() {
    document.finding_detail_upload_evidence.action = document.finding_detail.action;
}

function ev_approve(formname){
    if (!form_confirm(document.finding_detail, 'approve the evidence package')) {
        return false;
    }

    var content = document.createElement('div');
    var p = document.createElement('p');
    p.appendChild(document.createTextNode('Comments (OPTIONAL):'));
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

    Fisma.HtmlPanel.showPanel('Evidence Approval', content.innerHTML);
    document.getElementById('dialog_continue').onclick = function (){
        var form2 = formname;
        if  (document.all) { // IE
            var comment = document.getElementById('dialog_comment').innerHTML;
        } else {// firefox
            var comment = document.getElementById('dialog_comment').value;
        }
        form2.elements['comment'].value = comment;
        form2.elements['decision'].value = 'APPROVED';
        var submitMsa = document.createElement('input');
        submitMsa.type = 'hidden';
        submitMsa.name = 'submit_ea';
        submitMsa.value = 'APPROVED';
        form2.appendChild(submitMsa);
        form2.submit();
    }
}

function ev_deny(formname){
    if (!form_confirm(document.finding_detail, 'deny the evidence package')) {
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

    Fisma.HtmlPanel.showPanel('Evidence Denial', content.innerHTML);
    document.getElementById('dialog_continue').onclick = function (){
        var form2 = formname;
        if  (document.all) { // IE
            var comment = document.getElementById('dialog_comment').innerHTML;
        } else {// firefox
            var comment = document.getElementById('dialog_comment').value;
        }
        if (comment.match(/^\s*$/)) {
            alert('Comments are required in order to deny.');
            return;
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

function ms_approve(formname){
    if (!form_confirm(document.finding_detail, 'approve the mitigation strategy')) {
        return false;
    }

    var content = document.createElement('div');
    var p = document.createElement('p');
    var c_title = document.createTextNode('Comments (OPTIONAL):');
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
    
    Fisma.HtmlPanel.showPanel('Mitigation Strategy Approval', content.innerHTML);
    document.getElementById('dialog_continue').onclick = function (){
        var form2 = formname;
        if  (document.all) { // IE
            var comment = document.getElementById('dialog_comment').innerHTML;
        } else {// firefox
            var comment = document.getElementById('dialog_comment').value;
        }
        form2.elements['comment'].value = comment;
        form2.elements['decision'].value = 'APPROVED';
        var submitMsa = document.createElement('input');
        submitMsa.type = 'hidden';
        submitMsa.name = 'submit_msa';
        submitMsa.value = 'APPROVED';
        form2.appendChild(submitMsa);
        form2.submit();
    }
}

function ms_deny(formname){
    if (!form_confirm(document.finding_detail, 'deny the mitigation strategy')) {
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
    
    Fisma.HtmlPanel.showPanel('Mitigation Strategy Denial', content.innerHTML);
    document.getElementById('dialog_continue').onclick = function (){
        var form2 = formname;
        if  (document.all) { // IE
            var comment = document.getElementById('dialog_comment').innerHTML;
        } else {// firefox
            var comment = document.getElementById('dialog_comment').value;
        }
        if (comment.match(/^\s*$/)) {
            alert('Comments are required in order to submit.');
            return;
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
    msg = $P.stripslashes(msg);
    if (document.getElementById('msgbar')) {
        var msgbar = document.getElementById('msgbar'); 
    } else {
        return;
    }
    if (msgbar.innerHTML) {
        msgbar.innerHTML = msgbar.innerHTML + msg;
    } else {
        msgbar.innerHTML = msg;
    }

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

function addBookmark(obj, url){
    if (window.sidebar) { 
        // Firefox
        window.sidebar.addPanel(url.title, url.href,'');
    } else if (window.opera) {
        // Opera
        var a = document.createElement("A");
        a.rel = "sidebar";
        a.target = "_search";
        a.title = url.title;
        a.href = url.href;
        a.click();
    } else if (document.all) { 
        // IE
        window.external.AddFavorite(url.href, url.title);
    } else {
        alert("Your browser does not support automatic bookmarks. Please try to bookmark this page manually instead.");
    }
}

/**
 * A hastily written helper function for highlightWord() that iterates over an array of keywords
 */
function highlight(node, keywords) {
    // Sometimes keyword is blank... in that case, just return
    if ('' == keywords) {
        return;
    }
    
    // Sort in reverse. If a word is a fragment of another word on this list, it will highlight the larger
    // word first
    keywords.sort();
    keywords.reverse();

    // Highlight each word
    for (var i in keywords) {
        highlightWord(node, keywords[i]);
    }
}

/**
 * Recursively searches the dom for a keyword and highlights it by appliying a class selector called
 * 'highlight'
 *
 * @param node object
 * @param keyword string
 */ 
function highlightWord(node, keyword) {
	// Iterate into this nodes childNodes
	if (node && node.hasChildNodes) {
		var hi_cn;
		for (hi_cn=0;hi_cn<node.childNodes.length;hi_cn++) {
			highlightWord(node.childNodes[hi_cn],keyword);
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
                if(_v != elements[i].value) {
                    ; //this logic is broken... needs a complete rewrite
                }
            }
            if (e_type == 'checkbox' || e_type == 'radio') {
                var _v = elements[i].checked ? 'on' : 'off';  
                if(_v != elements[i].getAttribute('_value')) {
                    changed = true;  
                }
            }
        } else if (tag_name == 'SELECT') {
            var _v = elements[i].getAttribute('_value');    
            if(typeof(_v) == 'undefined')   _v = '';    
            if(_v != elements[i].options[elements[i].selectedIndex].value) {
                changed = true;  
            }
        } else if (tag_name == 'TEXTAREA') {
            var _v = elements[i].getAttribute('_value');
            if(typeof(_v) == 'undefined')   _v = '';
            var textarea_val = elements[i].value ? elements[i].value : elements[i].innerHTML;
            if(_v != textarea_val) {
                changed = true;
            }
        }
    }

    if(changed) {
        if (confirm('WARNING: You have unsaved changes on the page. If you continue, these'
                  + ' changes will be lost. If you want to save your changes, click "Cancel"' 
                  + ' now and then click "Save Changes".')) {
            return true;
        }
        else {
            return false;
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

function updateTimeField(id) {
    var hiddenEl = document.getElementById(id);
    var hourEl = document.getElementById(id + 'Hour');
    var minuteEl = document.getElementById(id + 'Minute');
    var ampmEl = document.getElementById(id + 'Ampm');
    
    var hour = hourEl.value;
    var minute = minuteEl.value;
    var ampm = ampmEl.value;
    
    if ('PM' == ampm) {
        hour = parseInt(hour) + 12;
    }
    
    hour = $P.str_pad(hour, 2, '0', 'STR_PAD_LEFT');
    minute = $P.str_pad(minute, 2, '0', 'STR_PAD_LEFT');    
    
    var time = hour + ':' + minute + ':00';
    hiddenEl.value = time;
}
