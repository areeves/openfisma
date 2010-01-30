<?php
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
 * along with OpenFISMA.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    Mark E. Haase <mhaaseendeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id: Excel.php 1523 2009-03-26 17:01:44Z mehaase $
 * @package   Fisma_Inject
 */

/**
 * This class injects findings from a system-generated Excel template. It is not a true injection plug-in since it does
 * not subclass Inject_Abstract, but it is placed in the same package because it serves a similar function.
 *
 * This plug-in makes heavy use of the SimpleXML xpath() function, which makes code easier to maintain, but could also
 * be a performance bottleneck for large spreadsheets. Currently there has not been any load-testing for this plugin.
 *
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @package   Fisma_Inject
 */
class Fisma_Inject_Excel
{
    /**
     * The name of the template file which gets sent to the client
     */
    const TEMPLATE_NAME = 'Finding_Upload_Template.xls';

    /**
     * The template version is used to make sure that we don't try to process a template which was produced by a
     * previous version of OpenFISMA. This number should be incremented whenever the template file or processing code
     * is modified.
     */
    const TEMPLATE_VERSION = 1;
                  
    /**
     * Maps numerical indexes corresponding to column numbers in the excel upload template onto those
     * column's logical names. Excel starts indexes at 1 instead of 0.
     *
     * @todo Move this definition and related items into a separate classs... this is too much stuff to put into the
     * controller
     */
    private $_excelTemplateColumns = array(
        1 => 'systemNickname',
        'discoveredDate',
        'network',
        'assetName',
        'assetIp',
        'assetPort',
        'productName',
        'productVendor',
        'productVersion',
        'findingSource',
        'findingDescription',
        'findingRecommendation',
        'findingType',
        'findingMitigationStrategy',
        'ecdDate',
        'securityControl',
        'threatLevel',
        'threatDescription',
        'countermeasuresEffectiveness',
        'countermeasureDescription',
        'contactInfo'
    );

    /**
     * Indicates which columns are required in the excel template. Human readable names are included so that meaningful
     * error messages can be provided for missing columns.
     */
    private $_requiredExcelTemplateColumns = array (
        'systemNickname' => 'System',
        'discoveredDate' => 'Date Discovered',
        'findingSource' => 'Finding Source',
        'findingDescription' => 'Finding Description',
        'findingRecommendation' => 'Finding Recommendation'
    );

    /**
     * The row to start on in the excel template. The template has 3 header rows, so start at the 4th row.
     */
    private $_excelTemplateStartRow = 4;
    
    /**
     * Parses and loads the findings in the specified excel file. Expects XML spreadsheet format from Excel 2007.
     * Compatible with older versions of Excel through the Office Compatibility Pack.
     *
     * @param string $filePath
     * @return int The number of findings processed in the file
     */
    function inject($filePath, $uploadId) {
        // Parse the file using SimpleXML. The finding data is located on the first worksheet.
        $spreadsheet = @simplexml_load_file($filePath);
        if ($spreadsheet === false) {
            throw new Fisma_Exception_InvalidFileFormat(
                "The file is not a valid Excel spreadsheet. Make sure that the file is saved as an XML spreadsheet."
            );
        }

        // Check that the template version matches the version of OpenFISMA which is running.
        $templateVersion = (int)$spreadsheet->CustomDocumentProperties->FismaTemplateVersion;
        if ($templateVersion != self::TEMPLATE_VERSION) {
            throw new Fisma_Exception_InvalidFileFormat(
                "This template was created by a previous version of OpenFISMA and is not compatible with the current"
                . " version. Download a new copy of the template and transfer your data into it."
            );
        }
                
        // Have to do some namespace manipulation to make the spreadsheet searchable by xpath.
        $namespaces = $spreadsheet->getNamespaces(true);
        $spreadsheet->registerXPathNamespace('s', $namespaces['']);
        $findingData = $spreadsheet->xpath('/s:Workbook/s:Worksheet[1]/s:Table/s:Row');
        if ($findingData === false) {
            throw new Fisma_Exception_InvalidFileFormat(
                "The file format is not recognized. Your version of Excel might be incompatible."
            );
        }
        
        // $findingData is an array of rows in the first worksheet. The first three rows on this worksheet contain
        // headers, so skip them.
        array_shift($findingData);
        array_shift($findingData);
        array_shift($findingData);
        
        // Now process each remaining row
        /**
         * @todo Perform these commits in a single transaction.
         */
        $rowNumber = $this->_excelTemplateStartRow;
        foreach ($findingData as $row) {
            // Copy the row data into a local array
            $finding = array();
            $column = 1;
            foreach ($row as $cell) {
                // If Excel skips a cell that has no data, then the next cell that has data will contain an
                // 'ss:Index' attribute to indicate which column it is in.
                $cellAttributes = $cell->attributes('ss', true);
                if (isset($cellAttributes['Index'])) {
                    $column = (int)$cellAttributes['Index'];
                }
                $cellChildren = $cell->children('urn:schemas-microsoft-com:office:spreadsheet');
                $finding[$this->_excelTemplateColumns[$column]] = $cellChildren->Data->asXml();
                $column++;
            }
            
            /**
             * @todo i realized that simplexml can not handle mixed content (an xml text node that also
             * contains xml tags)... so this whole thing needs to be re-written in DOM or some other API
             * that CAN read mixed content. until then -- formatting in excel is not preserved -- all
             * tags are stripped out and remaining special chars are encoded.
             */                
            $finding = array_map('strip_tags', $finding);
            $finding = array_map('htmlspecialchars', $finding);

            // Validate that required row attributes are filled in:
            foreach ($this->_requiredExcelTemplateColumns as $columnName => $columnDescription) {
                if (empty($finding[$columnName])) {
                    throw new Fisma_Exception_InvalidFileFormat("Row $rowNumber: Required column \"$columnDescription\"
                                                          is empty");
                }
            }

            // Map the row data into logical objects. Notice suppression is used heavily here to keep the code
            // from turning into spaghetti. When debugging this code, it will probably be helpful to remove these
            // suppressions.
            $poam = array();
            $poam['uploadId'] = $uploadId;
            $organization = Doctrine::getTable('Organization')->findOneByNickname($finding['systemNickname']);
            if (!$organization) {
                throw new Fisma_Exception_InvalidFileFormat("Row $rowNumber: Invalid system selected. Your template may
                                                      be out of date. Please try downloading it again.");
            }
            $poam['responsibleOrganizationId'] = $organization->id;

            $sourceTable = Doctrine::getTable('Source')->findOneByNickname($finding['findingSource']);
            if (!$sourceTable) {
                throw new Fisma_Exception_InvalidFileFormat("Row $rowNumber: Invalid finding source selected. Your
                                                      template may
                                                      be out of date. Please try downloading it again.");
            }
            $poam['sourceId'] = $sourceTable->id;
            if (!empty($finding['securityControl'])) {
                $securityControlTable = Doctrine::getTable('SecurityControl')->findOneByCode($finding['securityControl']);
                if (!$securityControlTable) {
                    throw new Fisma_Exception_InvalidFileFormat("Row $rowNumber: Invalid finding source selected. Your
                                                          template may
                                                          be out of date. Please try downloading it again.");
                }
                $poam['securityControlId'] = $securityControlTable->id;
            } else {
                $poam['securityControlId'] = null;
            }
            $poam['description'] = $finding['findingDescription'];
            if (!empty($finding['contactInfo'])) {
                $poam['description'] .= "<br>Point of Contact: {$finding['contactInfo']}";
            }
            $poam['recommendation'] = $finding['findingRecommendation'];
            $poam['type'] = $finding['findingType'];
            if (empty($poam['type'])) {
                $poam['type'] = 'NONE';
            } else {
                $poam['status'] = 'DRAFT';
            }
            $poam['mitigationStrategy'] = $finding['findingMitigationStrategy'];
            $poam['expectedCompletionDate'] = $finding['ecdDate'];
            $poam['discoveredDate'] = $finding['discoveredDate'];
            $poam['threatLevel'] = $finding['threatLevel'];
            if (empty($poam['threatLevel'])) {
                $poam['threatLevel'] = 'NONE';
            }
            $poam['threat'] = $finding['threatDescription'];
            $poam['countermeasuresEffectiveness'] = $finding['countermeasuresEffectiveness'];
            if (empty($poam['countermeasuresEffectiveness'])) {
                $poam['countermeasuresEffectiveness'] = 'NONE';
            }
            $poam['countermeasures'] = $finding['countermeasureDescription'];
            $poam['resourcesRequired'] = 'None';

            $asset = array();  
            $networkTable = Doctrine::getTable('Network')->findOneByNickname($finding['network']);
            if (!$networkTable) {
                throw new Fisma_Exception_InvalidFileFormat("Row $rowNumber: Invalid network selected. Your
                                                      template may
                                                      be out of date. Please try downloading it again.");
            }
            $asset['networkId'] = $networkTable->id;
            
            $asset['addressIp'] = $finding['assetIp'];
            $asset['addressPort'] = $finding['assetPort'];
            if (!empty($asset['addressPort']) && !is_numeric($asset['addressPort'])) {
                throw new Fisma_Exception_InvalidFileFormat("Row $rowNumber: The port number is not numeric.");
            }

            $asset['name'] = $finding['assetName'];
            if (empty($asset['name'])) {
                $asset['name'] = "{$asset['addressIp']}:{$asset['addressPort']}";
            }
            $asset['orgSystemId'] = $poam['responsibleOrganizationId'];

            $product = array();
            $product['name'] = $finding['productName'];
            $product['vendor'] = $finding['productVendor'];
            $product['version'] = $finding['productVersion'];
            
            // Now persist these objects. Check assets and products to see whether they exist before creating new
            // ones.
            if (!empty($product['name']) && !empty($product['vendor']) && !empty($product['version'])) {
                /** @todo this isn't a very efficient way to lookup products, but there might be no good alternative */
                $query = Doctrine_Query::create()
                         ->select()
                         ->from('Product p')
                         ->where('p.name = ?', $product['name'])
                         ->andWhere('p.vendor = ?', $product['vendor'])
                         ->andWhere('p.version = ?', $product['version']);
                $productRecord = $query->execute()->toArray();
                if (empty($productRecord)) {
                    $productRecord = new Product();
                    $productRecord->merge($product);
                    $productRecord->save();
                    $productId = $productRecord->id;
                } else {
                    $productId = $productRecord[0]['id'];
                }
            }

            // Persist the asset, if necessary
            if (!empty($asset['networkId']) && !empty($asset['addressIp']) && !empty($asset['addressPort'])) {
                $asset['productId'] = $productId;
                // Verify whether asset exists or not
                $q = Doctrine_Query::create()
                     ->select()
                     ->from('Asset a')
                     ->where('a.networkId = ?', $asset['networkId'])
                     ->andWhere('a.addressIp = ?', $asset['addressIp'])
                     ->andWhere('a.addressPort = ?', $asset['addressPort']);
                $assetRecord = $q->execute()->toArray();
                
                if (empty($assetRecord)) {
                    $assetRecord = new Asset();
                    $assetRecord->merge($asset);
                    $assetRecord->save();
                    $assetId = $assetRecord->id;
                } else {
                    $assetId = $assetRecord[0]['id'];
                }
            }
            // Finally, create the finding
            $poam['assetId'] = $assetId;
            
            $findingRecord = new Finding();
            $findingRecord->merge($poam);
            $findingRecord->save();
            $rowNumber++;
        }
        return $rowNumber - $this->_excelTemplateStartRow;
    }
}

