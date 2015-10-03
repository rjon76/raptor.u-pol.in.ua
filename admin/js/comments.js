function tabs(tid) {
    var cat = $('#cat'+tid);
    var link = $('#ln'+tid);
    
    if (cat.is(':visible'))
    {
        cat.fadeOut('fast');
        link.attr('class','phide');
    }
    else{
        cat.fadeIn('fast');
        link.attr('class','pshow');
    }
    return false;
}
function pdraw(tid) {
    var cat = $('#cat'+tid);
    var link = $('#ln'+tid);
    if(eval('cat' + tid)) {
	cat.fadeOut('fast');
	eval('cat' + tid + ' = false;');
	link.attr('class','phide');
    }
    else {
	cat.fadeIn('fast');
	eval('cat' + tid + ' = true;');
	link.attr('class','pshow');
    }
    return false;
}
function upComment(cid,id) {
    ajax.get(admin_dir+'/comments/commentup/cid/'+cid+'/id/' + id + '/', {'func': 'upCommentSucc', 'args': {'id' : id}});
}

function upCommentSucc(resp, args) {
    eval('var canUp = ' + resp + ';');

    if(canUp) {
        var id = args['id'];

        var pdiv = $('#comment' + id);
        var prevPdiv = pdiv.prev();
        var newPdiv = pdiv.clone(true);

        newPdiv.css('display', 'none');

        pdiv.fadeOut('slow', function() {
            pdiv.remove();
            newPdiv.insertBefore(prevPdiv);
            newPdiv.fadeIn('slow');
        });
    } else {
        alert('This comment can not be moved up!');
    }
}


function downComment(cid,id) {
    ajax.get(admin_dir+'/comments/commentdown/cid/'+cid+'/id/' + id + '/', {'func': 'downCommentSucc', 'args': {'id' : id}});
}

function downCommentSucc(resp, args) {
    eval('var canDown = ' + resp + ';');

    if(canDown) {
	var id = args['id'];

        var pdiv = $('#comment' + id);
        var nextPdiv = pdiv.next();
        var newPdiv = pdiv.clone(true);

        newPdiv.css('display', 'none');

        pdiv.fadeOut('slow', function() {
	    pdiv.remove();
            newPdiv.insertAfter(nextPdiv);
            newPdiv.fadeIn('slow');
        });
    } else {
        alert('This comment can not be moved down!');
    }
}

function hiddenComment(id) {
    ajax.get(admin_dir+'/comments/hidden/id/' + id + '/', {'func': 'hiddenCommentSucc', 'args': {'id' : id}});
}

function hiddenCommentSucc(resp, args) {
    eval('var isPulled = ' + resp + ';');
    
    var id = args['id'];
    var pdiv = $('#comment' + id);
    
    if(isPulled) {
        pdiv.addClass('row-hidden');
        
    } else {
        pdiv.removeClass('row-hidden');
    }
}