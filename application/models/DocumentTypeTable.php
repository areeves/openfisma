<?php
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
 */

/**
 * DocumentTypeTable 
 * 
 * @uses Fisma_Doctrine_Table
 * @package Model 
 * @copyright (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @author Josh Boyd <joshua.boyd@endeavorsystems.com> 
 * @license http://www.openfisma.org/content/license GPLv3
 */
class DocumentTypeTable extends Fisma_Doctrine_Table
{
    /**
     * Return the count of required document types
     * 
     * @return integer
     */
    public function getRequiredDocTypeCount()
    {
        $requiredDocTypeQuery = Doctrine_Query::create()
                                ->from('DocumentType')
                                ->where('required = ?', true);

        return $requiredDocTypeQuery->count();
    }

    /**
     * Return a list of missing document type names for specify systemid
     * 
     * @param  string $id The specified system id to get missing document types
     * @return string
     */
    public function getMissingDocumentTypeName($systemId)
    {
        if (empty($systemId)) {
            return null;
        }

        $documentTypes = Doctrine_Query::create()
                         ->select('IFNULL(GROUP_CONCAT(dt.name), \'N/A\') AS name')
                         ->from('DocumentType dt')
                         ->where('dt.required = ?', true)
                         ->andWhereNotIn('dt.id', $this->_getDocumentTypeIds($systemId))
                         ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
                         ->fetchOne();

        return $documentTypes['name'];
    }

    /**
     * Return a list of document type ids for specify systemid from system document table
     * 
     * @param  string $id The specified system id to get the array of document type id
     * @return array
     */
    protected function _getDocumentTypeIds($systemId)
    {
        if (empty($systemId)) {
            return null;
        }

        $documentTypeIds = Doctrine_Query::create()
                           ->select('sd.documenttypeid AS id')
                           ->from('SystemDocument sd')
                           ->where('sd.systemid = ?', $systemId)
                           ->execute();

        return $documentTypeIds->toKeyValueArray('id', 'id');
    }
}
