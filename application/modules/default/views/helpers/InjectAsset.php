<?php
/**
 * Copyright (c) 2009 Endeavor Systems, Inc.
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
 * @author    Josh Boyd <joshua.boyd@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license   http://openfisma.org/content/license
 * @version   $Id$
 * @package   View_Helper
 */

/**
 * Helper for injecting assets into layouts
 *
 * @copyright (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license http://openfisma.org/content/license
 * @package View_Helper
 * @var Zend_View_Interface $view
 * @var array $_depMap An array of dependencies for Fisma specific assets
 */
class View_Helper_InjectAsset
{
    public $view;
    private static $_depMap = array(
                                '/javascripts/combined.js' => 
                                array('/javascripts/php.js',
                                      '/javascripts/tiny_mce_config.js',
                                      '/javascripts/fisma.js',
                                      '/javascripts/editable.js',
                                      '/javascripts/selectallselectnone.js',
                                      '/javascripts/Fisma/AttachArtifacts.js',
                                      '/javascripts/Fisma/AutoComplete.js',
                                      '/javascripts/Fisma/Blinker.js',
                                      '/javascripts/Fisma/Calendar.js',
                                      '/javascripts/Fisma/Chart.js',
                                      '/javascripts/Fisma/CheckboxTree.js',
                                      '/javascripts/Fisma/Commentable.js',
                                      '/javascripts/Fisma/Finding.js',
                                      '/javascripts/Fisma/Email.js',
                                      '/javascripts/Fisma/FindingSummary.js',
                                      '/javascripts/Fisma/Highlighter.js',
                                      '/javascripts/Fisma/HtmlPanel.js',
                                      '/javascripts/Fisma/Incident.js',
                                      '/javascripts/Fisma/Ldap.js',
                                      '/javascripts/Fisma/Module.js',
                                      '/javascripts/Fisma/Remediation.js',
                                      '/javascripts/Fisma/Search.js',
                                      '/javascripts/Fisma/Search/Criteria.js',
                                      '/javascripts/Fisma/Search/CriteriaDefinition.js',
                                      '/javascripts/Fisma/Search/CriteriaQuery.js',
                                      '/javascripts/Fisma/Search/CriteriaRenderer.js',
                                      '/javascripts/Fisma/Search/Panel.js',
                                      '/javascripts/Fisma/Spinner.js',
                                      '/javascripts/Fisma/System.js',
                                      '/javascripts/Fisma/SwitchButton.js',
                                      '/javascripts/Fisma/TableFormat.js',
                                      '/javascripts/Fisma/TabView.js',
                                      '/javascripts/Fisma/TabView/Roles.js',
                                      '/javascripts/Fisma/UrlPanel.js',
                                      '/javascripts/Fisma/User.js',
                                      '/javascripts/Fisma/Util.js',
                                      '/javascripts/Fisma/Vulnerability.js',
                                      '/javascripts/AC_RunActiveContent.js',
                                      '/javascripts/jquery-min.js',
                                      '/javascripts/jquery142min.js',
                                      '/javascripts/jquery-ui-181custom_min.js',
                                      '/javascripts/jquery_jqplot.js',
                                      '/javascripts/jqplot_canvasTextRenderer_min.js',
                                      '/javascripts/jqplot_canvasAxisLabelRenderer_min.js',
                                      '/javascripts/jqplot_canvasAxisTickRenderer_min.js',
                                      '/javascripts/jqplot_categoryAxisRenderer.js',
                                      '/javascripts/jqplot_barRenderer.js',
                                      '/javascripts/jqplot_pointLabels.js',
                                      '/javascripts/jqplot_pieRenderer.js',
                                      '/javascripts/jqplotWrapper.js'
                                 ),
                                '/stylesheets/combined.css' =>
                                array('/stylesheets/main.css',
                                      '/stylesheets/AutoComplete.css',
                                      '/stylesheets/AttachArtifacts.css',
                                      '/stylesheets/Dashboard.css',
                                      '/stylesheets/Finding.css',
                                      '/stylesheets/FindingSummary.css',
                                      '/stylesheets/Incident.css',
                                      '/stylesheets/Modules.css',
                                      '/stylesheets/Search.css',
                                      '/stylesheets/SwitchButton.css',
                                      '/stylesheets/Toolbar.css',
                                      '/stylesheets/User.css',
                                      '/stylesheets/jquery_jqplot.css'
                                )
                            );

    /**
     * setView - Gives access to the current Zend_View object
     *
     * @param Zend_View_Interface $view
     * @access public
     * @return void
     */
    public function setView(Zend_View_Interface $view)
    {
        $this->view = $view;
    }

    /**
     * injectAsset - Manages the injection of CSS or JS assets into a layout or view. Takes into account
     * rollups/combined files, and the currently running debug level.
     *
     * @param string $asset Path to the asset
     * @param string $type Type of asset
     * @param boolean $combo Whether the asset is a combo package or not
     * @param string $media Media settings for CSS files
     * @param string $conditional Conditional settings for CSS/JS files
     * @access public
     * @return void
     */
    public function injectAsset($asset, $type, $combo = FALSE,
        $media = 'screen', $conditional = FALSE) 
    {
        /**
         * This asset is a Combo, and the application is in debug mode, so we need to output
         * each of the individual pieces of the combo.
         */
        if ($combo && Fisma::debug()) {
            $assets = self::$_depMap[$asset];
        } else {
            /**
             * This is just a single asset, throw it into an array for easier processing
             */
            $assets = array($asset);
        }

        /**
         * If we're not in debug mode, then insert the application version and -min into
         * the path.
         */
        if (!Fisma::debug()) {
            $appVersion = Fisma::configuration()->getConfig('app_version');

            foreach ($assets as &$asset) {
                $asset = str_replace(
                    ".$type", "-min." . $appVersion . ".$type", $asset
                );
            }
        }

        switch ($type) {
            case 'js':
                foreach ($assets as $asset) {
                    $this->view->headScript()->appendFile(
                        $asset, 'text/javascript', array('conditional' => $conditional)
                    );
                }

                break;

            case 'css':
                foreach ($assets as $asset) {
                    $this->view->headLink()->appendStylesheet($asset, $media, $conditional);
                }

                break;

            default:
                break;
        }
    }
}
