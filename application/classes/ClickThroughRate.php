<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Click through rate for a certain search term.
 * @author rbe
 */
class ClickThroughRate {

    /**
     * Ranking.
     * @var Ranking
     */
    private $ranking;

    /**
     * Number of visits (for certain search term).
     * @var integer
     */
    private $visits;

    /**
     * Ask Ads in percent.
     * @var float
     */
    private $askAdwords;

    /**
     * Price Comp in percent.
     * @var float
     */
    private $priceComp;

    /**
     * Add. Review in percent.
     * @var float
     */
    private $addReview;

    /**
     * Search Ad. in percent.
     * @var float
     */
    private $searchAd;

    /**
     * Average click through rate in percent.
     * @var float
     */
    private $averageCtr;

    /**
     * Revenue in currency.
     * @var float
     */
    private $revenue;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->ranking = NULL;
        $this->visits = 0;
        $this->askAdwords = 0;
        $this->priceComp = 0.0;
        $this->addReview = 0.0;
        $this->searchAd = 0.0;
        $this->averageCtr = 0.0;
    }

    /**
     *
     * @return integer
     */
    public function getRanking() {
        return $this->ranking;
    }

    /**
     * @param integer $ranking
     * @return
     */
    public function setRanking($ranking) {
        $this->ranking = $ranking;
    }

    /**
     *
     * @return float
     */
    public function getAddReview() {
        return $this->addReview;
    }

    /**
     *
     * @param float $addReview
     */
    public function setAddReview($addReview) {
        // Pre-condition: >= 0
        if ($addReview < 0) {
            throw new IllegalArgumentException("Negative argument");
        }
        $this->addReview = $addReview;
    }

    /**
     *
     * @return float
     */
    public function getAskAdwords() {
        return $this->askAdwords;
    }

    /**
     *
     * @param float askAds
     */
    public function setAskAdwords($askAds) {
        // Pre-condition: >= 0
        if ($askAds < 0) {
            throw new IllegalArgumentException("Negative argument");
        }
        $this->askAdwords = $askAds;
    }

    /**
     *
     * @return float
     */
    public function getAverageCtr() {
        return $this->averageCtr;
    }

    /**
     *
     * @param float averageCtr
     */
    public function setAverageCtr($averageCtr) {
        // Pre-condition: >= 0
        if ($averageCtr < 0) {
            throw new IllegalArgumentException("Negative argument");
        }
        $this->averageCtr = $averageCtr;
    }

    /**
     *
     * @return float
     */
    public function getPriceComp() {
        return $this->priceComp;
    }

    /**
     *
     * @param float $priceComp
     */
    public function setPriceComp($priceComp) {
        // Pre-condition: >= 0
        if ($priceComp < 0) {
            throw new IllegalArgumentException("Negative argument");
        }
        $this->priceComp = $priceComp;
    }

    /**
     *
     * @return float
     */
    public function getRevenue() {
        return $this->revenue;
    }

    /**
     *
     * @param float $revenue
     */
    public function setRevenue($revenue) {
        // Pre-condition: >= 0
        if ($revenue < 0) {
            throw new IllegalArgumentException("Negative argument");
        }
        $this->revenue = $revenue;
    }

    /**
     *
     * @return float
     */
    public function getSearchAd() {
        return $this->searchAd;
    }

    /**
     *
     * @param float $searchAd
     */
    public function setSearchAd($searchAd) {
        // Pre-condition: >= 0
        if ($searchAd < 0) {
            throw new IllegalArgumentException("Negative argument");
        }
        $this->searchAd = $searchAd;
    }

    /**
     *
     * @return integer
     */
    public function getVisits() {
        return $this->visits;
    }

    /**
     *
     * @param integer $visits
     */
    public function setVisits($visits) {
        // Pre-condition: >= 0
        if ($visits < 0) {
            throw new IllegalArgumentException("Negative argument");
        }
        $this->visits = $visits;
    }

}

?>
