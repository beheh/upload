<?php

require('UploadToken.class.php');

class Upload {

    private $pdo;
    private $tokens;

    function __construct() {
        $this->pdo = new PDO('mysql:dbname=database;host=localhost', 'username', 'password');
    }

    private function getExistingTokens() {
        $sql = $this->pdo->prepare('SELECT `id`, `token` FROM `upload_token`');
        $sql->execute();
        $tokens = array();
        foreach($sql->fetchAll(PDO::FETCH_ASSOC) as $row) {
            $tokens[$row['token']] = $row;
        }
        return $tokens;
    }

    protected function generateToken() {
        if(!isset($this->tokens)) {
            $this->tokens = $this->getExistingTokens();
        }

        $letters = 'ABCDEFGHIJKLMNOPQRSTUVXYZ';
        $length = strlen($letters);
        do {
            $token = '';
            for($i = 0; $i < 6; $i++) {
                $num = rand(0, $length - 1);
                $token .= substr($letters, $num, 1);
            }
        }
        while(isset($this->tokens[$token]));
        //$token = 'AAAAAA';
        return $token;
    }

    protected function addToken() {
        do {
            $token = $this->generateToken();
            $sql = $this->pdo->prepare('INSERT INTO `upload_token` (`token`) VALUES ( :token )');
            $sql->execute(array(':token' => $token));
        }
        while(!$this->pdo->lastInsertId());

        $token = new UploadToken($this->pdo->lastInsertId(), $token);
        $this->tokens[$token->getToken()] = array('id' => $token->getId());
        return $token;
    }

    public function generateTokenPair() {
        $upload = $this->addToken();
        $download = $this->addToken();

        $sql = $this->pdo->prepare('INSERT INTO `upload_file` (`token_upload`, `token_download`, `created`, `file`) VALUES ( :upload , :download , :created , :file )');
        $sql->execute(array(':upload' => $upload->getId(), ':download' => $download->getId(), ':created' => $_SERVER['REQUEST_TIME'], ':file' => ''));

        return array('upload' => $upload->getToken(), 'download' => $download->getToken());
    }

    public function getToken($token) {
        if(!isset($this->tokens)) {
            $this->tokens = $this->getExistingTokens();
        }

        if(isset($this->tokens[$token])) {
            $token = new UploadToken($this->tokens[$token]['id'], $token);
            return $token;
        }
        else {
            return false;
        }
    }

    public function uploadFile($token, $file) {
        $token = $this->getToken($token);

        if(!$token) {
            return false;
        }
        $name = basename($file['name']);

        $sql = $this->pdo->prepare('SELECT `id` FROM `upload_file` WHERE `token_upload` = :token AND `uploaded` = \'\' LIMIT 1');
        $sql->execute(array(':token' => $token->getId()));
        $row = $sql->fetch();

        $time = $_SERVER['REQUEST_TIME'];
        $path = 'files/'.$time.'_'.$row['id'];

        if(!file_exists($path) && move_uploaded_file($file['tmp_name'], $path)) {
            $sql = $this->pdo->prepare('UPDATE `upload_file` SET `uploaded` = :uploaded , `file` = :file WHERE `token_upload` = :token');
            $sql->execute(array(':uploaded' => $time, ':file' => $name, ':token' => $token->getId()));
            if($sql->rowCount()) {
                return true;
            }
            else {
                unlink($path);
                return false;
            }
        }
        else {
            return false;
        }
    }

}

?>
