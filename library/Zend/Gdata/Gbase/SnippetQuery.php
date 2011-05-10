<?php

/**
 * Zend Framework
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://framework.zend.com/license/new-bsd
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@zend.com so we can send you a copy immediately.
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gbase
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id$
 */

/**
 * Zend_Gdata_Query
 */
// require_once('Zend/Gdata/Query.php');

/**
 * Zend_Gdata_Gbase_Query
 */
// require_once('Zend/Gdata/Gbase/Query.php');

/**
 * Assists in constructing queries for Google Base Snippets Feed
 *
 * @link http://code.google.com/apis/base/
 *
 * @category   Zend
 * @package    Zend_Gdata
 * @subpackage Gbase
 * @copyright  Copyright (c) 2005-2011 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 */
class Zend_Gdata_Gbase_SnippetQuery extends Zend_Gdata_Gbase_Query
{
    /**
     * Path to the snippets feeds on the Google Base server.
     */
    const BASE_SNIPPET_FEED_URI = 'http://www.google.com/base/feeds/snippets';

    /**
     * The default URI for POST methods
     *
     * @var string
     */
    protected $_defaultFeedUri = self::BASE_SNIPPET_FEED_URI;

    /**
     * Returns the query URL generated by this query instance.
     *
     * @return string The query URL for this instance.
     */
    public function getQueryUrl()
    {
        $uri = $this->_defaultFeedUri;
        if ($this->getCategory() !== null) {
            $uri .= '/-/' . $this->getCategory();
        }
        $uri .= $this->getQueryString();
        return $uri;
    }

}
