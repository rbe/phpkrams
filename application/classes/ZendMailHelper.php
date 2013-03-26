<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Helper for Zend_Mail.
 * <p>
 * Use as e.g.:
 * $mail = array(
 *      subject = "The subject",
 *      from_address = "bla@blub.de",
 *      from_name = "Bla Blub",
 *      to = array(
 *          array("address" => "re@ceiver.com", "name" => "The receiver")
 *      ),
 *      "body" => '<html><body>The body with an image: <img src="_IMG1_"/></body></html>'
 * );
 * $z = new ZendMailHelper();
 * $z->create($mail)
 *   ->inlineAttachment("_IMG1_", array(
 *          "path" => "/path/to/img.png",
 *          "type" => "image/png"
 *      ))
 *   ->send();
 * </p>
 * @author rbe
 */
class ZendMailHelper {

    /**
     * Zend_Mail instance.
     * @var Zend_Mail
     */
    private $mail;

    /**
     * The body.
     * @var string
     */
    private $body;

    /**
     * Constructor.
     */
    public function ZendMailHelper() {
        $this->mail = new Zend_Mail("utf-8");
    }

    /**
     *
     * @param array $info
     * @return ZendMailHelper Provides fluent interface.
     */
    public function create(array $info) {
        // Set Subject:
        if (!$info["subject"]) {
            throw new IllegalStateException("Cannot send mail: no subject!");
        }
        $this->mail->setSubject($info["subject"]);
        // Set From:
        if (!$info["from_address"]) {
            throw new IllegalStateException("Cannot send mail: no from address!");
        }
        $this->mail->setFrom($info["from_address"]/*, $info["from_name"]*/);
        // Add To:
        if (!$info["to"]) {
            throw new IllegalStateException("Cannot send mail: no recipient!");
        }
        foreach ($info['to'] as $t) {
            if ($t["address"]) {
                $this->mail->addTo($t["address"], isset($t["name"]) ? $t["name"] : "");
            }
        }
        // Body
        if ($info["body"]) {
            $this->setBody($info["body"]);
        }
        //
        return $this;
    }

    /**
     * Set body of mail. Use for HTML and non-HTML bodies.
     * @param string $body
     * @return ZendMailHelper Provides fluent interface.
     */
    public function setBody($body) {
        $this->body = $body;
        //
        return $this;
    }

    /**
     *
     * @param array $attachment
     * @return ZendMailHelper Provides fluent interface.
     */
    public function inlineAttachment($id, array $attachment) {
        // Create attachment
        $att = $this->mail->createAttachment(file_get_contents($attachment["path"]));
        $att->type = $attachment["type"];
        $att->disposition = Zend_Mime::DISPOSITION_INLINE;
        $att->encoding = Zend_Mime::ENCODING_BASE64;
        $att->filename = basename($attachment["path"]);
        $att->id = "cid_" . md5($att->filename);
        // Integrate image in body
        if (!$this->body) {
            throw new IllegalStateException("Could not inline attachment without body!");
        }
        $count = 0;
        $this->body = str_replace($id, "cid:" . $att->id, $this->body, &$count);
        if ($count == 0) {
            throw new IllegalStateException("Attachment could not be inlined!");
        }
        //
        return $this;
    }

    /**
     * Send mail.
     */
    public function send() {
        if (strstr($this->body, "<html")) {
            $this->mail->setBodyHtml($this->body);
        }
        $this->mail->setBodyText(strip_tags($this->body));
        $this->mail->send();
    }

}

?>
