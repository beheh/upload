<?php
require('php/Upload.class.php');

//$upload = new Upload();

$token = trim($_GET['token']);
if(strlen($token) != 6) {
    exit();
}

$pdo = new PDO('mysql:dbname=database;host=localhost', 'username', 'password');
$sql = $pdo->prepare('SELECT `id` FROM `upload_token` WHERE `token` = :token LIMIT 1');
$sql->execute(array(':token' => $token));
$row = $sql->fetch(PDO::FETCH_ASSOC);

$id =  isset($row['id']) ? $row['id'] : 0;

$sql = $pdo->prepare('SELECT `id`, `uploaded`, `file` FROM `upload_file` WHERE `token_download` = :token_id LIMIT 1');
$sql->execute(array(':token_id' => $id));
$row = $sql->fetch(PDO::FETCH_ASSOC);

if($row && is_numeric($row['id'])) {
    //prevent nasty directory traversal from potentially manipulated sql
    $file = basename($row['file']);
    $path = 'files/'.$row['uploaded'].'_'.$row['id'];

    //check if file even exists
    if(file_exists($path)) {

        $size = filesize($path);

        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$file.'');
        header('Content-Length: '.$size);

        readfile($path);
    }
    else {
        header('HTTP/1.0 404 Not Found');
        header('Content-Type: text/plain');
        echo 'File no longer exists.';
    }
}
else {
    header('HTTP/1.0 404 Not Found');
    header('Content-Type: text/plain');
    echo 'Token is invalid.';
}
?>
