var working = false;

var step = 0;
var token = '';
var upload = false;
var download = false;
var file = '';

var check = 0;
var popped = false;

$(document).ready(function() {
    $('#row_check').show();
    readToken();
    setStep(1, false, true);
    if(token) {
        checkToken();
    }

    $('#token').keydown(function() {
        if(working || upload || download) return;
        $('#token_text').fadeOut();
    });

    $('#check').click(function(e) {
        e.preventDefault();
        if($('#row_upload_result')) $('#row_upload_result').hide();
        if(working) return;

        readToken();
        if(!token) {
            $('#token').focus()
            $('#token_text').text('ist kein gültiges Token.');
            $('#token_text').fadeIn();
            return;
        }
        checkToken();

    });
});

function readToken() {
    token = $('#token').val().toUpperCase();
    $('#token').val(token);
}

function checkToken() {
    console.log("checking token");
    working = true;
    $('#token_working').show();
    $('#token').attr('disabled', true);
    $('#check').attr('disabled', true);
    $('#token_text').hide();
    popped = false;
    $.ajax({
        type: 'POST',
        url: 'ajax.php',
        cache: false,
        data: JSON.stringify({
            'token': token
        }),
        success: function(result) {
            if(!result.error) {
                if(result.file) {
                    file = result.file;
                }
                if(result.type == 'upload') {
                    upload = true;
                }
                else {
                    download = true;
                }
                if(download) {
                    if(result.file) {
                        setStep(3);
                    }
                    else if(!popped) {
                        nextStep();
                    }
                }
                else if(!popped) {
                    nextStep();
                }
            }
            else {
                $('#token_text').text(result.error+'.');
                $('#token_text').fadeIn();
            }
        },
        error: function(a, b, c) {
            $('#token_text').text('konnte nicht überprüft werden.');
            $('#token_text').fadeIn();
        },
        complete: function() {
            working = false;
            $('#token_working').hide();
            $('#token').removeAttr('disabled');
            $('#check').removeAttr('disabled');
        },
        dataType: 'json'
    });
}

function setStep(new_step, dontpush, replace) {
    console.log("setting step to "+new_step);
    if(step != new_step) {
        if(!dontpush) {
            var name = 'step' + new_step;
            switch(new_step) {
                case 1:
                    name = 'input';
                    break;
                case 2:
                    if(download)
                        name = 'wait';
                    if(upload)
                        name = 'upload';
                    break;
                case 3:
                    name = 'download';
                    break;
            }
            var state = {
                'step': new_step,
                'token': token,
                'download': download,
                'upload': upload,
                'file': file
            };
            if (history.pushState) {
                if(replace) {
                    console.log("replacing state with step "+new_step);
                    history.replaceState(state, '', '#'+name);
                }
                else {
                    console.log("adding state with step "+new_step);
                    history.pushState(state, '', '#'+name);
                }
            }
            else {
                location.hash = name;
            }
        }
        step = new_step;
    }
    return updateStep();
}

if(!history.pushState) {
    $(window).bind('hashchange', function() {
        console.log("hash changed to "+hash);
        new_step = step;
        hash = location.hash;
        upload = false;
        download = false;
        switch(hash) {
            case '#input':
                new_step = 1;
                break;
            case '#wait':
                new_step = 2;
                if(file) new_step = 3;
                download = true;
                break;
            case '#upload':
                new_step = 2;
                upload = true;
                break;
            case '#download':
                new_step = 3;
                download = true;
                break;
        }
        setStep(new_step);
    });
}

window.onpopstate = function(e) {
    if(e.state && e.state.step) {
        popped = true;
        console.log("pop step to "+e.state.step);
        token = e.state.token;
        download = e.state.download;
        upload = e.state.upload;
        file = e.state.file;
        setStep(e.state.step, false, true);
    }
};

function nextStep(replace) {
    console.log("next step");
    setStep(step + 1, false, replace);
    return updateStep();
}

function updateStep() {
    console.log("updatting step to "+step);
    if(step == 1) {
        $('#token').removeAttr('disabled');
        $('#check').removeAttr('disabled');
        $('#token').fadeIn();
        $('#row_check').fadeIn();
        $('#token_text').hide();
        $('#token_text').removeClass('big');
        $('#row_check').fadeIn();
        if(working) {
            $('#token_waiting').show();
        }
        else {
            $('#token_waiting').hide();
        }
        $('#token').focus();
    }
    else {
        $('#token').hide();
        $('#token_text').text(token);
        $('#token_text').fadeIn();
        $('#token_text').addClass('big');
        $('#row_check').finish();
        $('#row_check').hide();
    }
    if(step == 2) {
        if(download) {
            $('#row_download_wait').fadeIn();
            scheduleCheckDownload();
        }
        else {
            $('#row_download_wait').hide();
            clearTimeout(check);
        }
        if(upload) {
            if(file) {
                $('#file').hide();
		if($('#row_upload_result').length == 0) {
		    $('#upload_file').text('Bereits hochgeladen.');
		    $('#upload_file').show();
		    $('#row_upload').fadeIn();
		}
		else {
		    $('#row_upload').hide();
		}
            }
            else {
                $('#upload_file').fadeIn();
                $('#file').fadeIn();
                $('#row_upload_submit').fadeIn();
		$('#row_upload').fadeIn();
            }
        }
        else {
            $('#row_upload').hide();
            $('#file').hide();
            $('#row_upload_submit').hide();
        }
    }
    else {
        $('#row_upload').hide();
        $('#upload_file').hide();
        $('#file').hide();
        $('#row_upload_submit').hide();
        $('#row_download_wait').hide();
    }
    if(step == 3) {
        if(download) {
            $('#row_download').fadeIn();
            if(file) {
                $('#download').text(file);
                var url = 'http://upload.beheh.de/f/'+token;
                $('#download').attr('href', url);
                document.getElementById('download_frame').contentDocument.location.replace(url);
            }
        }
        else {
            $('#row_download').hide();
        }
    }
    else {
        $('#row_download').hide();
    }
    return true;
}

function scheduleCheckDownload() {
    clearTimeout(check);
    check = setTimeout('checkDownload()', 5000);
    return true;
}

function checkDownload() {
    $.ajax({
        type: 'POST',
        url: 'ajax.php',
        cache: false,
        data: JSON.stringify({
            'token': token
        }),
        success: function(result) {
            if(!result.error) {
                if(result.file) {
                    file = result.file;
                    setStep(3, false, true);
                }
                else {
                    scheduleCheckDownload();
                }
            }
            else {
                setStep(1);
                $('#token_text').text(result.error+'.');
            }
        },
        error: function() {
            scheduleCheckDownload();
        }
    });
}
