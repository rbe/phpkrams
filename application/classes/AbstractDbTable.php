<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * Abstract database table base class.
 * @author rbe
 */
abstract class AbstractDbTable extends Zend_Db_Table_Abstract {

    //    /**
    //     *
    //     * @var string
    //     */
    //    protected $_name = 'TABLENAME';
    //
    //    /**
    //     *
    //     * @var string
    //     */
    //    protected $_primary = 'PKNAME';
    //
    //    /**
    //     * Standard in Zend_Db_Table_Abstract; must not be defined.
    //     * @var boolean
    //     */
    //    protected $_sequence = true;

    /**
     *
     * @param array $data
     * @return mixed
     */
    public function insert(array $data) {
        // Add timestamp
        if (empty($data['created_on'])) {
            $data['created_on'] = date("Y-m-d H:i:s");
        }
        // Don't allow updated_on to be set
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
        if (empty($data['updated_on'])) {
            $data['updated_on'] = date("Y-m-d H:i:s");
        }
        // Don't allow created_on to be updated
        if (!empty($data["created_on"])) {
            unset($data["created_on"]);
        }
        //
        return parent::update($data, $where);
    }

    //    /**
    //     * Find user by uid.
    //     * @param integer $uid
    //     * @return Zend_Db_Table_Rowset_Abstract
    //     */
    //    public function findByUid($uid) {
    //        $where = $this->getAdapter()->quoteInto('uid = ?', $uid);
    //        return $this->fetchAll($where, 'uid');
    //    }

}

?>
