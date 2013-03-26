<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * A search result from a Search Engine like Google.
 * @author rbe
 */
class SearchResult {

    /**
     * Date/time of this search result.
     * @var array
     */
    private $dateTime;

    /**
     * Page of this entry.
     * @var integer
     */
    private $page;

    /**
     * Position on page of this entry.
     * @var integer
     */
    private $position;

    /**
     * The URL.
     * @var string
     */
    private $url;

    /**
     * Title of URL.
     * @var string
     */
    private $title;

    /**
     * Content of URL.
     * @var string
     */
    private $content;

    /**
     * Constructor.
     * @param integer page
     * @param integer position
     * @param string $url
     * @param string $title
     * @param string $content
     */
    public function SearchResult($page, $position, $url, $title, $content) {
        $this->dateTime = getdate();
        $this->page = $page;
        $this->position = $position;
        $this->url = $url;
        $this->title = $title;
        $this->content = $content;
    }

    /**
     * Date/time of this search result.
     * @see getdate().
     * @return date
     */
    public function getDateTime() {
        return $this->dateTime;
    }

    /**
     *
     * @return integer
     */
    public function getPage() {
        return $this->page;
    }

    /**
     *
     * @return integer
     */
    public function getPosition() {
        return $this->position;
    }

    /**
     *
     * @return string
     */
    public function getUrl() {
        return $this->url;
    }

    /**
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     *
     * @return string
     */
    public function getContent() {
        return $this->content;
    }

    /**
     * Return string representation of this search result.
     * @return string
     */
    public function toString() {
        return "SearchResult[url=" . $this->url . " pp=" . $this->page . "," . $this->position . " title=" . $this->title . "]";
    }

}

?>
