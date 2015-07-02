<?php

require('php/Upload.class.php');

$upload = new Upload();
$tokens = $upload->generateTokenPair();

echo 'Upload: <a href="http://upload.beheh.de/'.$tokens['upload'].'">'.$tokens['upload'].'</a>';
echo '<br>'.PHP_EOL;
echo 'Download: <a href="http://download.beheh.de/'.$tokens['download'].'">'.$tokens['download'].'</a>';

?>
