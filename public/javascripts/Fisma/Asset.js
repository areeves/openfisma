/**
 * Copyright (c) 2012 Endeavor Systems, Inc.
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
 * @author    Duy K. Bui <duy.bui@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2012 {@link http://www.endeavorsystems.com}
 * @license   http://www.openfisma.org/content/license
 */

Fisma.Asset = {
    /**
     * Popup a panel for upload evidence
     *
     * @return {Boolean} False to interrupt consequent operations
     */
    addService : function() {
        /*Fisma.UrlPanel.showPanel(
            'Upload Evidence',
            '/finding/remediation/upload-form',
            function(panel) {
                // Initialize form action from finding_detail.action since they are separated forms and the form from
                // from the panel belongs to document body rather than the form document.finding_detail.But they should
                // have same target action. So set the latter`s action with the former`s.
                // document.finding_detail_upload_evidence.action = document.finding_detail.action;
                // make the submit button a YUI widget
                var inputs = panel.body.getElementsByTagName("input");
                for (var i in inputs) {
                    if (inputs[i].type === 'submit') {
                        new YAHOO.widget.Button(inputs[i]);
                    }
                }
            }
        );*/
        return false;
    },
};
