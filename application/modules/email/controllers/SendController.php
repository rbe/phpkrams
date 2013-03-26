<!-- Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com -->
<?php

/**
 * Send an email.
 * @author rbe
 */
class Email_SendController extends ZendControllerBase {

    public function init() {
        parent::init();
    }

    public function preDispatch() {
        parent::preDispatch();
    }

    public function postDispatch() {
        parent::postDispatch();
    }

    /**
     * Send the mail using a PHPTAL template.
     */
    public function doAction() {
        // Check REMOTE_ADDR; must be "localhost"
        $remoteaddr = $_SERVER["REMOTE_ADDR"];
        $thisaddr = array(
            "localhost",
            "127.0.0.1",
            $_SERVER["SERVER_ADDR"]
        );
        $ok = false;
        foreach ($thisaddr as $a) {
            if ($remoteaddr == $a) {
                $ok = true;
                break;
            }
        }
        if (!$ok) {
            print "Only localhost is permitted, you are " . $remoteaddr . "!<br/>";
            //exit;
        }
        // Get request
        $request = $this->getRequest();
        // Email To: address
        $toAddr = $request->getParam("to");
        // Validate to-address and go on
        if (ZendHelper::validateEmail($toAddr)) {
            // Zend info
            $module = $request->getModuleName();
            $controller = $request->getControllerName();
            $action = $request->getActionName();
            // Template to send
            $tpl = $request->getParam("tpl");
            // Create a new template object
            $template = new PHPTAL(
                ZendHelper::getRoot()
                . "/application/modules"
                . "/" . $module
                . ZendHelper::getConfig()->phptal->template->dir
                . "/" . $controller
                . "/" . $tpl
                . "." . ZendHelper::getConfig()->phptal->template->suffix
            );
            // Process template
            $phptalTpl = "";
            try {
                // Add all request parameters to template
                foreach ($this->getRequest()->getParams() as $k => $v) {
                    $template->$k = $v;
                }
                // Execute the template
                $phptalTpl = $template->execute();
                // Send mail
                $mail = new ZendMailHelper();
                $mail->create(array(
                        "subject" => ZendHelper::getConfig()->email->header->$tpl->subject,
                        "from_address" => ZendHelper::getConfig()->email->header->$tpl->sender->email,
                        "from_name" => ZendHelper::getConfig()->email->header->$tpl->sender->name,
                        "to" => array(
                            array(
                                "address" => $toAddr
                            )
                        ),
                        "body" => $phptalTpl
                    )
                )
//                // Add attachments; search and replace img-tag _PLACEHOLDER_s
//                ->inlineAttachment("_EMAILLOGO_", array(
//                    "path" => "../html/_files/images/flower_emaillogo.gif",
//                    "type" => "image/gif"
//                    )
//                )
                ->send();
                // That's it.
                print "<html><body>Mail sent to " . $toAddr . "</body></html>";
            } catch (Exception $e){
                echo $e;
            }
        } else {
            print "Email address validation failed!";
        }
        // No further processing - EXIT!
        exit;
    }

}

?>
