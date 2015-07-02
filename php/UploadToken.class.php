<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of UploadToken
 *
 * @author Benedict
 */
class UploadToken {

    private $id;
    private $token;

    function __construct($id, $token) {
        $this->id = $id;
        $this->token = $token;
    }

    function __toString() {
        return $this->token;
    }

    public function getId() {
        return $this->id;
    }

    public function getToken() {
        return $this->token;
    }

}

?>
