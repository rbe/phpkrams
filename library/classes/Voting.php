<?php

/**
 * 
 * @author rbe
 */
class Voting extends AbstractDbTable {
    
    /**
     * Constructor.
     */
    public function Voting() {
        parent::__construct();
        $this->_name = "voting";
//        $this->_primary = "email";
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
        return parent::insert($data);
    }

    /**
     * DOES NOTHING - this table shouldn"t be updated.
     * @param array $data
     * @param string $where
     * @return mixed
     */
    public function update(array $data, $where) {
        return NULL;
    }

    /**
     * 
     * @param string $status
     * @return Zend_Db_Table_Rowset_Abstract
     */
    public function findByUserGalleryImage($uid, $galleryid, $imageid) {
        $where = array(
            $this->getAdapter()->quoteInto("uid = ?", $uid),
            $this->getAdapter()->quoteInto("galleryid = ?", $galleryid),
            $this->getAdapter()->quoteInto("imageid = ?", $imageid)
        );
        return $this->fetchAll($where);
    }
    
}

?>
