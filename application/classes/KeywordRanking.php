<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Ranking of a certain keyword/URL combination.
 * @author rbe
 */
class KeywordRanking {
    
    /**
     * Search Term.
     * @var string
     */
    private $searchTerm;

    /**
     * Page number of Google Search result.
     * @var integer
     */
    private $page;

    /**
     * Position in page of Google Search result.
     * @var integer
     */
    private $position;

    /**
     * URL.
     * @var string
     */
    private $url;

    /**
     * Constructor.
     */
    public function KeywordRanking() {
        $this->searchTerm = "";
        $this->page = 0;
        $this->position = 0;
        $this->url = NULL;
    }

    /**
     *
     * @return
     */
    public function getPage() {
        return $this->page;
    }

    /**
     *
     * @param page
     */
    public function setPage($page) {
        // Pre-condition: >= 0
        if ($page < 0) {
            throw new InvalidArgumentException("Negative argument");
        }
        $this->page = $page;
    }

    /**
     *
     * @return
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     *
     * @param position
     */
    public function setPosition($position) {
        // Pre-condition: >= 0
        if (position < 0) {
            throw new InvalidArgumentException("Negative argument");
        }
        $this->position = $position;
    }

    /**
     *
     * @return
     */
    public function getSearchTerm() {
        return $this->searchTerm;
    }

    /**
     *
     * @param searchTerm
     */
    public function setSearchTerm($searchTerm) {
        // Pre-condition: not empty
        if (NULL == $searchTerm || (NULL != $searchTerm && count($searchTerm) == 0)) {
            throw new InvalidArgumentException("Null argument");
        }
        $this->searchTerm = $searchTerm;
    }

    /**
     *
     * @return
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     *
     * @param url
     */
    public function setUrl($url) {
        // Pre-condition: not empty
        if (NULL == $url || (NULL != $url && count($url) == 0)) {
            throw new InvalidArgumentException("Null argument");
        }
        $this->url = $url;
    }

}

?>
