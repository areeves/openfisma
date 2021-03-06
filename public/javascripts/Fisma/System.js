/**
 * Copyright (c) 2010 Endeavor Systems, Inc.
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
 * @fileoverview Provides client-side behavior for the AttachArtifacts behavior
 * 
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2010 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/content/license
 * @version   $Id: AttachArtifacts.js 3188 2010-04-08 19:35:38Z mhaase $
 */
 
Fisma.System = {
    /**
     * Called when a system document finishes uploading
     * 
     * Eventually it would be nifty to refresh the YUI table but for now we will just refresh the entire page
     */
    uploadDocumentCallback : function (yuiPanel) {
        window.location.href = window.location.href;
    },

    /**
     * Displays the hidden block on the FIPS-199 page to add information types to a system 
     */
    showInformationTypes : function () {
        document.getElementById('addInformationTypes').style.display = 'block';
    },

    /**
     * Build URL for adding information type to the system 
     */
    addInformationType : function (elCell, oRecord, oColumn, oData) {
        elCell.innerHTML = "<a href='/system/add-information-type/id/" + oRecord.getData('organization') + "/sitId/" + oData + "'>Add</a>";
    },


    /**
     * Build URL for removing information types from a system 
     */
    removeInformationType : function (elCell, oRecord, oColumn, oData) {
        elCell.innerHTML = "<a href='/system/remove-information-type/id/" + oRecord.getData('organization') + "/sitId/" + oData + "'>Remove</a>";
    }
};
