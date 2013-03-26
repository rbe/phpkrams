<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Description of AnalyzeController
 * @author rbe
 */
class Seo_AnalyzeController extends ZendControllerBase {

    public function init() {
        parent::init();
    }

    public function preDispatch() {
        parent::preDispatch();
    }

    public function postDispatch() {
        parent::postDispatch();
    }

    public function performAction() {
        $this->view->language = NULL;
        $this->view->url = NULL;
        $this->view->kw = NULL;
        $this->view->result = NULL;
        $rq = new ZendRq($this->getRequest());
        if ($rq->Analyze) {
            $sf = new SeoFacade($rq->language, explode(",", $rq->kw), $rq->url);
            $this->view->language = $rq->language;
            $this->view->url = $rq->url;
            $this->view->kw = $rq->kw;
            $this->view->result = $sf->analyze();
        }
    }

}
?>
