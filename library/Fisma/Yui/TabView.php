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
 * A PHP wrapper for the YUI TabView class
 *
 * This class adds some functionality to the basic tab view, such as using cookies to keep the selected tab across
 * page loads.
 *
 * @author     Mark E. Haase
 * @copyright  (c) Endeavor Systems, Inc. 2010 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Fisma
 * @subpackage Fisma_Yui
 */
class Fisma_Yui_TabView
{
    /**
     * Unique string identifier for this tab view, used to create unique cookie values
     *
     * @var string
     */
    private $_id;

    /**
     * Stores the ID of the object being displayed in the tab view
     *
     * @var int
     */
    private $_objectId;

    /**
     * Stores the ID of the object being displayed in the tab view
     *
     * @var int
     */
    private $_orientation;

    /**
     * The position of the tab bar (top / bototm / left / right)
     *
     * Each tab is defined as an associative array with indices 'name' and 'url'
     *
     * @var array
     */
    private $_tabs;

    /**
     * Construct a tab view instance with a unique ID
     *
     * Each tab view object needs an ID that is unique across the entire application. This enables us to render multiple
     * tab views per page, create cookies for each tab view, etc.
     *
     * IT IS RECOMMENDED THAT YOU NAME THE TAB VIEW AFTER THE CONTROLLER AND ACTION THAT IT SUPPORTS. For example, the
     * FindingController::viewAction has a tab view called "FindingView". This ensures that your tab view won't conflict
     * with another tab view defined somewhere else in the application.
     *
     * If your view needs multiple tab views, then you need to append a unique suffix to each tab view. E.g.
     * "FindingView1", "FindingView2", etc.
     *
     * When viewing a tab view on subsequent requests, a cookie is set to remember which tab was being displayed. The
     * objectId is used to reset the tabview to the first tab when you load the same tab view with a different object.
     * For example, if you view Finding #1, then click tab 2, then click refresh, the page will reload and select tab 2.
     * However, if you then switch to Finding #2, the first tab will be selected again.
     *
     * If you are working on a tab view which does not represent an object (such as the application configuration view)
     * then pass null for the objectId (or omit it entirely).
     *
     * @param string $tabViewId This ID must be unique application-wide, otherwise session cookies will conflict
     * @param int $objectId This is the ID of the object which is being displayed, or NULL.
     * @param string $orientation Use "top" (default) or "bottom" for horizontal tabbar, "left" or "right" for vertical
     */
    public function __construct($tabViewId, $objectId = null, $orientation = 'top')
    {
        if (empty($tabViewId)) {
            throw new Fisma_Zend_Exception('TabView ID must be set to non-empty value');
        }

        $this->_id = $tabViewId;

        /*
         * If the object ID is null, then we need to set it to a constant value. 0 is arbitrarily chosen for this
         * purpose.
         */
        if (is_null($objectId)) {
            $this->_objectId = 0;
        } else {
            $this->_objectId = $objectId;
        }

        $this->_orientation = $orientation;
        $this->_tabs = array();
    }

    public function getTabViewId()
    {
        return $this->_id;
    }

    public function getObjectId()
    {
        return $this->_objectId;
    }

    public function getOrientation()
    {
        return $this->_orientation;
    }

    /**
     * Add a tab to this tab view
     *
     * @param string $name The name displayed on the tab
     * @param string $url The URL which supplies the HTML when this tab is selected
     * @param string $id The id to assign to the tab
     * @param string $active Whether or not the tab is active
     */
    public function addTab($name, $url, $id = NULL, $active = 'false')
    {
        $id = (empty($id)) ? $name : $id;
        $this->_tabs[] = array('id' => $id, 'name' => $name, 'url' => $url, 'active' => $active);
    }

    const LAYOUT_NOT_INSTANTIATED_ERROR = 'Layout has not been instantiated.';
    /**
     * Render the tabview to HTML
     *
     * Cookie names are generated by prepending a prefix 'TabView' to the tab views unique ID, and appending a
     * meaningful name. E.g. the "FindingView" tab view generates cookies like "TabView_FindingView_SelectedTab", etc.
     *
     * @return string
     * @throws Fisma_Zend_Exception
     */
    public function render($layout = null)
    {
        if (!isset($layout)) {
            $layout = Zend_Layout::getMvcInstance();
        }
        if ($layout==null) {
            throw new Fisma_Zend_Exception(self::LAYOUT_NOT_INSTANTIATED_ERROR);
        }
        $view = $layout->getView();

        $tabs = array(
            'selectedTabCookie' => 'TabView_' . $this->_id . '_SelectedTab',
            'objectId' => $this->_objectId,
            'objectIdCookie' => 'TabView_' . $this->_id . '_ObjectId',
            'tabs' => $this->_tabs,
            'tabViewContainer' => 'TabView_' . $this->_id . '_TabViewContainer',
            'tabViewOrientation' => $this->_orientation
        );

        return $view->partial('yui/tab-view.phtml', 'default', $tabs);
    }

    //use render() to do what __tostring() was supposed to do because __tostring() cannot accept parameter
    public function __tostring()
    {
        return $this->render();
    }
}
