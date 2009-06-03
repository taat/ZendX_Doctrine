<?php

/**
 * Class ZendX_Doctrine_Paginator
 * Extension of Zend_Paginator to use with Doctrine_Paginator_Adapter
 *
 * @author TAAT
 */
class ZendX_Doctrine_Paginator extends Zend_Paginator
{
    /**
     * Constructor
     *
     * @param Doctrine_Query $query Query to paginate
     */
    function __construct(Doctrine_Query $query) {
        $this->adapter = new ZendX_Doctrine_Paginator_Adapter_Doctrine($query);
        parent::__construct($this->adapter);
    }

    /**
     * Sets the total row count for adapter, either directly or through a supplied query
     * Use this method if you already know how many rows query returns
     * This will save uncecsesary db queries
     *
     * See ZendX_Doctrine_Paginator_Adapter_Doctrine::setRowCount()
     * @param  Doctrine_Query|integer $totalRowCount Total row count integer
     *                                               or query
     * @return ZendX_Doctrine_Paginator $this
     * @throws ZendX_Doctrine_Paginator_Exception
     */
    public function setRowCount($rowCount)
    {
        $this->adapter->setRowCount($rowCount);

        return $this;
    }

    /**
     * Get items to render on current page
     *
     * @access public
     * @param  $string
     * @return null
     */
    public function getItemsByPage($page = null)
    {
        if (NULL === $page) {
            $page = $this->getCurrentPageNumber();
        }

        return parent::getItemsByPage($page);
    }

    /**
     * Set default scrolling style
     * This is non static version of {@link setDefaultScrollingStyle()}
     *
     * @see setDefaultScrollingStyle()
     * @access public
     * @param string $style Scrolling style (All|Elastic|Jumping|Sliding)
     * @return ZendX_Doctrine_Paginator
     */
    public function setScrollingStyle($style)
    {
        self::$_defaultScrollingStyle = $style;

        return $this;
    }

    /**
     * Set default pagination partial script
     * @access public
     * @param string $filename Partial view script filename
     * @return ZendX_Doctrine_Pagination $this
     */
    public function setViewPartial($filename)
    {
         Zend_View_Helper_PaginationControl::setDefaultViewPartial($filename);

         return $this;
    }

    /**
     * Alias for {@link setViewPartial()}
     *
     * @see setViewPartial()
     */
    public function setDefaultViewPartial($filename)
    {
        return $this->setViewPartial($filename);
    }


}