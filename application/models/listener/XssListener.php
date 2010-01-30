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
 *
 * @author    Mark E. Haase <mhaase@endeavorsystems.com>
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/mw/index.php?title=License
 * @version   $Id$
 * @package   Listener
 */
 
/**
 * A special listener which performs sanitization on user-provided inputs to protect against XSS attacks.
 * 
 * This listener works by introspecting the model and looking for an extra property named "purify", which can have
 * the values "html" or "plaintext". In HTML mode, this listener invokes the HtmlPurifier library to clean up
 * invalid and/or malicious markup while preserving valid user-generated markup. In plain text mode, the listener
 * uses htmlspecialchars() to escape any characters which may interrupt normal rendering of plain text.
 *
 * This listener should be attached to any class which puts external data (e.g., user-provided) 
 * 
 * @copyright (c) Endeavor Systems, Inc. 2008 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/license.php
 * @package   Listener
 */
class XssListener extends Doctrine_Record_Listener 
{
    /**
     * The HTMLPurifier instance used by the listener
     * 
     * @var HTMLPurifier
     */
    private static $_purifier;
    
    /**
     * Purify any fields which have been marked in the schema as needing purification
     * 
     * @param Doctrine_Event $event
     */
    public function preSave(Doctrine_Event $event) 
    {
        $invoker = $event->getInvoker();
        $modified = $invoker->getModified();
        $table = $invoker->getTable();
        
        // Step through each modified value, and see if it needs to have any purification applied
        foreach ($modified as $column => $value) {
            $columnDefinition = $table->getColumnDefinition($column);
            if (isset($columnDefinition['extra'])
                && isset ($columnDefinition['extra']['purify'])) {
                $purifyType = $columnDefinition['extra']['purify'];
                switch ($purifyType) {
                    case 'plaintext':
                        $invoker[$column] = htmlspecialchars($value);
                        break;
                    case 'html':
                        $invoker[$column] = $this->getPurifier()->purify($value);
                        break;
                    default:
                        throw new Fisma_Exception("Undefined purification type '$purifyType' on column "
                                                . "'$column' on table '{$table->getTableName()}'");
                }
            }
        }
    }
    
    /**
     * Return the purifier instance for this class, initializing it first if necessary
     *
     * @see http://htmlpurifier.org/live/configdoc/plain.htm
     * 
     * @return HTMLPurifier
     */
    public function getPurifier() 
    {
        if (!isset(self::$_purifier)) {
            require_once('HTMLPurifier/Bootstrap.php');
            $config = HTMLPurifier_Config::createDefault();
            // Whenever the configuration is modified, the definition rev needs to be incremented.
            // This prevents HTML Purifier from using a stale cach definition
            $config->set('Cache', 'DefinitionImpl', null); // remove this later
            $config->set('Core', 'Encoding', 'ASCII'); /** @todo utf8 */
            $config->set('HTML', 'Doctype', 'HTML 4.01 Strict'); /** @todo put the purifier into the registry */
            // Make sure to keep the following line in sync with Tiny MCE so users aren't surprised when their
            // data looks different before storage and after retreival.
            $config->set('HTML', 'Allowed', 'a[href],p[style],b,i,strong,em,span[style],ul,li,ol,table,tr,th,td');
            $config->set('HTML', 'TidyLevel', 'medium'); // Conform user submitted HTML to our doctype
            $config->set('AutoFormat', 'Linkify', true); // Turn text URLS into <a> links
            $config->set('AutoFormat', 'RemoveEmpty', true); // Remove tags which do not contain semantic information
            $config->set('Output', 'CommentScriptContents', false); // Do not add HTML comments for browsers that don't understand scripts
            $config->set('URI', 'AllowedSchemes', array('http','https','mailto')); // Restrict what types of links users can create
            $config->set('URI', 'Munge', '/redirect/redirect/?url=%s'); // Force links to use the OpenFISMA URL redirector
            self::$_purifier = new HTMLPurifier($config);
        } 
        
        return self::$_purifier;
    }
}
