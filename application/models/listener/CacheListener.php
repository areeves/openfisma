<?php
/**
 * Copyright (c) 2009 Endeavor Systems, Inc.
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
 * @author    Josh Boyd <joshua.boyd@endeavorsystems.com> 
 * @copyright (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license   http://openfisma.org/content/license
 * @version   $Id: $
 * @package   Listener
 */
 
/**
 * A listener for handling the results cache. If the results cache is not enabled,
 * this listener does nothing thanks to Doctrine properly handling the non-exitence
 * of a results cache.
 *
 * @copyright (c) Endeavor Systems, Inc. 2009 (http://www.endeavorsystems.com)
 * @license   http://www.openfisma.org/license.php
 * @package   Listener
 */
class CacheListener extends Doctrine_Record_Listener
{
    /**
     * Enables result cache usage for every DqlSelect issued.
     *
     * @param Doctrine_Event $event
     */
    public function preDqlSelect(Doctrine_Event $event)
    {
        $q = $event->getQuery();
        $q->useResultCache(true);
    }

    /**
     * Expires the results cache entry for this query on every
     * DqlUpdate. This way there is no stale data in the cache, ever.
     *
     * @param Doctrine_Event $event
     */
    public function preDqlUpdate(Doctrine_Event $event)
    {
        $q = $event->getQuery();
        $q->expireResultCache(true);
    }

    /**
     * Expires the results cache for this query on every DqlDelete.
     * This way there is no stale data in the cache, ever.
     *
     * @param Doctrine_Event $event
     */
    public function preDqlDelete(Doctrine_Event $event)
    {
        $q = $event->getQuery();
        $q->expireResultCache(true);
    }
}
