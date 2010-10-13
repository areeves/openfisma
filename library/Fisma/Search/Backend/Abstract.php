<?php
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
 */

/**
 * Abstract base class for search engine backends
 *
 * @author     Mark E. Haase <mhaase@endeavorystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2010 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Fisma
 * @subpackage Fisma_Search
 */
abstract class Fisma_Search_Backend_Abstract
{
    /**
     * True if highlighting should be turned on
     */
    private $_highlightingEnabled = true;

    /**
     * Search results are limited to this number of characters per field
     *
     * @var int
     */
    private $_maxRowLength = 100;

    /**
     * Delete all documents in the index
     */
    abstract public function deleteAll();

    /**
     * Delete all documents of the specified type in the index
     *
     * "Type" refers to a model, such as Asset, Finding, Incident, etc.
     *
     * @param string $type
     */
    abstract public function deleteByType($type);

    /**
     * Delete the specified object from the index
     *
     * $type must have a corresponding table class which implements Fisma_Search_Searchable
     *
     * @param string $type The class of the object
     * @param array $object
     */
    abstract public function deleteObject($type, $object);

    /**
     * Index an array of objects
     *
     * @param string $type The class of the object
     * @param array $collection
     */
    abstract public function indexCollection($type, $collection);

    /**
     * Add the specified object (in array format) to the search engine index
     *
     * This will overwrite any existing object with the same luceneDocumentId
     *
     * @param string $type The class of the object
     * @param array $object
     */
    abstract public function indexObject($type, $object);

    /**
     * Optimize the index (degfragments the index)
     */
    abstract public function optimizeIndex();

    /**
     * Simple search: search all fields for the specified keyword
     *
     * @param string $type Name of model index to search
     * @param string $keyword
     * @param string $sortColumn Name of column to sort on
     * @param boolean $sortDirection True for ascending sort, false for descending
     * @param int $start The offset within the result set to begin returning documents from
     * @param int $rows The number of documents to return
     * @param bool $deleted If true, include soft-deleted records in the results
     * @return Fisma_Search_Result
     */
    abstract public function searchByKeyword($type, $keyword, $sortColumn, $sortDirection, $start, $rows, $deleted);

    /**
     * Advanced search: search based on a list of specific field criteria
     *
     * @param string $type Name of model index to search
     * @param Fisma_Search_Criteria $criteria
     * @param string $sortColumn Name of column to sort on
     * @param boolean $sortDirection True for ascending sort, false for descending
     * @param int $start The offset within the result set to begin returning documents from
     * @param int $rows The number of documents to return
     * @param bool $deleted If true, include soft-deleted records in the results
     * @return Fisma_Search_Result Rectangular array of search results
     */
    abstract public function searchByCriteria(
        $type,
        Fisma_Search_Criteria $criteria,
        $sortColumn,
        $sortDirection,
        $start,
        $rows,
        $deleted
    );

    /**
     * Validate the backend's configuration
     *
     * The implementing class should use this to exercise basic diagnostics
     *
     * @return mixed Return TRUE if configuration is valid, or a string error message otherwise
     */
    abstract public function validateConfiguration();

    /**
     * Escape a parameter for inclusion in a Lucene query
     *
     * @see http://lucene.apache.org/java/2_4_0/queryparsersyntax.html#Escaping%20Special%20Characters
     *
     * @param string $parameter
     * @return string Escaped parameter
     */
    public function escape($parameter)
    {
        $specialChars = '+-!(){}[]^"~*?:\&|';

        return addcslashes($parameter, $specialChars);
    }

    /**
     * Get Max Row Length
     *
     * @return int
     */
    public function getMaxRowLength()
    {
        return $this->_maxRowLength;
    }

    /**
     * Set Max Row Length
     *
     * Set to null to turn off row length limit
     *
     * @param int $length
     */
    public function setMaxRowLength($length)
    {
        $this->_maxRowLength = $length;
    }

    /**
     * Get whether highlighting is enabled or not
     *
     * @return bool
     */
    public function getHighlightingEnabled()
    {
        return $this->_highlightingEnabled;
    }

    /**
     * Control highlighting behavior
     *
     * @param bool $enabled
     */
    public function setHighlightingEnabled($enabled)
    {
        $this->_highlightingEnabled = $enabled;
    }

    /**
     * Convert HTML string to a form that is ideal for text indexing
     *
     * This removes tags but ensures that the removal of tags does not result in separate words being concatenated
     * together.
     *
     * Notice that malformed HTML inputs may be mangled by this method.
     *
     * @param string $htmlString
     * @return string
     */
    protected function _convertHtmlToIndexString($html)
    {
        // Remove line feeds. They are replaced with spaces to prevent the next word on the next line from adjoining
        // the last word on the previous line, but consecutive spaces are culled out later.
        $html = str_replace(chr(10), ' ', $html);
        $html = str_replace(chr(13), ' ', $html);

        // Remove tags, but be careful not to concatenate together two words that were split by a tag
        $html = preg_replace('/(\w)<.*?>(\W)/', '$1$2', $html);
        $html = preg_replace('/(\W)<.*?>(\w)/', '$1$2', $html);
        $html = preg_replace('/<.*?>/', ' ', $html);

        // Decode entities (this way we don't index words like 'lt', 'rt', and 'amp')
        $html = html_entity_decode($html);

        // Remove excess whitespace
        $html = preg_replace('/[ ]*\R[ ]*/', "\n", $html);
        $html = preg_replace('/^\s+/', '', $html);
        $html = preg_replace('/\s+$/', '', $html);
        $html = preg_replace('/ +/', ' ', $html);

        // Character set encoding -- input charset is a guess
        $html = iconv('ISO-8859-1', 'UTF-8//TRANSLIT//IGNORE', $html);

        return $html;
    }
}
