<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * 
 * @author rbe
 */
class AuthUser extends AbstractDbTable {

    /**
     * Constructor.
     */
    public function AuthUser() {
        parent::__construct();
        $this->_name = "authuser";
        $this->_primary = "uid";
        $this->_sequence = true;
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
     * Find user by uid.
     * @param integer $uid
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findByUid($uid) {
        $where = $this->getAdapter()->quoteInto("uid = ?", $uid);
        return $this->fetchAll($where, "uid");
    }

    /**
     * Find user by username.
     * @param string $username
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findByUsername($username) {
        $where = $this->getAdapter()->quoteInto("username = ?", $username);
        return $this->fetchAll($where, "username");
    }

    /**
     * Find user by username and password.
     * @param string $username
     * @param string $password
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findAuthenticatedUser($username, $password) {
        $where = array(
            $this->getAdapter()->quoteInto("username = ?", $username),
            $this->getAdapter()->quoteInto("password = ?", $password),
            $this->getAdapter()->quoteInto("auth_url_clicked_on IS NOT NULL", ""),
            $this->getAdapter()->quoteInto("auth_url_clicked_on <> '0000-00-00 00:00:00 '", "")
        );
        return $this->fetchAll($where, "username");
    }

    /**
     * Find user by email.
     * @param string $email
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findByEmail($email) {
        $where = $this->getAdapter()->quoteInto("email = ?", $email);
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
