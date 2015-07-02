<?php
require('php/Upload.class.php');
$token = '';
if(isset($_REQUEST['token'])) {
    $token = htmlspecialchars(substr($_REQUEST['token'], 0, 6));
}
$result = false;
if(isset($_FILES['file'])) {
    $upload = new Upload();
    if($upload->uploadFile($token, $_FILES['file'])) {
        $result = 'Datei erfolgreich hochgeladen.';
    }
    else {
        $result = 'Datei konnte nicht hochgeladen werden.';
    }
}
?>
<!doctype html>
<html>
    <head>
        <title>uploadtool</title>
        <script type="text/javascript" src="js/jquery-2.1.0.min.js"></script>
        <script type="text/javascript" src="js/upload.js"></script>
        <meta http-equiv="content-type" content="text/html; charset=UTF-8">
        <link rel="stylesheet" type="text/css" href="css/upload.css">
        <link rel="search" href="http://upload.beheh.de/search.xml" type="application/opensearchdescription+xml" title="upload/download">
    </head>
    <body ondragenter="">
        <h1><a href="http://upload.beheh.de">Uploadtool</a></h1>
        <form enctype="multipart/form-data" method="post" action=".">
            <table>
                <tr>
                    <th><label for="token">Token</label></th>
                    <td><input type="text" name="token" id="token" maxlength="6" value="<?php echo $token; ?>" required autofocus><span id="token_text"></span><img src="loader.gif" alt="Überprüfe" id="token_working" style="display: none;"></td>
                </tr>
                <tr id="row_check" style="display: none;">
                    <th>&nbsp;</th><td><input type="submit" id="check" name="check" value="Weiter"></td>
                </tr>
				<?php
                if($result) {
                    ?>
                    <tr id="row_upload_result">
                        <th>Status:</th><td><?php echo $result; ?></td>
                    </tr>
                    <?php
                }
                ?>
                <tr id="row_upload">
                    <th><label for="file">Datei</label></th>
                    <td><input type="file" name="file" id="file"><span id="upload_file" style="display: none;"></span></td>
                </tr>
                <tr id="row_upload_submit">
                    <th>&nbsp;</th><td><input type="submit" id="upload" name="upload" value="Weiter"></td>
                </tr>
                <tr id="row_download_wait" style="display: none;">
                    <th><label>Datei</label></th>
                    <td><span id="download_wait"><img src="loader.gif" alt="Lädt"> Warte auf Upload...</span> <span id="download_notify" class="small" style="display: none;"><a href="#" id="download_notify_link">Benachrichtigen</a></span></td>
                </tr>
                <tr id="row_download" style="display: none;"><th><label>Datei</label></th>
                    <td><a href="http://upload.beheh.de/files/" id="download"></a></td><iframe style="display:none" id="download_frame"></iframe></tr>
            </table>
        </form>
        <footer>
            <div>&raquo; <a href="http://beheh.de">Powered by beheh.de</a></div>
        </footer>
        <img src="loader.gif" alt="Preloading" style="width: 0; height: 0;">
    </body>
</html>
