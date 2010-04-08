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
 * An abstract class for creating injection plug-ins
 * 
 * @author     Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Fisma
 * @subpackage Fisma_Inject
 * @version    $Id$
 */
abstract class Fisma_Inject_Abstract
{
    const CREATE_FINDING = 1;
    const DELETE_FINDING = 2;
    const REVIEW_FINDING = 3;

    /**
     * The full xml file path to be used to the injection plugin
     * 
     * @var string
     */
    protected $_file;
    
    /**
     * The network id to be used for injection
     * 
     * @var string
     */
    protected $_networkId;
    
    /**
     * The organization id to be used for injection
     * 
     * @var string
     */
    protected $_orgSystemId;

    /**
     * The finding source id to be used for injected 
     * 
     * @var string
     */
    protected $_findingSourceId;
    
    /**
     * The summary counts array
     * 
     * @var array
     */
    private $_totalFindings = array('created' => 0, 'deleted' => 0, 'reviewed' => 0);

    /**
     * collection of findings to be created 
     * 
     * @var array
     */
    private $_findings = array();

    /** 
     * Parse all the data from the specified file, and save it to the instance of the object by calling _save(), and 
     * then _commit() to commit to database.
     *
     * Throws an exception if the file is an invalid format.
     *
     * @param string $uploadId The primary key for the upload object associated with this file
     * @throws Fisma_Inject_Exception
     */
    abstract public function parse($uploadId);

    /**
     * Create and initialize a new plug-in instance for the specified file
     * 
     * @param string $file The specified xml file path
     * @param string $networkId The specified network id
     * @param string $systemId The specified organization id
     * @param string $findingSourceId The specified finding source id
     */
    public function __construct($file, $networkId, $systemId, $findingSourceId) 
    {
        $this->_file            = $file;
        $this->_networkId       = $networkId;
        $this->_orgSystemId     = $systemId;
        $this->_findingSourceId = $findingSourceId;
    }

    /**
     * The get handler method is overridden in order to provide read-only access to the summary counts for
     * this plug-in.
     *
     * Example: echo "Created {$plugin->created} findings";
     * 
     * @param string $field The specified summary counts key
     * @return int The summary count value of the specified key
     */
    public function __get($field) 
    {
        return (!empty($this->_totalFindings[$field])) ? $this->_totalFindings[$field] : 0;
    }

    /**
     * Save data to instance 
     * 
     * @param array $findingData 
     * @param array $assetData 
     * @param array $productData 
     */
    protected function _save($findingData, $assetData = NULL, $productData = NULL)
    {
        if (empty($findingData)) {
            throw new Fisma_Inject_Exception('Save cannot be called without finding data!');
        }

        // Add data to provided assetData
        if (!empty($assetData)) {
            $assetData['networkId'] = $this->_networkId;
            $assetData['orgSystemId'] = $this->_orgSystemId;
            $assetData['source'] = 'SCAN';

            $assetData['id'] = $this->_prepareAsset($assetData);
        }

        // Add data to provided productData
        if (!empty($productData)) {
            $assetData['productId'] = $this->_prepareProduct($productData);
        }

        // Prepare finding
        $finding = new Finding();
        $finding->merge($findingData);

        // Handle related objects, since merge doesn't
        if (!empty($findingData['cve'])) {
            foreach ($findingData['cve'] as $cve) {
                $finding->Cve[]->value = $cve;
            }
        }

        if (!empty($findingData['bugtraq'])) {
            foreach ($findingData['bugtraq'] as $bugtraq) {
                $finding->Bugtraq[]->value = $bugtraq;
            }
        }

        if (!empty($findingData['xref'])) {
            foreach ($findingData['xref'] as $xref) {
                $finding->Xref[]->value = $xref;
            }
        }

        // Handle duplicated findings
        $duplicateFinding = $this->_getDuplicateFinding($finding);
        $action = ($duplicateFinding) ? $this->_getDuplicateAction($finding, $duplicateFinding) : self::CREATE_FINDING;
        $finding->duplicateFindingId = ($duplicateFinding) ? $duplicateFinding['id']: NULL;

        // Take the specified action on the current finding
        switch ($action) {
            case self::CREATE_FINDING:
                $this->_totalFindings['created']++;
                break;
            case self::DELETE_FINDING:
                $this->_totalFindings['deleted']++;
                // Deleted findings are not saved, so we exit the _save routine
                $finding->free();
                unset($finding);
                return;
                break;
            case self::REVIEW_FINDING:
                $this->_totalFindings['reviewed']++;
                $finding->status = 'PEND';
                break;
        }

        // Store data in instance to be committed later
        $this->_findings[] = array('finding' => $finding, 'asset' => $assetData, 'product' => $productData);
    }

    /**
     * Commit all data that has been saved 
     *
     * Subclasses should call this function to commit findings rather than committing new findings directly.
     */
    protected function _commit() 
    {
        Doctrine_Manager::connection()->beginTransaction();

        try {
            foreach ($this->_findings as &$findingData) {
                if (@!$findingData['asset']['productId'] && !empty($findingData['product'])) {
                    $findingData['asset']['productId'] = $this->_saveProduct($findingData['product']);
                }

                if (!$findingData['asset']['id']) {
                    $findingData['asset']['id'] = $this->_saveAsset($findingData['asset']);
                }

                $findingData['finding']->assetId = $findingData['asset']['id'];
                $findingData['finding']->save();
                $findingData['finding']->free();
                unset($findingData['finding']);
            }

            Doctrine_Manager::connection()->commit();
        } catch (Exception $e) {
            Doctrine_Manager::connection()->rollBack();
            throw $e;
        }
    }

    /**
     * Get a duplicate of the specified finding
     * 
     * @param $finding A finding to check for duplicates
     * @return bool|Finding Return a duplicate finding or FALSE if none exists
     */
    private function _getDuplicateFinding($finding)
    {
        $duplicateFindings = Doctrine_Query::create()
            ->select('f.id, f.responsibleOrganizationId, f.type, f.status')
            ->from('Finding f')
            ->where('description LIKE ?', $finding->description)
            ->andWhere('status <> ?', 'PEND')
            ->setHydrationMode(Doctrine::HYDRATE_ARRAY)
            ->execute();

        return ($duplicateFindings) ? array_pop($duplicateFindings) : FALSE;
    }
    
    /**
     * Evaluate duplication rules for two findings
     * 
     * @param Finding $newFinding
     * @param Array $duplicateFinding
     * @return int One of the constants: CREATE_FINDING, DELETE_FINDING, or REVIEW_FINDING
     */
    private function _getDuplicateAction(Finding $newFinding, Array $duplicateFinding)
    {
        $action  = NULL;
        $orgSame = ($newFinding->ResponsibleOrganization->id == $duplicateFinding['responsibleOrganizationId']) ? TRUE 
            : FALSE;
        
        switch ($duplicateFinding['type']) {
            case 'CAP':
            case 'FP':
            case 'NONE':
                if ($orgSame) {
                    $action = ($duplicateFinding['status'] == 'CLOSED') ? self::CREATE_FINDING : self::DELETE_FINDING;
                } else {
                    $action = self::REVIEW_FINDING;
                }
                break;
            case 'AR':
                $action = ($orgSame) ? self::DELETE_FINDING : self::REVIEW_FINDING;
                break;
            default:
                throw new Fisma_Exception('No duplicate finding action defined for mitigation type: '
                    . $duplicateFinding['type']);
        }

        return $action;
    }

    /**
     * Get the existing asset id if it exists 
     * 
     * @param mixed $passetData 
     * @return int|boolean 
     */
    private function _prepareAsset($assetData)
    {
        // Verify whether asset exists or not
        $assetRecord = Doctrine_Query::create()
                        ->select('id')
                        ->from('Asset a')
                        ->where('a.networkId = ?', $assetData['networkId'])
                        ->andWhere('a.addressIp = ?', $assetData['addressIp'])
                        ->andWhere('a.addressPort = ?', $assetData['addressPort'])
                        ->setHydrationMode(Doctrine::HYDRATE_NONE)
                        ->execute();

        return ($assetRecord) ? $assetRecord[0][0] : FALSE;
    }

    /**
     * Save the asset
     *
     * @param array $assetData The asset data to save
     * @return int id of saved asset 
     */
    private function _saveAsset($assetData)
    {
        $asset = new Asset();

        $asset->merge($assetData);
        $asset->save();
        
        $id = $asset->id;

        // Check to see if any of the pending assets are duplicates, if so, update the finding to point to the correct 
        // asset id
        foreach ($this->_findings as &$findingData) {
            if (empty($findingData['finding']->Asset) && $findingData['asset'] == $assetData) {
                $findingData['asset']['id'] = $id;
            }
        }
        // Free object
        $asset->free();
        unset($asset);

        return $id;
    }

    /**
     * Get the existing product id if it exists_
     * 
     * @param array $productData 
     * @return int|boolean 
     */
    private function _prepareProduct($productData)
    {
        // Verify whether product exists or not
        $productRecordQuery = Doctrine_Query::create()
                              ->select('id')
                              ->from('Product p')
                              ->setHydrationMode(Doctrine::HYDRATE_NONE);

        // Match existing products on the CPE ID if it is available, otherwise match on name, vendor, and version
        if (isset($productData['cpeName'])) {
            $productRecordQuery->where('p.cpename = ?', $productData['cpeName']);
        } else {
            if (empty($productData['name'])) {
                $productRecordQuery->andWhere('p.name IS NULL');
            } else {
                $productRecordQuery->andWhere('p.name = ?', $productData['name']);
            }
            
            if (empty($productData['vendor'])) {
                $productRecordQuery->andWhere('p.vendor IS NULL');
            } else {
                $productRecordQuery->andWhere('p.vendor = ?', $productData['vendor']);
            }

            if (empty($productData['version'])) {
                $productRecordQuery->andWhere('p.version IS NULL');
            } else {
                $productRecordQuery->andWhere('p.version = ?', $productData['version']);
            }
        }

        $productRecord = $productRecordQuery->execute();

        return ($productRecord) ? $productRecord[0][0] : FALSE;
    }
    
    /**
     * Save product and update asset's product
     *
     * @param array $productData The product data to save
     * @return void
     */
    private function _saveProduct($productData)
    {
        $product = new Product();
        $product->merge($productData);
        $product->save();

        $id = $product->id;

        $product->free();
        unset($product);

        // Check to see if any of the pending products are duplicates, if so, update the finding to point to the
        // correct product id
        foreach ($this->_findings as &$findingData) {
            if (empty($findingData['asset']['productId']) && $findingData['product'] == $productData) {
                $findingData['asset']['productId'] = $id;
            }
        }

        return $id;
    }
}
