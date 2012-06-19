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
 * {@link http://www.gnu.org/licenses/}.
 *
 * @fileoverview Provides various formatters for use with YUI table
 *
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2010 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/content/license
 */

Fisma.TableFormat = {
    /**
     * CSS green color
     */
    greenColor : 'lightgreen',

    /**
     * CSS yellow color
     */
    yellowColor : 'yellow',

    /**
     * CSS red color
     */
    redColor : 'pink',

    /**
     * Color an element green
     */
    green : function (element) {
        element.style.backgroundColor = Fisma.TableFormat.greenColor;
    },

    /**
     * Color an element yellow
     */
    yellow : function (element) {
        element.style.backgroundColor = Fisma.TableFormat.yellowColor;
    },

    /**
     * Color an element red
     */
    red : function (element) {
        element.style.backgroundColor = Fisma.TableFormat.redColor;
    },

    /**
     * A formatter which colors the security authorization date in red, yellow, or green (or not at all)
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    securityAuthorization : function (elCell, oRecord, oColumn, oData) {
        elCell.innerHTML = oData;

        // Date format is YYYY-MM-DD. Convert into javascript date object.
        var dateParts = oData.split('-');

        if (3 === dateParts.length) {

            var authorizedDate = new Date(dateParts[0], dateParts[1], dateParts[2]);

            var greenDate = new Date();
            greenDate.setMonth(greenDate.getMonth() - 30);

            var yellowDate = new Date();
            yellowDate.setMonth(yellowDate.getMonth() - 36);

            if (authorizedDate >= greenDate) {
                Fisma.TableFormat.green(elCell.parentNode);
            } else if (authorizedDate >= yellowDate) {
                Fisma.TableFormat.yellow(elCell.parentNode);
            } else {
                Fisma.TableFormat.red(elCell.parentNode);
            }
        }
    },

    /**
     * A formatter which colors the self-assessment date in red, yellow, or green (or not at all)
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    selfAssessment : function (elCell, oRecord, oColumn, oData) {
        elCell.innerHTML = oData;

        // Date format is YYYY-MM-DD. Convert into javascript date object.
        var dateParts = oData.split('-');

        if (3 === dateParts.length) {

            var assessmentDate = new Date(dateParts[0], dateParts[1], dateParts[2]);

            var greenDate = new Date();
            greenDate.setMonth(greenDate.getMonth() - 8);

            var yellowDate = new Date();
            yellowDate.setMonth(yellowDate.getMonth() - 12);

            if (assessmentDate >= greenDate) {
                Fisma.TableFormat.green(elCell.parentNode);
            } else if (assessmentDate >= yellowDate) {
                Fisma.TableFormat.yellow(elCell.parentNode);
            } else {
                Fisma.TableFormat.red(elCell.parentNode);
            }
        }
    },

    /**
     * A formatter which wrap the img element around the source URI
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    imageControl : function (elCell, oRecord, oColumn, oData) {
        var img = document.createElement('img');
        img.src = oData;
        elCell.appendChild(img);
    },

    /**
     * A proxy for selfAssessment() above -- they have identical formatting logic
     */
    contingencyPlanTest : function (elCell, oRecord, oColumn, oData) {
        Fisma.TableFormat.selfAssessment(elCell, oRecord, oColumn, oData);
    },

    /**
     * A formatter which colors cells green if the value is YES, and red if the value is NO
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    yesNo : function (elCell, oRecord, oColumn, oData) {
        elCell.innerHTML = oData;

        if ('YES' === oData) {
            Fisma.TableFormat.green(elCell.parentNode);
        } else if ('NO' === oData) {
            Fisma.TableFormat.red(elCell.parentNode);
        }
    },

    /**
     * A formatter which displays an edit icon that is linked to an edit page
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
     editControl : function (elCell, oRecord, oColumn, oData) {

        var icon = document.createElement('img');
        icon.src = '/images/edit.png';

        var link = document.createElement('a');
        link.href = oData;
        link.appendChild(icon);

        elCell.appendChild(link);
    },

    /**
     * A formatter which displays a delete icon that is linked to a delete action
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    deleteControl : function (elCell, oRecord, oColumn, oData) {

        var icon = document.createElement('img');
        icon.src = '/images/del.png';

        elCell.appendChild(icon);
        YAHOO.util.Event.on(icon, "click", function() {
            Fisma.Util.formPostAction(null, oData, null);
        });
    },

    /**
     * A formatter which used to convert escaped HTML into unescaped HTML ...
     * Now it uses the default formatter, for a few reasons:
     *    1. We don't store html unescaped anymore, unless it's unsafe html
     *    2. The javascript in data-table-local.phtml is wrong, and doesn't execute YAHOO formatters properly
     *    3. Using this as it was resulted in an XSS vulnerability, and I don't have time to rewrite the entire
     *       implementation so that it works properly.
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     * @TODO Fix data-table-local.phtml script so that it works with YAHOO formatters
     * @deprecated
     */
    formatHtml : function(el, oRecord, oColumn, oData) {
        YAHOO.widget.DataTable.formatDefault.apply(this, arguments);
    },

    /**
     * A formatter which displays the total of overdue findings that is linked to a finding search page
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    overdueFinding : function (elCell, oRecord, oColumn, oData) {

        // Construct overdue finding search url
        var overdueFindingSearchUrl = '/finding/remediation/list?q=';

        // Handle organization field
        var organization = oRecord.getData('System');

        if (organization) {

            // Since organization may be html-encoded, decode the html before (url)-escaping it
            organization = $P.html_entity_decode(organization);

            overdueFindingSearchUrl += "/organization/textExactMatch/" + encodeURIComponent(organization);
        }

        // Handle status field
        var status = oRecord.getData('Status');

        if (status) {
            status = PHP_JS().html_entity_decode(status);
            overdueFindingSearchUrl += "/denormalizedStatus/enumIs/" + encodeURIComponent(status);
        }

        // Handle source field
        var parameters = oColumn.formatterParameters;

        if (parameters.source) {
            overdueFindingSearchUrl += "/source/textExactMatch/" + encodeURIComponent(parameters.source);
        }

        // Handle date fields
        var from = null;

        if (parameters.from) {
            var fromDate = new Date();
            fromDate.setDate(fromDate.getDate() - parseInt(parameters.from, 10));

            from = fromDate.getFullYear() + '-' + (fromDate.getMonth() + 1) + '-' + fromDate.getDate();
        }

        var to = null;

        if (parameters.to) {
            var toDate = new Date();
            toDate.setDate(toDate.getDate() - parseInt(parameters.to, 10));

            to = toDate.getFullYear() + '-' + (toDate.getMonth() + 1) + '-' + toDate.getDate();
        }

        if (from && to) {
            overdueFindingSearchUrl += "/nextDueDate/dateBetween/" +
                                        encodeURIComponent(to) +
                                        "/" +
                                        encodeURIComponent(from);
        } else if (from) {
            overdueFindingSearchUrl += "/nextDueDate/dateBefore/" + encodeURIComponent(from);
        } else {

            // This is the TOTAL column
            var todayString = $P.date('Y-m-d');
            overdueFindingSearchUrl += "/nextDueDate/dateBefore/" + encodeURIComponent(todayString);
        }

        elCell.innerHTML = '<a href="' + overdueFindingSearchUrl + '">' + oData + "</a>";
    },

    /**
     * A formatter which colors the the percentage of the required documents
     * which system has completed in red, yellow, or green (or not at all)
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    completeDocTypePercentage : function (elCell, oRecord, oColumn, oData) {
        var percentage = parseInt(oData, 10);

        if (oData !== null) {
            elCell.innerHTML = oData + "%";

            if (percentage >= 95 && percentage <= 100) {
                Fisma.TableFormat.green(elCell.parentNode);
            } else if (percentage >= 80 && percentage < 95) {
                Fisma.TableFormat.yellow(elCell.parentNode);
            } else if (percentage >= 0 && percentage < 80) {
                Fisma.TableFormat.red(elCell.parentNode);
            }
        }
    },

    /**
     * A formatter which displays the missing document type name
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    incompleteDocumentType : function (elCell, oRecord, oColumn, oData) {
        var docTypeNames = '';
        if (oData.length > 0) {
            docTypeNames += '<ul><li>';
            docTypeNames += oData.replace(/,/g, '</li><li>');
            docTypeNames += '</li></ul>';
        }

        elCell.innerHTML = docTypeNames;
    },

    /**
     * Creates a checkbox element that can be used to select the record. If the model has soft delete and
     * any of the records are deleted, then the checkbox is replaced by an icon so that user's don't try to
     * "re-delete" any already-deleted items.
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    formatCheckbox : function (elCell, oRecord, oColumn, oData) {

        if (oRecord.getData('deleted_at')) {

            elCell.parentNode.style.backgroundColor = "pink";

        } else {
            var checkbox = document.createElement("input");
            checkbox.type = "checkbox";
            checkbox.className = YAHOO.widget.DataTable.CLASS_CHECKBOX;
            checkbox.checked = oData;

            if (elCell.firstChild) {
                elCell.removeChild(elCell.firstChild);
            }

            elCell.appendChild(checkbox);
        }
    },

    /**
     * A formatter which displays the size of file with unit
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    formatFileSize : function (elCell, oRecord, oColumn, oData) {
        // Convert to number
        var size = parseInt(oData, 10);

        if(YAHOO.lang.isNumber(size)) {
            if (size < 1024) {
                size = size + ' bytes';
            } else if (size < (1024 * 1024)) {
                size = (size / 1024).toFixed(1) + ' KB';
            } else if (size < (1024 * 1024 * 1024)) {
                size = (size / (1024 * 1024)).toFixed(1) + ' MB';
            } else {
                size = (size / (1024 * 1024 * 1024)).toFixed(1) + ' GB';
            }

            elCell.innerHTML = size;
        }
    },

    /**
     * Show a control that can be used to remove the current record from the table.
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    remover: function (elCell, oRecord, oColumn, oData) {
        // Put table in closure scope
        var table = this;

        var img = document.createElement('img');
        img.src = '/images/delete_row.png';
        YAHOO.util.Event.on(
            img,
            "click",
            function () {
                YAHOO.util.Event.removeListener(img, "click");
                img.src = "/images/spinners/small.gif";

                Fisma.Incident.removeUser(
                    oRecord.getData('incidentId'),
                    oRecord.getData('userId'),
                    table
                );
            }
        );

        elCell.appendChild(img);
    },

    /**
     * A formatter which displays Yes or No for a boolean value
     *
     * The highlighting engine seems to do something very strange with boolean values:
     * true, no highlight: true (boolean value)
     * false, no highlight: false (boolean value)
     * true, highlighted: "******T" (string value)
     * false, highlighted: "******F" (string value)
     * The code below compensates for this.
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    formatBoolean : function (elCell, oRecord, oColumn, oData) {
        var cell = $(elCell);
        if (oData === true) {
            cell.text("Yes");
        } else if (oData === false) {
            cell.text("No");
        } else {
            cell.html($("<span/>").addClass("highlight").text(oData.substr(-1) === "T" ? "Yes" : "No"));
        }
    },

    /**
     * A formatter which displays buttons for task operation
     *
     * @param elCell Reference to a container inside the <td> element
     * @param oRecord Reference to the YUI row object
     * @param oColumn Reference to the YUI column object
     * @param oData The data stored in this cell
     */
    formatTaskAction : function (elCell, oRecord, oColumn, oData) {
        var deleteButton= new YAHOO.widget.Button({
            label: "Delete",
            id: YAHOO.util.Dom.generateId(),
            container: elCell,
            onclick: {
                fn: Fisma.Task.deleteRecord,
                obj: {
                    id : oRecord.getData('id'),
                    objectId: oRecord.getData('objectId'),
                    type: "Finding"
                }
            },
            disabled: true
        });

        var findingStatus = oRecord.getData('findingStatus');
        if (findingStatus === 'NEW' || findingStatus === 'DRAFT') {
            deleteButton.set('disabled', false);
        }

        var commentButton = new YAHOO.widget.Button({
            label: "Add Comment",
            id: YAHOO.util.Dom.generateId(),
            container: elCell,
            onclick: {
                fn: Fisma.Task.showCommentPanel,
                obj: {
                    id : oRecord.getData('id'),
                    objectId: oRecord.getData('objectId'),
                    type: "Finding",
                    target: oRecord,
                    callback: {object: "Task", method: "handleCommentCallback"}
                }
            }
        });

        var menuButton = new YAHOO.widget.Button({
            label: "Change Status",
            type: "menu",
            menu: [
                {text: "OPEN", onclick: {
                    fn: Fisma.Task.statusMenuItemClick,
                    obj: {
                        id: oRecord.getData('id'),
                        objectId: oRecord.getData('objectId'),
                        target: oRecord,
                        type: 'Finding'
                    }
                }},
                {text: "PENDING", onclick: {
                    fn: Fisma.Task.statusMenuItemClick,
                    obj: {
                        id: oRecord.getData('id'),
                        objectId: oRecord.getData('objectId'),
                        target: oRecord,
                        type: 'Finding'
                    }
                }},
                {text: "CLOSED", onclick: {
                    fn: Fisma.Task.statusMenuItemClick,
                    obj: {
                        id: oRecord.getData('id'),
                        objectId: oRecord.getData('objectId'),
                        target: oRecord,
                        type: 'Finding'
                    }
                }}
            ],
            container: elCell,
            disabled: true
        });

        if (findingStatus === 'EN') {
            menuButton.set('disabled', false);
        }
    }
};
