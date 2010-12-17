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
 * A PHP wrapper for the jqPlot javascript wrapper chart library. 
 * 
 * These charts can asynchronously load json information from an external source or be initialized with the data 
 * in which will define how the chart will be created and what it will plot.
 * 
 * @author     Dale Frey
 * @copyright  (c) Endeavor Systems, Inc. 2010 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Fisma
 * @subpackage Fisma_Chart
 * @version    $Id$
 */
class Fisma_Chart
{
    /**
     * An array that holds information defining how the chart will be constructed and what it will plot
     * The array fields are as follows:
	 *     Obj['chartData']	      Array to pass to jqPlot as the data to plot (numbers).
	 *     Obj['chartDataText']	  Array of labels (strings) for each data set in chartData
	 *     Obj['chartLayerText']  Array of labels (strings) for each different line/layer in a milti-line-char or 
                                  stacked-bar-chart
	 *     Obj['links']           (optional) Array of links of which the browser should navigate to when a given data 
           element is clicked on
	 *     Obj['linksdebug']      (optional) Boolean, if set true, an alert box of what was clicked on will pop up 
                                  instead of browser navigation based on Obj['links']
     *
     * @var Array
     */
    public $chartParamArr;
    
    /**
     * Initalizes an internal array which will be passed down to the JavaScript jqPlot-wrapper
     * 
     * @param width - width in pixels to set the chart
     * @param height - height in pixels to set the chart
     * @param externalDataURL - (optional) external data source for the JavaScript to request a Fisma_Chart->export()
     */
    public function __construct($width = null, $height = null, $chartUniqueId = null, $externalDataUrl = null)
    {
        $this->chartParamArr['chartData'] = array();
        $this->chartParamArr['chartDataText'] = array();
        $this->chartParamArr['widgets'] = array();
        
        if (!empty($width)) {
            $this->setWidth($width);
        }
        
        if (!empty($height)) {
            $this->setHeight($height);
        }
        
        if (!empty($chartUniqueId)) {
            $this->setUniqueid($chartUniqueId);
        }
        
        if (!empty($externalDataUrl)) {
            $this->setExternalSource($externalDataUrl);
        }
        
        return $this;
    }
    
    /**
     * The chart title to show above the chart
     * 
     * @return Fisma_Chart
     */
    public function setTitle($inString)
    {
        $this->chartParamArr['title'] = $inString;
        return $this;
    }
    
    /**
     * The chart type (bar, pie, or stackedbar)
     * 
     * @return Fisma_Chart
     */
    public function setChartType($inString)
    {
        $this->chartParamArr['chartType'] = $inString;
        return $this;
    }
    
    /**
     * The chart width in pixels
     * 
     * @return Fisma_Chart
     */
    public function setWidth($inInteger)
    {
        $this->chartParamArr['width'] = $inInteger;
        return $this;
    }
    
    /**
     * The chart height in pixels
     * 
     * @return Fisma_Chart
     */
    public function setHeight($inInteger)
    {
        $this->chartParamArr['height'] = $inInteger;
        return $this;
    }
    
    /**
     * The uniqueid for this chart, name of the div in which holds the canvases
     * 
     * @return Fisma_Chart
     */
    public function setUniqueid($inString)
    {
        $this->chartParamArr['uniqueid'] = $inString;
        return $this;
    }
    
    /**
     * An array of CSS colors to use for each bar (in a bar chart), or each layer of bars (on a stacked bar-chart).
     * 
     * @return Fisma_Chart
     */
    public function setColors($inStrArray)
    {
        $this->chartParamArr['colors'] = $inStrArray;
        return $this;
    }
    
    public function setConcatXLabel($inBoolean)
    {
        $this->chartParamArr['concatXLabel'] = $inBoolean;
        return $this;
    }
    
    public function setExternalSource($inString)
    {
        $this->chartParamArr['externalSource'] = $inString;
        return $this;
    }
    
    public function setAlign($inString)
    {
        $this->chartParamArr['align'] = $inString;
        return $this;
    }
    
    /**
     * Adds a column onto the chart to render. The input params may either be a value (bar/pie chart), or an array of
     * values (stacked-bar/stacked-line chart).
     * 
     * @return Fisma_Chart
     */
    public function addColumn($columnLabel, $addValue, $addLink)
    {
        $this->chartParamArr['chartData'][] = $addValue;
        $this->chartParamArr['chartDataText'][] = $columnLabel;
        $this->chartParamArr['links'][] = $addLink;
        return $this;
    }
    
    /**
     * Overrides, erases, and sets the data array (numbers to plot) to the array given
     * 
     * @return Fisma_Chart
     */
    public function setData($inArray)
    {
        $this->chartParamArr['chartData'] = $inArray;
        return $this;
    }
    
    /**
     * Overrides, erases, and sets the link array (or string) for chart elements to link to
     * 
     * @return Fisma_Chart
     */
    public function setLinks($inArray)
    {
        $this->chartParamArr['links'] = $inArray;
        return $this;
    }
    
    /**
     * Overrides, erases, and sets the labels to use on the x-axis
     * 
     * @return Fisma_Chart
     */
    public function setAxisLabelsX($inArray)
    {
        $this->chartParamArr['chartDataText'] = $inArray;
        return $this;
    }
    
    /**
     * Overrides, erases, and sets the labels to use on for the different layers of bars on a stacked bar/line chart
     * 
     * @return Fisma_Chart
     */
    public function setLayerLabels($inArray)
    {
        $this->chartParamArr['chartLayerText'] = $inArray;
        return $this;
    }
    
    /**
     * Adds a widget/option-field onto the chart
     * 
     * @return Fisma_Chart
     */
    public function addWidget($uniqueid = null, $label = null, $type = 'text', $defaultvalue = null)
    {
        if ($type !== 'text' && $type !== 'combo') {
            throw new Fisma_Zend_Exception(
                "Unknown widget type in Fisma_Chart->addWidget(). Type must be either 'text' or 'combo'"
            );
        }
        
        $wigData = array('type' => $type);
        
        if (!empty($uniqueid)) {
            $wigData['uniqueid'] = $uniqueid;
        }
        
        if (!empty($label)) {
            $wigData['label'] = $label;
        }
        
        if (!empty($defaultvalue)) {
            $wigData['defaultvalue'] = $defaultvalue;
        }
        
        $this->chartParamArr['widgets'][] = $wigData;
        
        return $this;
    }

    /**
     * Render the chart to HTML
     * 
     * @return string
     */
    public function export($expMode = 'html')
    {
        switch ($expMode)
        {
            case 'array':
                
                return $this->chartParamArr;
                
            case 'html':
                
                $dataToView = array();
                $view = Zend_Layout::getMvcInstance()->getView();
                
                // make up a uniqueid is one was not given
                if (empty($this->chartParamArr['uniqueid'])) {
                    $this->chartParamArr['uniqueid'] = 'chart' . uniqid();
                }
                
                // alignment html to apply to the div that will hold the chart canvas
                if (empty($this->chartParamArr['align']) || $this->chartParamArr['align'] == 'center' ) {
                    
                    $dataToView['divContainerArgs'] =   'style="margin-left: auto; margin-right: auto; display:none;"';
                    
                } elseif ($this->chartParamArr['align'] == 'left' || $this->chartParamArr['align'] == 'right' ) {
                    
                    $dataToView['divContainerArgs'] =   'class="' . $this->chartParamArr['align'] . '; display:none;"';
                    
                }
                unset($this->chartParamArr['align']);
                
                // send the chart data to the view script as well
                $dataToView['chartParamArr'] = $this->chartParamArr;
                $dataToView['chartId'] = $this->chartParamArr['uniqueid'];
                
                return $view->partial('chart/chart.phtml', 'default', $dataToView);
                
            default:
                
                throw new Fisma_Zend_Exception(
                    "Unknown export-mode (expMode) given to Fisma_Chart->export(). Given mode was: " . $expMode
                );
        }
    }
}
