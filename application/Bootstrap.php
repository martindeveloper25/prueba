<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function _initConfig()
    {
        $config = new Zend_Config($this->getOptions(), true);
        Zend_Registry::set('config', $config);
    }
    
    public function _initViewHelpers()
    {
        $doctypeHelper = new Zend_View_Helper_Doctype();
        $doctypeHelper->doctype(Zend_View_Helper_Doctype::HTML5);
        $this->bootstrap('layout');
        $layout = $this->getResource('layout');
        $view = $layout->getView();
        $config = Zend_Registry::get('config');
        $view->headMeta()->appendHttpEquiv('Content-Type', 'text/html; charset=utf-8');
        $view->headMeta()->appendName("robots", "noindex, nofollow x");

        $js = sprintf(
            "var urls = {
                siteUrl : '%s',
        
            }", 
            $config->app->siteUrl
          
        );
        
        $view->headScript()->appendScript($js);
       
        define('SITE_URL', $config->app->siteUrl);
        define('TITLE', $config->app->title);
        
    }
    
    protected function __initSession() {
        Zend_Session::start();
    }
    
    protected function _initDbResource() {
        
        $this->_executeResource('db');
        $adapter = $this->getResource('db');
        Zend_Registry::set('db', $adapter);
        
    }
    
    protected function _initRoutes()
    {
        $routeConfig = new Zend_Config_Ini(APPLICATION_PATH . '/configs/routes.ini');
        
        $router = new Zend_Controller_Router_Rewrite();
        $router->addConfig($routeConfig);
        
        $this->getResource('frontController')->setRouter($router);
    }

}

