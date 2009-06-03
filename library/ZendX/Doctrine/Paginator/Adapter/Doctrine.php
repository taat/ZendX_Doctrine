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
 * @package    ZendX_Doctrine
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @version    $Id: $
 */

/**
 * @category   Zend
 * @package    ZendX_Doctrine
 * @see        Zend_Paginator_Adapter_Interface
 * @copyright  Copyright (c) 2005-2009 Zend Technologies USA Inc. (http://www.zend.com)
 * @license    http://framework.zend.com/license/new-bsd     New BSD License
 * @author     Jason Eisenmenger
 * @author     Tomek Pęszor
 * @see http://framework.zend.com/wiki/display/ZFPROP/Zend_Paginator_Adapter_Doctrine+-+Jason+Eisenmenger
 */
class ZendX_Doctrine_Paginator_Adapter_Doctrine implements Zend_Paginator_Adapter_Interface
{

    /**
     * Name of the row count column
     *
     * @var string
     */
    const ROW_COUNT_COLUMN = 'zend_paginator_row_count';

    /**
     * Database query
     *
     * @var Doctrine_Query
     */
    protected $_query = null;

    /**
     * Total item count
     *
     * @var integer
     */
    protected $_rowCount = null;

    /**
     * Constructor.
     *
     * @param Doctrine_Query $query The select query
     */
    public function __construct(Doctrine_Query $query)
    {
        $this->_query = $query;
    }

    /**
     * Sets the total row count, either directly or through a supplied query
     *
     * @param  Doctrine_Query|integer $totalRowCount Total row count integer
     *                                               or query
     * @return Zend_Paginator_Adapter_Doctrine $this
     * @throws ZendX_Doctrine_Paginator_Exception If unable to determine number of rows.
     */
    public function setRowCount($rowCount)
    {
        if ($rowCount instanceof Doctrine_Query) {
            $sql = $rowCount->getSql();

            if (false === strpos($sql, self::ROW_COUNT_COLUMN)) {
                throw new ZendX_Doctrine_Paginator_Exception('Row count column \''.self::ROW_COUNT_COLUMN.'\' not found.');
            }

            $result = $rowCount->fetchOne()->toArray();

            $this->_rowCount = count($result) > 0 ? $result[self::ROW_COUNT_COLUMN] : 0;
        } else if (is_integer($rowCount)) {
            $this->_rowCount = $rowCount;
        } else {
            throw new ZendX_Doctrine_Paginator_Exception('Invalid row count \'' . $rowCount . '\'.');
        }

        return $this;
    }

    /**
     * Returns an array of items for a page.
     *
     * @param  integer $offset Page offset
     * @param  integer $itemCountPerPage Number of items per page
     * @return array
     */
    public function getItems($offset, $itemCountPerPage)
    {
        $this->_query->limit($itemCountPerPage)->offset($offset);

        return $this->_query->execute();
    }

    /**
     * Returns the total number of rows in the result set.
     *
     * @return integer
     */
    public function count()
    {
        if ($this->_rowCount === null) {
            $rowCount = $this->_query->count();
            $this->setRowCount($rowCount);
        }

        return $this->_rowCount;
    }
}
