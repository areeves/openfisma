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
 * A generic response object for asynchronous requests that can signal success or failure
 * 
 * The typical use case is to create an object of this type, serialize it to JSON, and return it to the browser.
 * 
 * The only purpose of this class is to standardize the way PHP talks to JS during an asynchronous request. Otherwise
 * each developer will invent their own crazy-undocumented response object and debugging will be a nightmare.
 * 
 * This file corresponds to AsyncResponse.js
 * 
 * @author     Mark E. Haase
 * @copyright  (c) Endeavor Systems, Inc. 2009 {@link http://www.endeavorsystems.com}
 * @license    http://www.openfisma.org/content/license GPLv3
 * @package    Fisma
 * @subpackage Fisma_AsyncResponse
 * @version    $Id$
 */
class Fisma_AsyncResponse
{
    /**
     * A boolean which indicates general success or failure
     * 
     * It's access is public so that it can be serialized to JSON in a sensible way
     * 
     * Default to true, since this is a convenient standard for error handling
     */
    public $success = true;
    
    /**
     * A message which is suitable for display to the end user
     * 
     * It's access is public so that it can be serialized to JSON in a sensible way
     */
    public $message;
        
    /**
     * Mark response as succeeded
     * 
     * @param string $message Optional message which is safe to display to the end user
     */
    public function succeed($message = null)
    {
        $this->success = true;
        
        $this->message = empty($message) ? null : $message;
    }
    
    /**
     * Mark response as failed
     * 
     * @param string $message Optional message which is safe to display to the end user
     */
    public function fail($message = null)
    {
        $this->success = false;
        
        $this->message = empty($message) ? null : $message;
    }
}
