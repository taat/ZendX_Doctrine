<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        // index actions
    }


    /**
     * @todo Pagination cache
     */
    public function paginatorAction()
    {
    }

    public function paginatorslidingAction()
    {
        $page = $this->_getParam('page');
        $q = Doctrine_Query::create()->from('Test t');

        $paginator = new ZendX_Doctrine_Paginator($q);
        $paginator->setItemCountPerPage(3)
        ->setPageRange(10)
        ->setCurrentPageNumber($page)
        ->setScrollingStyle('Sliding')
        ->setViewPartial('pagination.phtml');

        // or set global defaults for whole site:
        // Zend_Paginator::setDefaultScrollingStyle('Jumping');
        // Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        $this->view->paginator = $paginator;

    }

    public function paginatorjumpingAction()
    {
        $page = $this->_getParam('page');
        $q = Doctrine_Query::create()->from('Test t');

        $paginator = new ZendX_Doctrine_Paginator($q);
        $paginator->setItemCountPerPage(3)
        ->setPageRange(10)
        ->setCurrentPageNumber($page)
        ->setScrollingStyle('Jumping')
        ->setViewPartial('pagination.phtml');

        // or set global defaults for whole site:
        // Zend_Paginator::setDefaultScrollingStyle('Jumping');
        // Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        $this->view->paginator = $paginator;

    }

    public function paginatorelasticAction()
    {
        $page = $this->_getParam('page');
        $q = Doctrine_Query::create()->from('Test t');

        $paginator = new ZendX_Doctrine_Paginator($q);
        $paginator->setItemCountPerPage(3)
        ->setPageRange(10)
        ->setCurrentPageNumber($page)
        ->setScrollingStyle('Elastic')
        ->setViewPartial('pagination.phtml');

        // or set global defaults for whole site:
        // Zend_Paginator::setDefaultScrollingStyle('Jumping');
        // Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        $this->view->paginator = $paginator;

    }

    public function paginatorallAction()
    {
        $page = $this->_getParam('page');
        $q = Doctrine_Query::create()->from('Test t');

        $paginator = new ZendX_Doctrine_Paginator($q);
        $paginator->setItemCountPerPage(3)
        ->setPageRange(10)
        ->setCurrentPageNumber($page)
        ->setScrollingStyle('All')
        ->setViewPartial('pagination.phtml');

        // or set global defaults for whole site:
        // Zend_Paginator::setDefaultScrollingStyle('Jumping');
        // Zend_View_Helper_PaginationControl::setDefaultViewPartial('pagination.phtml');

        $this->view->paginator = $paginator;

    }



}

