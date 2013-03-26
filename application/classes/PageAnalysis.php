<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Page analysis based on click through rate of all rankings of a certain URL.
 * @author rbe
 */
class PageAnalysis {

    /**
     * Click through rates to analyze.
     * @var ClickThroughRate
     */
    private $clickThroughRate;

    /**
     * Constructor.
     */
    public function __construct() {
        $this->clickThroughRate = array();
    }

    /**
     * Add a click through rate.
     * @param ClickThroughRate $ctr
     */
    public function addClickThroughRate($ctr) {
        $this->clickThroughRate[] = $ctr;
    }

    /**
     * Number of click through rates.
     * @return integer
     */
    public function getCtrCount() {
        return count($this->clickThroughRate);
    }

    /**
     * Calculate total revenue of all click through rates.
     * @return float
     */
    public function getTotalRevenue() {
        // Pre condition: clickThroughRate must not be empty
        if (NULL == $this->clickThroughRate || (NULL != $this->clickThroughRate && count($this->clickThroughRate) == 0)) {
            throw new IllegalStateException("No click through rate(s)!");
        }
        //
        $totalRevenue = 0.0;
        foreach ($this->clickThroughRate as $ctr) {
            $totalRevenue += $ctr->getRevenue();
        }
        // Post-condition: >= 0
        if ($totalRevenue < 0) {
            throw new IllegalStateException("Total revenue could not be negative!");
        }
        //
        return $totalRevenue;
    }

    /**
     * Calculate average conversion rate of all click through rates.
     * @return float
     */
    public function getAverageConversionRate() {
        // Pre-condition: clickThroughRate must not be empty
        if (NULL == $this->clickThroughRate || (NULL != $this->clickThroughRate && count($this->clickThroughRate) == 0)) {
            throw new IllegalStateException("No click through rate(s)!");
        }
        //
        $avgConvRate = 0.0;
        $count = 0;
        foreach ($this->clickThroughRate as $ctr) {
            $avgConvRate += $ctr->getAverageCtr();
            $count++;
        }
        $avgConvRate = $avgConvRate / $count;
        // Post-condition: >= 0
        if ($avgConvRate < 0) {
            throw new IllegalStateException("Average conversion rate could not be negative!");
        }
        //
        return $avgConvRate;
    }

    /**
     * Calculate average position.
     * @return float
     */
    public function getAveragePosition() {
        // Pre-condition: clickThroughRate must not be empty
        if (NULL == $this->clickThroughRate || (NULL != $this->clickThroughRate && count($this->clickThroughRate) == 0)) {
            throw new IllegalStateException("No click through rate(s)!");
        }
        //
        $avgPos = 0.0;
        $count = 0;
        foreach ($this->clickThroughRate as $ctr) {
            $avgPos += $ctr->getRanking()->getPosition();
            $count++;
        }
        $avgPos = $avgPos / $count;
        // Post-condition: >= 0
        if ($avgPos < 0) {
            throw new IllegalStateException("Average position could not be negative!");
        }
        //
        return $avgPos;
    }

}

?>
