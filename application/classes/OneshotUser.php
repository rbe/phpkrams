<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * 
 * @author rbe
 */
class OneshotUser extends AbstractDbTable {

    /**
     * Constructor.
     */
    public function OneshotUser() {
        parent::__construct();
        $this->_name = "oneshotuser";
        $this->_primary = "email";
        $this->_sequence = false;
    }

    /**
     *
     * @param array $data
     * @return mixed
     */
    public function insert(array $data) {
        // Add timestamp
        if (empty($data["created_on"])) {
            $data["created_on"] = date("Y-m-d H:i:s");
        }
        // Don"t allow updated_on to be set
        if (!empty($data["updated_on"])) {
            unset($data["updated_on"]);
        }
        //
        return parent::insert($data);
    }

    /**
     *
     * @param array $data
     * @param string $where
     * @return mixed
     */
    public function update(array $data, $where) {
        // Add timestamp
        if (empty($data["updated_on"])) {
            $data["updated_on"] = date("Y-m-d H:i:s");
        }
        // Don"t allow created_on to be updated
        if (!empty($data["created_on"])) {
            unset($data["created_on"]);
        }
        //
        return parent::update($data, $where);
    }

    /**
     * Find user by email.
     * @param string $username
     * @param string $password
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findByEmail($email) {
        $where = array(
            $this->getAdapter()->quoteInto("email = ?", $email)
            //$this->getAdapter()->quoteInto("auth_url_clicked_on IS NOT NULL", "")
        );
        return $this->fetchAll($where, "email");
    }

    /**
     * Find user by auth_url_id.
     * @param string $authId
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findByAuthId($authId) {
        $where = $this->getAdapter()->quoteInto("auth_url_id = ?", $authId);
        return $this->fetchAll($where, "auth_url_id");
    }

}

?>
