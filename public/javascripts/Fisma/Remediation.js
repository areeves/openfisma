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
 * @fileoverview This file contains related javascript code about the feature finding remediation
 *
 * @author    Jackson Yang <yangjianshan@users.sourceforge.net>
 * @copyright (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license   http://www.openfisma.org/content/license
 */

Fisma.Remediation = {
    /**
     * Popup a panel for upload evidence
     *
     * @return {Boolean} False to interrupt consequent operations
     */
    uploadEvidence : function() {
        Fisma.UrlPanel.showPanel(
            'Upload Evidence',
            '/finding/remediation/upload-form',
            function(panel) {
                // Initialize form action from finding_detail.action since they are separated forms and the form from
                // from the panel belongs to document body rather than the form document.finding_detail.But they should
                // have same target action. So set the latter`s action with the former`s.
                document.finding_detail_upload_evidence.action = document.finding_detail.action;
                // make the add another upload button YUI
                var addAnotherFileButton = new YAHOO.widget.Button("add-another-file-button");
                // YUI strips away the classes, replace them
                YAHOO.util.Dom.addClass("add-another-file-button", "ie7-only");
                YAHOO.util.Dom.addClass("add-another-file-button", "ie8-only");
                YAHOO.util.Dom.addClass("add-another-file-button", "ie9-only");
                // add the appropriate event listener to the button
                YAHOO.util.Event.addListener("add-another-file-button", "click", Fisma.Remediation.addUploadEvidence);
                // make the submit button a YUI widget
                var inputs = panel.body.getElementsByTagName("input");
                var i;
                for (i in inputs) {
                    if (inputs[i].type === 'submit') {
                        var submitButton = new YAHOO.widget.Button(inputs[i]);
                    }
                }
            }
        );
        return false;
    },

   /**
     * Popup a panel to approve or deny mitigation strategy or evidence
     *
     * @param {Event} event     The event object
     * @param {Object} args     The actual argument array in an object form
     * {String} args.action     The action name: APPROVED or DENIED
     * {String} args.formId     The HTML id of the original form
     * {String} args.panelTitle The text shown on the panel
     * {int}    args.findingId  The id of the current finding
     */
    remediationAction : function(event, args) {
        var action = args.action;
        var formId = args.formId;
        var panelTitle = args.panelTitle;
        var findingId = args.findingId;
        var panel;

        if ('REJECTED' === action) {
            panel = Fisma.UrlPanel.showPanel(
                panelTitle,
                '/finding/remediation/reject-evidence/id/' + findingId,
                function(){
                    document.finding_detail_reject_evidence.action = document.finding_detail.action;
                    var rejectEvidenceButton = new YAHOO.widget.Button(
                        YAHOO.util.Selector.query("input[type=submit]", "finding_detail_reject_evidence", true)
                    );
                    var closeDialogFunction = function() {
                        panel.destroy();
                    };
                    var closeButton = new YAHOO.widget.Button("dialog_close", {onclick: {fn: closeDialogFunction}});
                }
            );
        } else {
            var content = document.createElement('div');
            var warning = document.createElement('div');
            warning.className = 'messageBox attention';
            var warn_message = 'WARNING: This action cannot be undone.';
            warning.appendChild(document.createTextNode(warn_message));
            content.appendChild(warning);
            var p = document.createElement('p');
            var c_title;
            if ('APPROVED' === action) {
                c_title = document.createTextNode('Comments (OPTIONAL):');
            } else {
                c_title = document.createTextNode('Comments:');
            }
            p.appendChild(c_title);
            content.appendChild(p);
            var textarea = document.createElement('textarea');
            textarea.id = 'dialog_comment';
            textarea.name = 'comment';
            textarea.rows = 5;
            textarea.cols = 60;
            content.appendChild(textarea);
            var div = document.createElement('div');
            div.className = 'buttonBar';
            content.appendChild(div);
            var confirmButton = document.createElement('button');
            confirmButton.id = 'dialog_continue';
            confirmButton.appendChild(document.createTextNode('Confirm'));
            div.appendChild(confirmButton);
            var cancelButton = document.createElement('button');
            cancelButton.id = 'dialog_close';
            cancelButton.style.marginLeft = '5px';
            cancelButton.appendChild(document.createTextNode('Cancel'));
            div.appendChild(cancelButton);

            panel = Fisma.HtmlPanel.showPanel(panelTitle, content.innerHTML);

            var continueButton = new YAHOO.widget.Button("dialog_continue", {onclick: {fn: function () {
                var form2 = document.getElementById(formId);
                var comment = document.getElementById('dialog_comment').value;

                if ('DENIED' === action) {
                    if (comment.match(/^\s*$/)) {
                        var alertMessage = 'Comments are required.';
                        var config = {zIndex : 10000};
                        Fisma.Util.showAlertDialog(alertMessage, config);
                        return;
                    }
                }

                form2.elements.comment.value = comment;
                form2.elements.decision.value = action;

                var sub = document.createElement('input');
                sub.type = 'hidden';
                sub.name = 'submit_msa';
                sub.value = action;
                form2.appendChild(sub);
                form2.submit();
                return;
            }}});

            var closeButton = new YAHOO.widget.Button("dialog_close", {onclick: {fn: function () {
                panel.destroy();
                return false;
            }}});
        }
        return true;
    },

    /**
     * Handle onclick event of the button on the Evidence upload form
     * to attach one more file
     */
    addUploadEvidence : function() {
        var file_list = document.getElementById('evidence_upload_file_list');

        var new_upload = document.createElement('input');
        new_upload.type = 'file';
        new_upload.name = 'evidence[]';
        new_upload.multiple = true;
        file_list.appendChild(new_upload);

        YAHOO.util.Event.preventDefault(event);
        return false;
    },

    /**
     * Validate the reject_evidence form for required field(s)
     */
    rejectEvidenceValidate : function() {
        if (document.finding_detail_reject_evidence.comment.value.match(/^\s*$/)) {
            var alertMessage = 'Comments are required.';
            var config = {zIndex : 10000};
            Fisma.Util.showAlertDialog(alertMessage, config);
            return false;
        }
        return true;
    },

    /**
     * Validate the upload_evidence form to check for duplicated uploads
     */
    uploadEvidenceValidate : function(event) {
        if (document.finding_detail_upload_evidence.forceSubmit) {
            return true;
        }
        var duplicationDetected = false;
        var message = "WARNING: The following file(s) will be replaced: <ul>";

        var i;
        for (i = 0; i < document.links.length; i++) {
            var link = document.links[i];

            if (link.href.indexOf('download-evidence') >= 0 && link.lastChild.nodeName !== 'IMG') {
                var files = document.finding_detail_upload_evidence['evidence[]'].files;
                var j, fileName;
                if (!files) // this ugly chunk is the workaround for IE7
                {
                    var elements = document.finding_detail_upload_evidence.elements;
                    for (j = 0; j < elements.length; j++) {
                        if (elements[j].name === 'forceSubmit') {
                            return true;
                        }
                        if (elements[j].name === 'evidence[]') {
                            fileName = elements[j].value;
                            fileName = fileName.slice(fileName.lastIndexOf('\\')+1);
                            if (fileName === link.lastChild.data) {
                                duplicationDetected = true;
                                message += "<li>" + fileName + "</li>";
                            }
                        }
                    }
                } else {
                    for (j = 0; j < files.length; j++) {
                        fileName = (!files[j].fileName) ? files[j].name : files[j].fileName;
                        if (fileName === link.lastChild.data) {
                            duplicationDetected = true;
                            message += "<li>" + fileName + "</li>";
                            break;
                        }
                    }
                }
            }
        }

        message += "</ul>Do you want to continue?";
        if (duplicationDetected) {
            Fisma.Util.showConfirmDialog(
                event,
                {
                    text:message,
                    func:'Fisma.Remediation.uploadEvidenceConfirm'
                }
            );
            return false;
        } else {
            return true;
        }
    },

    /**
     * Force the submission of upload_evidence form
     */
    uploadEvidenceConfirm : function() {
        var forcedIndicator = document.createElement('input');
        forcedIndicator.type = 'hidden';
        forcedIndicator.name = 'forceSubmit';
        forcedIndicator.value = true;
        document.finding_detail_upload_evidence.appendChild(forcedIndicator);
        document.finding_detail_upload_evidence.upload_evidence.click();
    },

    /**
     * A static reference to the Source create form panel
     */
    createSourcePanel : null,

    /**
     * Display Source create form panel
     */
    displaySourcePanel : function (element) {
        if (element.value === 'new') {
            var panelConfig = {width : "700px", modal : true};

            Fisma.Remediation.createSourcePanel = Fisma.UrlPanel.showPanel(
                'Create New Finding Source',
                '/finding/source/form',
                function () {
                    var sourceMessageBox = new Fisma.MessageBox(document.getElementById("sourceMessageBar"));
                    Fisma.Registry.get("messageBoxStack").push(sourceMessageBox);

                    // The form contains some scripts that need to be executed
                    var scriptNodes = Fisma.Remediation.createSourcePanel.body.getElementsByTagName('script');

                    var i;
                    for (i = 0; i < scriptNodes.length; i++) {
                        try {
                            eval(scriptNodes[i].text);
                        } catch (e) {
                            var message = 'Not able to execute one of the scripts embedded in this page: ' + e.message;
                            Fisma.Util.showAlertDialog(message);
                        }
                    }
                },
                'createSourcePanel',
                panelConfig
            );

            Fisma.Remediation.createSourcePanel.subscribe("hide", function() {
                Fisma.Registry.get("messageBoxStack").pop();
                setTimeout(function () {
                    Fisma.Remediation.createSourcePanel.destroy();
                    Fisma.Remediation.createSourcePanel = null;
                }, 0);
            }, this, true);
        }
    },

    /**
     * Submit an XHR to create a Finding Source
     */
    createSource : function () {
        // The scope is the button that was clicked, so save it for closures
        var saveButton = this;
        var form = Fisma.Remediation.createSourcePanel.body.getElementsByTagName('form')[0];

        // Disable the submit button
        saveButton.set("disabled", true);

        // Save the username so we can populate it back on the create finding form
        var sourceName = document.getElementById("name").value;

        YAHOO.util.Connect.setForm(form);
        YAHOO.util.Connect.asyncRequest('POST', '/finding/source/create/format/json', {
            success : function(o) {
                var result;

                try {
                    result = YAHOO.lang.JSON.parse(o.responseText).result;
                } catch (e) {
                    result = {success : false, message : e};
                }

                if (result.success) {
                    Fisma.Remediation.createSourcePanel.hide();

                    // Insert the new source into the <select>
                    var sourceId = parseInt(result.message, 10);
                    var newOption = document.createElement('option');
                    newOption.value = sourceId;
                    newOption.appendChild(document.createTextNode(sourceName));
                    newOption.selected = true;
                    jQuery('#sourceId > option[value="new"]').after(newOption);

                    // Reflect the change in the YUI Select Menu Button
                    var selectButton = YAHOO.widget.Button.getButton('sourceId-button');
                    var newSource = selectButton.getMenu().addItem({
                        'text': sourceName,
                        'value': sourceId
                    });
                    selectButton.set('selectedMenuItem', newSource);
                    selectButton.set('label', sourceName);

                    Fisma.Util.message('A finding source has been created.', 'info', true);
                } else {
                    Fisma.Util.message(result.message, 'warning', true);
                    saveButton.set("disabled", false);
                }
            },
            failure : function(o) {
                var alertMessage = 'Failed to create new finding source: ' + o.statusText;
                Fisma.Remediation.createSourcePanel.setBody(alertMessage);
            }
        }, null);
    },

    completeMilestone : function(element, enabled) {
        if (!enabled) {
            Fisma.Util.showAlertDialog('A milestone can only be completed during EN phase.');
            return false;
        }
        var e = jQuery('img', element);
        if (e.attr('src').indexOf('check_empty.png') >= 0) {
            e.attr('src', '/images/check.png');
            e.parent().after('<span>Completed by Sample User on 2012-03-29 at 08:41 AM</span>');
        }
        return false;
    }
};
