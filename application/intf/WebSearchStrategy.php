<?php

/**
 * Strategy Pattern: abstract different search engines.
 * @author rbe
 */
interface WebSearchStrategy {

    /**
     * Perform search.
     * @param array $args
     * @param string $referer
     * @return array or NULL
     */
    function search($args = NULL, $referer = 'http://localhost/test/');

    /**
     * Return last status of search request sent to Google.
     * @return integer
     */
    function getResponseStatus();

    /**
     * Return last status of search request sent to Google.
     * @return integer
     */
    function getResponseDetails();

    /**
     * Get index of current page.
     * @return integer
     */
    function getCurrentPageIndex();

    /**
     * Get index of last page.
     * @return integer
     */
    function getMaxPageIndex();

    /**
     * Get URL for more results.
     * @return string
     */
    function getMoreResultsUrl();

    /**
     * Get URL for more results.
     * @return string
     */
    function getEstimatedResultCount();

    /**
     * Return search results for last query as array of SearchResult instances.
     * @return array
     */
    function getResults();

    /**
     * Return search results of 'next page' as array of SearchResult instances.
     * @return array
     */
    function nextPage();

}

?>
