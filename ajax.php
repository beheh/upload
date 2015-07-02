<?php

require('php/Upload.class.php');

$input = file_get_contents('php://input');
$request = false;
if($input) {
    $request = json_decode($input, true);
}
header('Content-Type: application/json');
if($request && isset($request['token'])) {
    $token = strtoupper($request['token']);
    if(strlen($token) == 6) {
        $result = array();
        $pdo = new PDO('mysql:dbname=database;host=localhost', 'username', 'password');
        $sql = $pdo->prepare('SELECT * FROM `upload_token` WHERE `token` = :token LIMIT 1');
        $sql->execute(array(':token' => $request['token']));
        $row = $sql->fetch();
        $token_id = $row['id'];
        $sql = $pdo->prepare('SELECT * FROM `upload_file` WHERE `token_upload` = :token_id OR `token_download` = :token_id LIMIT 1');
        $sql->execute(array(':token_id' => $token_id));
        $row = $sql->fetch();
        if($row) {
            if($token_id == $row['token_upload']) {
                $result['type'] = 'upload';
                $result['file'] = false;
                if($row['file'])
                    $result['file'] = true;
            }
            else {
                $result['type'] = 'download';
                $result['file'] = false;
                if($row['file'])
                    $result['file'] = $row['file'];
            }
        }
        else {
            $result['error'] = 'ist kein gültiges Token';
        }
    }
    else {
        $result['error'] = 'ist kein gültiges Token';
    }
}
else {
    $result['error'] = 'Ungültiger Aufruf';
}
echo json_encode($result, JSON_FORCE_OBJECT);
?>
