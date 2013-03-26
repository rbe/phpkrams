<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 *
 * @author rbe
 */
class SeoFacade {

    /**
     * Language.
     * @var string
     */
    private $langauge;

    /**
     * List of keywords to analyze.
     * @var array
     */
    private $keywords;

    /**
     * URL to analyze.
     * @var string
     */
    private $url;

    /**
     * Constructor.
     * @param <type> $keywords
     * @param <type> $url
     */
    public function SeoFacade($language, array $keywords, $url) {
        $this->language = $language;
        $this->keywords = $keywords;
        $this->url = $url;
    }

    /**
     *
     */
    public function analyze() {
        $result = "<table border=1>\n";
        // Ask Google for every keyword
        foreach ($this->keywords as $k) {
            $result .= "<tr><td colspan=5>Asking Google for keyword '" . $k . "' pointing to URL " . $this->url . "</td></tr>\n";
            //
            try {
                $g = new GoogleWebSearch(
                    array(
                        "q" => '"' . $k . '"',
                        "rsz" => "large",
                        "start" => 0,
                        "hl" => $this->language
                    )
                );
                $g->search();
                //
                $result .= "<tr><td colspan=5>Found " . $g->getMaxPageIndex() . " pages with estimated result count of " . $g->getEstimatedResultCount() . "</td></tr>";
                //
                $i = 0;
                foreach ($g->getResults(100) as $sr) {
                    $i++;
                    $p = stripos($sr->getUrl(), $this->url);
                    //print "!" . $sr->getUrl() . "! == !" . $this->url . "! " . $p . "<br/>";
                    if($p !== false) {
                        $result .= "<tr><td><b>YES</b></td><td>" . $this->url . "</td><td>" . $k . "</td><td>page " . $sr->getPage() . " position " . $sr->getPosition() . "</td><td>" . $sr->toString() . "</td>";
                    } else {
                        $result .= "<tr><td>NO</td><td>" . $this->url . "</td><td>" . $k . "</td><td>page " . $sr->getPage() . " position " . $sr->getPosition() . "</td><td>" . $sr->toString() . "</td>";
                    }
                }
            } catch (InvalidArgumentException $e) {
                print "No query: " . $e->getMessage();
            } catch (IllegalStateException $e) {
                print "No result: " . $e->getMessage();
                print $e->getTraceAsString();
            }
            // Build KeywordRanking instances
        }
        $result .= "</table>\n";
        return $result;
        // Auswertung anzeigen: Welche Seite/Platz von wievielen Ergebnissen?
        // Tests vergleichen können
        // Executive Summary: x Keywords gecheckt, x davon auf 1. Seite, x auf 2. Seite, x nicht auf den ersten x Seiten
    }

}

?>
