<?php

/**
 * @Entity @Table(name="Sites")
 */

class Site {

    /** 
      * @Id @Column(type="integer") @GeneratedValue  
      * @var int
    */
    protected $id;
    /** @Column(type="string", unique=true) */
    protected $shortName;
    /** @Column(type="string", length=2000, nullable=true) */
    protected $description;
    /** @Column(type="string", nullable=true) */
    protected $location;
    /** @Column(type="string", nullable=true) */
    protected $domain;

    public function getShortName() {
        return $this->shortName;
    }
    public function getDescription() {
        return $this->description;
    }
    public function getLocation() {
        return $this->location;
    }
    public function getDomain() {
        return $this->domain;
    }    
    /* ======== End of Getters ============= */
    public function setShortName($shortName) {
        $this->shortName = $shortName;
    }
    public function setDescription($description) {
        $this->description = $description;
    }
    public function setLocation($location) {
        $this->location = $location;
    }
    public function setDomain($domain) {
        $this->domain = $domain;
    }}
