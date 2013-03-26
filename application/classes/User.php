<?php

// Copyright (C) 2009 Informationssysteme Ralf Bensmann, http://www.bensmann.com

/**
 * An user.
 * @author rbe
 */
class User extends AbstractBean {

    /**
     * User id.
     * @var integer
     */
    public $uid;

    /**
     * Salutation.
     * @var string
     */
    public $salutation;

    /**
     * Title.
     * @var string.
     */
    public $title;

    /**
     *
     * @var string
     */
    public $firstname;

    /**
     *
     * @var string
     */
    public $lastname;

    /**
     *
     * @var string
     */
    public $street;

    /**
     *
     * @var string
     */
    public $zip;

    /**
     *
     * @var string
     */
    public $city;

    /**
     *
     * @var string
     */
    public $telephone;

    /**
     *
     * @var string
     */
    public $homepage;

    /**
     * Email address of user.
     * @var string
     */
    public $email;

    /**
     * Username.
     * @var string
     */
    public $username;

    /**
     * Password.
     * @var string
     */
    public $password;


    /**
     * Constructor.
     * @param string $username
     */
    public function User($username = NULL) {
        $this->username = $username;
    }

    /**
     *
     * @return string
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     *
     * @param string $email
     */
    public function setEmail($email) {
        $this->email = $email;
    }

    /**
     *
     * @return string
     */
    public function getTitle() {
        return $this->title;
    }

    /**
     *
     * @param string $name
     */
    public function setTitle($title) {
        $this->title = $title;
    }

    /**
     *
     * @return string
     */
    public function getPassword() {
        return $this->password;
    }

    /**
     *
     * @param string $name
     */
    public function setPassword($password) {
        $this->password = $password;
    }

    /**
     *
     * @return string
     */
    public function getFirstname() {
        return $this->firstname;
    }

    /**
     *
     * @param string $name
     */
    public function setFirstname($firstname) {
        $this->firstname = $firstname;
    }

    /**
     *
     * @return string
     */
    public function getLastname() {
        return $this->lastname;
    }

    /**
     *
     * @param string $name
     */
    public function setLastname($lastname) {
        $this->lastname = $lastname;
    }

    /**
     *
     * @return string
     */
    public function getStreet() {
        return $this->street;
    }

    /**
     *
     * @param string $name
     */
    public function setStreet($street) {
        $this->street = $street;
    }

    /**
     *
     * @return string
     */
    public function getZip() {
        return $this->zip;
    }

    /**
     *
     * @param string $name
     */
    public function setZip($zip) {
        $this->zip = $zip;
    }

    /**
     *
     * @return string
     */
    public function getCity() {
        return $this->city;
    }

    /**
     *
     * @param string $name
     */
    public function setCity($city) {
        $this->city = $city;
    }

    /**
     *
     * @return string
     */
    public function getTelephone() {
        return $this->telephone;
    }

    /**
     *
     * @param string $name
     */
    public function getSalutation() {
        return $this->salutation;
    }

    /**
     *
     * @param string $salutation
     */
    public function setSalutation($salutation) {
        $this->salutation = $salutation;
    }

    /**
     * Is salutation set?
     * @return boolean
     */
    public function hasNoSalutation() {
        return $this->salutation ? false : true;
    }

    /**
     * Is salutation set?
     * @return boolean
     */
    public function hasMrsSalutation() {
        return $this->salutation == "Frau" ? true : false;
    }

    /**
     * Is salutation set?
     * @return boolean
     */
    public function hasMrSalutation() {
        return $this->salutation == "Herr" ? true : false;
    }

    /**
     *
     * @param string $telephone
     */
    public function setTelephone($telephone) {
        $this->telephone = $telephone;
    }

    /**
     *
     * @return string
     */
    public function getHomepage() {
        return $this->homepage;
    }

    /**
     *
     * @param string $homepage
     */
    public function setHomepage($homepage) {
        $this->homepage = $homepage;
    }

    /**
     *
     * @return string
     */
    public function getUsername() {
        return $this->username;
    }

    /**
     *
     * @param string $username
     */
    public function setUsername($username) {
        $this->username = $username;
    }

    /**
     * User id.
     * @return integer
     */
    public function getUid() {
        return $this->uid;
    }

    /**
     *
     * @param integer $uid
     */
    public function setUid($uid) {
        $this->uid = $uid;
    }


}

?>
