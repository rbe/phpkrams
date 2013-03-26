<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * @author rbe
 */
class GoogleWebSearch {

    /**
     * Query arguments.
     * @var array
     */
    private $query_args;

    /**
     * Maximum number of search results depending on query parameter 'rsz'.
     * @var integer
     */
    private $maxRszResultCount;

    /**
     * Result from Google search.
     * @var array
     */
    private $google_result;

    /**
     * Array with search results.
     * @var SearchResult
     */
    private $result;

    /**
     * Constructor.
     * @param array $args
     */
    public function GoogleWebSearch($args = NULL) {
        $this->query_args = $args;
        $this->maxRszResultCount = array("small" => 4, "large" => 8);
        $this->google_result = NULL;
        $this->result = NULL;
    }

    /**
     * Perform search through Google's AJAX search API.
     * @param array $args
     * @param string $referer
     * @return boolean true
     */
    public function search($args = NULL, $referer = 'http://localhost/test/') {
        // Pre-condition: $args must not be empty
        if (NULL != $args) {
            // Save query arguments
            $this->query_args = $args;
        }
        if (NULL == $this->query_args) {
            throw new InvalidArgumentException("No query given!");
        }
        // Reset results
        $this->google_result = NULL;
        $this->result = NULL;
        //
        $url = "http://ajax.googleapis.com/ajax/services/search/web";
        // At this time (2009-01), only valid value for protocol version is 1.0
        $this->query_args['v'] = '1.0';
        // Check search arguments
        if (NULL == $this->query_args['q'] || NULL == $this->query_args['v']) {
            throw new IllegalStateException("Need q and v!");
        }
        // Build query string
        $url .= '?' . http_build_query($this->query_args, '', '&');
        // Initialize cURL
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        // The referer *must* be set
        curl_setopt($ch, CURLOPT_REFERER, $referer);
        $body = curl_exec($ch);
        curl_close($ch);
        // Decode and return the response
        $this->google_result = json_decode($body, true);
        // Post-condition: $result must not be empty
        if (NULL == $this->google_result) {
            throw new IllegalStateException("Search result is empty?!");
        }
        // Check response
        $st = $this->getResponseStatus();
        if ($st != 200) {
            throw new IllegalStateException("Invalid response status (" . $st . "/" . $this->getResponseDetails() . ")");
        }
        return true;
    }

    /**
     * Return last status of search request sent to Google.
     * @return integer
     */
    public function getResponseStatus() {
        // Pre-condition: search must be done/$result must not be empty.
        if(NULL == $this->google_result) {
            throw new IllegalStateException("getResponseStatus: No search performed?!");
        }
        return $this->google_result['responseStatus'];
    }

    /**
     * Return last status of search request sent to Google.
     * @return integer
     */
    public function getResponseDetails() {
        // Pre-condition: search must be done/$result must not be empty.
        if(NULL == $this->google_result) {
            throw new IllegalStateException("getResponseDetails: No search performed?!");
        }
        return $this->google_result['responseDetails'];
    }

    /**
     * Return results returned by last search request sent to Google.
     * @return array
     */
    private function getRawResults() {
        // Pre-condition: search must be done/$result must not be empty.
        if(NULL == $this->google_result) {
            throw new IllegalStateException("getRawResults: No search performed?!");
        }
        return $this->google_result['responseData']['results'];
    }

    /**
     * Get cursor: information of current page, available pages.
     * ["cursor"]=>  array(4) {
     *      ["pages"]=>  array(8) {
     *          [0]=>  array(2) {
     *              ["start"]=>  string(1) "0"
     *              ["label"]=>  int(1)
     *          }
     *          [1]=>  array(2) {
     *              ["start"]=>  string(1) "8"
     *              ["label"]=>  int(2)
     *          }
     *          ...
     *      }
     *      ["estimatedResultCount"]=>  string(4) "3380"
     *      ["currentPageIndex"]=>  int(0)
     *      ["moreResultsUrl"]=>  string(85) "http://www.google.com/search?oe=utf8&ie=utf8&source=uds&start=0&hl=de&q=Ralf+Bensmann"
     * }
     * @return array
     */
    private function getCursor() {
        // Pre-condition: search must be done/$result must not be empty.
        if(NULL == $this->google_result) {
            throw new IllegalStateException("getCursor: No search performed?!");
        }
        return $this->google_result['responseData']['cursor'];
    }

    /**
     * Get index of current page.
     * @return integer
     */
    public function getCurrentPageIndex() {
        $a = $this->getCursor();
        return $a['currentPageIndex'];
    }

    /**
     * Get index of last page.
     * @return integer
     */
    public function getMaxPageIndex() {
        $a = $this->getCursor();
        return count($a['pages']);
    }

    /**
     * Get URL for more results.
     * @return string
     */
    public function getMoreResultsUrl() {
        $a = $this->getCursor();
        return $a['moreResultsUrl'];
    }

    /**
     * Get URL for more results.
     * @return string
     */
    public function getEstimatedResultCount() {
        $a = $this->getCursor();
        return $a['estimatedResultCount'];
    }

    /**
     * Return search results for last query as array of SearchResult instances.
     * @param integer $count
     * @return array
     */
    public function getResults($resultCount = 1) {
        // Pre-condition: $count must be >= 1
        if (NULL == $resultCount || 0 == $resultCount) {
            throw new InvalidArgumentException("Null argument!");
        }
        // Perform search if not done yet
        try {
            $rawResults = $this->getRawResults();
        } catch (IllegalStateException $e) {
            $this->search();
        }
        //
        $pageCount = intval(ceil($resultCount / $this->maxRszResultCount[$this->query_args['rsz']]));
        if ($pageCount > $this->getMaxPageIndex()) {
            $pageCount = $this->getMaxPageIndex();
        }
        // Perform search(es)
        $a = array();
        for ($i = 0; $i < $pageCount; $i++) {
            // Create SearchResult instances
            $cpi = $this->getCurrentPageIndex();
            $pos = 0;
            foreach ($this->getRawResults() as $r) {
                if (count($a) < $resultCount) {
                    $a[] = new SearchResult($cpi + 1, ++$pos, $r['url'], $r['title'], $r['content']);
                }
            }
            $this->nextPage();
        }
        return $a;
    }

    /**
     * Return search results of 'next page' as array of SearchResult instances.
     * @return array
     */
    public function nextPage() {
        // Compute next 'start' parameter for Google
        $actualStart = $this->query_args['start'];
        $nextStart = $actualStart + $this->maxRszResultCount[$this->query_args['rsz']];
        //print $actualStart . " -> " . $nextStart . "<br/>";
        if ($nextStart > $this->getMaxPageIndex() * $this->maxRszResultCount[$this->query_args['rsz']]) {
            throw new IllegalStateException("No more result pages available!");
        }
        // Perform search
        $this->query_args['start'] = $nextStart;
        try {
            $this->search();
        } catch (IllegalStateException $e) {

        }
    }

}

?>
