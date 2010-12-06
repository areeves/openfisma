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
	 *     Obj['uniqueid']        The name of the div for jqPlot create canvases inside of. The HTML within this div
                                  will be erased before chart plotting.
	 *     Obj['title']           The title to render above the chart
	 *     Obj['chartType']       String that must be "bar", "stackedbar", "line", or "pie"
	 *     Obj['chartData']	      Array to pass to jqPlot as the data to plot (numbers).
	 *     Obj['chartDataText']	  Array of labels (strings) for each data set in chartData
	 *     Obj['chartLayerText']  Array of labels (strings) for each different line/layer in a milti-line-char or 
                                  stacked-bar-chart
	 *     Obj['colors']		  (optional) Array of colors for the chart to use across layers on stacked-bar-charts
	 *     Obj['links']           (optional) Array of links of which the browser should navigate to when a given data 
           element is clicked on
	 *     Obj['linksdebug']      (optional) Boolean, if set true, an alert box of what was clicked on will pop up 
                                  instead of browser navigation based on Obj['links']
     *
     * @var Array
     */
    public $chartData;
        
    /**
     * Build a jqPlot chart object
     * 
     * @param string $sourceUrl The URL which contains the XML definition/data for this chart
     */
    public function __construct($chartData)
    {
        $this->chartData = $chartData;
    }
    
    /**
     * Render the chart HTML
     * 
     * @return string
     */
    public function __toString()
    {
        $dataToView = array();
        $view = Zend_Layout::getMvcInstance()->getView();
        
        // width and height are required params
        if (empty($this->chartData['width']) || empty($this->chartData['height'])) {
            throw new Fisma_Zend_Exception(
                "Chart width and height must both be defined in chartData array in class Fisma_Chart"
            );
        }
        
        // make up a uniqueid is one was not given
        if (empty($this->chartData['uniqueid'])) {
            $this->chartData['uniqueid'] = 'chart' . uniqid();
        }
        
        // alignment html to apply to the div that will hold the chart canvas
        if (empty($this->chartData['align']) || $this->chartData['align'] == 'center' ) {
            
            $dataToView['divContainerArgs'] =   'style="width: ' . $this->chartData['width'] . 'px;' .
                                                'margin-left: auto; ' .
                                                'margin-right: auto; display:none;"';
        } elseif ($this->chartData['align'] == 'left' || $this->chartData['align'] == 'right' ) {
            
            $dataToView['divContainerArgs'] =   'class="' . $this->chartData['align'] . '; display:none;"';
            
        }
        unset($this->chartData['align']);
        
        // send the chart data to the view script as well
        $dataToView['chartData'] = $this->chartData;
        
        return $view->partial('chart/chart.phtml', 'default', $dataToView);
    }
}
