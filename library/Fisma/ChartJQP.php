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
class Fisma_ChartJQP
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
        
        if (empty($chartData['width']) || empty($chartData['height'])) {
            throw new Fisma_Zend_Exception(
                "Chart width and height must both be defined in array passed to __construct in class Fisma_ChartJQP"
            );
        }
        
        $this->chartData = $chartData;
    }
    
    /**
     * Render the chart HTML
     * 
     * @return string
     */
    public function __toString()
    {
        $view = Zend_Layout::getMvcInstance()->getView();
        
        $dataToView = array();
        $dataToView['chartData'] = $this->chartData;
        
        return $view->partial('chart/chartJQP.phtml', 'default', $dataToView);
    }
}
