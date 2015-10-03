var browserOpen = false;

function changeTextareaValue(resp, args) {
    eval('var val = ' + resp + ';');
    $(args['inputElement']).val(val);
    closeBrowser();
}

function openBrowser(inputElement, siteDir) {
    if(!browserOpen) {

        $('#commanderDisp').fileTree({
            root: siteDir,
            //script: '/jquery.file.tree.php?info=0',
            script: admin_dir+'/files/filetree/?info=0'

        }, function(file) {
            ajax.post(admin_dir+'/content/getimginfo/', 'path=' + encodeURIComponent(file), {'func': 'changeTextareaValue', 'args': {'inputElement' : inputElement}});
        });


        var scrllTop = $(document).scrollTop();
        var docWidth = $(document).width();
        var width = $('#commander').width();

        $('#commander').css('left', (docWidth / 2 - width / 2) + 'px')
                       .css('top', (scrllTop + 100) + 'px');

        $('#commander').fadeIn('fast');

        browserOpen = true;
    }
}

function closeBrowser() {
    $('#commander').fadeOut('fast');
    browserOpen = false;
}

function deleteFile(element, filePath) {
    if(confirm('Delete file?')) {
        ajax.post(admin_dir+'/files/delete/', 'path=' + encodeURIComponent(filePath), {'func': 'deleteFileSucc', 'args': {'element' : $(element)}});
    }
}

function deleteFileSucc(resp, args) {
    eval('var isDeleted = ' + resp + ';');

    if(isDeleted) {
        var element = args['element'];
        var parent = element.parent();
        parent.fadeOut();
        parent.remove();
    } else {
        alert('File can not be deleted!');
    }
}