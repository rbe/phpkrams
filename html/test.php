<?php

require_once '../library/autoload.php';

function pr($g) {
    print "<p>\n";
    $i = 0;
    foreach ($g->getResults(27) as $sr) {
        print $sr->toString() . "<br>\n";
        $i++;
    }
    print "Results: " . $i;
    print "</p>\n";
}

function test() {
    $g = new GoogleWebSearch(
        array(
            "q" => "Ralf Bensmann",
            "rsz" => "large",
            "start" => 0,
            "hl" => "de"
        )
    );
    try {
        pr($g);
        print "<p>\n";
    } catch (InvalidArgumentException $e) {
        print "No query: " . $e->getMessage();
    } catch (IllegalStateException $e) {
        print "No result: " . $e->getMessage();
        print $e->getTraceAsString();
    }
}

?>
