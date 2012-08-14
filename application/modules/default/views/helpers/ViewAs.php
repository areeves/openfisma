<?php
/**
 * Copyright (c) 2012 Endeavor Systems, Inc.
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
 * A view helper which renders View As information, when applicable.
 *
 * @author     Andrew Reeves <andrew.reeves@endeavorsystems.com>
 * @copyright  (c) Endeavor Systems, Inc. 2012 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    View_Helper
 */
class View_Helper_ViewAs extends Zend_View_Helper_Abstract
{
    /**
     * Return the View As information.
     *
     *
     * @return string
     */
    public function viewAs()
    {
        $url = $this->view->url();;
        if ($viewAs = CurrentUser::getInstance()->viewAs()) {
            $href = $this->view->url(array('controller' => 'view-as', 'action' => 'stop'), null, true);
            $href .= '?url=' . urlencode($url);
            return '<div id="view-as">'
                . 'Viewing as ' . $this->view->userInfo($viewAs->displayName, $viewAs->id)
                . '<a href="' . $href . '"> ✗ </a>'
                . '</div>';
        } else {
            return '';
        }
    }
}
