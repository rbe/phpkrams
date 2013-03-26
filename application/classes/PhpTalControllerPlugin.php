<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Zend_Controller_Plugin preparing View
 */
class PhpTalControllerPlugin extends Zend_Controller_Plugin_Abstract {

    /**
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request) {
        // Get root directory
        $root = ZendHelper::getRoot();
        //
        $viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper("viewRenderer");
        /*$view = new Zend_View();*/
        // Now we are using PHPTAL View
        $view = new PhpTalZendView();
        // This is done in FflZendControllerBase too: after using _forward
        // in a Controller the path gets lost...?!
        $view->addScriptPath(
            $root
            . "/application/modules/"
            . $request->getModuleName()
            . ZendHelper::getConfig()->phptal->template->dir
        )
        ->doctype("XHTML1_STRICT");
        $viewRenderer->setView($view)
        ->setViewSuffix(ZendHelper::getConfig()->phptal->template->suffix)
        ->init();
        //        // setup Zend_Layout (optional)
        //        Zend_Layout::startMvc(array(
        //                    'layoutPath' => $root . '/application/modules/default/views/layouts',
        //                    'layout' => 'main',
        //                    'viewSuffix' => 'tpl.html'
        //            )
        //        );
        // add this two lines if you wish to use PHPTAL
        $phptal = new PHPTAL;
        $view->setEngine($phptal);
        // PHPTAL options
        $phptal->setPhpCodeDestination($root . ZendHelper::getConfig()->phptal->tmp->dir)
        ->setForceReparse(true)
        // Pre-filter to support traditional Zend_View syntax in templates
        ->setPreFilter(new PhpTalZendFilter());
    }

}
