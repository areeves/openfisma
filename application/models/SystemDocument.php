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
 * <http://www.gnu.org/licenses/>.
 */

/**
 * SystemDocument
 * 
 * @author     Ryan Yang <ryan@users.sourceforge.net>
 * @copyright  (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license    http://www.openfisma.org/content/license
 * @package    Model
 * @version    $Id$
 */
class SystemDocument extends BaseSystemDocument
{
    /**
     * Return the physical path to this document
     * 
     * @return string
     */
    public function getPath() 
    {
        $path = Fisma::getPath('systemDocument')
              . '/'
              . $this->System->Organization->id
              . '/'
              . $this->fileName;
              
        return $path;
    }
    
    public function getSizeKb()
    {
        return round($this->size / 1024, 0) . " KB";
    }
    
    /**
     * Returns a URL for an icon which represents this document
     * 
     * @return string
     */
    public function getIconUrl()
    {
        $pi = pathinfo($this->fileName);
        $extension = strtolower($pi['extension']);
        $imagePath = Fisma::getPath('image');
        if (file_exists("$imagePath/mimetypes/$extension.png")) {
            return "/images/mimetypes/$extension.png";
        } else {
            return "/images/mimetypes/unknown.png";
        }
    }
}
