<?php

/**
 * 
 * @author rbe
 */
class FlowerImage {

    /**
     * Get path for image with certain resolution relative to DOCUMENT_ROOT.
     * @param integer $galleryid
     * @param integer $imageid
     * @param integer $res
     * @return string
     */
    public static function getRelativeUrl($gallery, $imageid, $res) {
        // Path for gallery
        $pg = "";
        switch ($gallery) {
            case 2:
                $pg = "";
                break;
            case 15:
                $pg = "09";
                break;
        }
        //
        $p = sprintf("files/gallery%s/image_%d/%04d.jpg", $pg, $res, $imageid);
        if (file_exists($_SERVER['DOCUMENT_ROOT'] . "/" . $p)) {
            return $p;
        } else {
            throw new IllegalStateException("Could not find image");
        }
    }

    /**
     * Get absolute URL for image with certain resolution.
     * @param integer $galleryid
     * @param integer $imageid
     * @param integer $res
     * @return string
     */
    public static function getAbsoluteUrl($gallery, $imageid, $res) {
        $p = self::getRelativeUrl($gallery, $imageid, $res);
        return "http://" . $_SERVER['SERVER_NAME'] . "/" . $p;
    }

}

?>
