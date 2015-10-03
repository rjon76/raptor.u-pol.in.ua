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
function upProduct(cId, pId) {
    ajax.get(admin_dir+'/products/productup/cid/' + cId + '/pid/' + pId + '/', {'func': 'upProductSucc', 'args': {'pId' : pId}});
}

function upProductSucc(resp, args) {
    eval('var canUp = ' + resp + ';');

    if(canUp) {
        var pId = args['pId'];

        var pdiv = $('#prod' + pId);
        var prevPdiv = pdiv.prev();
        var newPdiv = pdiv.clone(true);

        newPdiv.css('display', 'none');

        pdiv.fadeOut('slow', function() {
            pdiv.remove();
            newPdiv.insertBefore(prevPdiv);
            newPdiv.fadeIn('slow');
        });
    } else {
        alert('This product can not be moved up!');
    }
}


function downProduct(cId, pId) {
    ajax.get(admin_dir+'/products/productdown/cid/' + cId + '/pid/' + pId + '/', {'func': 'downProductSucc', 'args': {'pId' : pId}});
}

function downProductSucc(resp, args) {
    eval('var canDown = ' + resp + ';');

    if(canDown) {
	var pId = args['pId'];

        var pdiv = $('#prod' + pId);
        var nextPdiv = pdiv.next();
        var newPdiv = pdiv.clone(true);

        newPdiv.css('display', 'none');

        pdiv.fadeOut('slow', function() {
	    pdiv.remove();
            newPdiv.insertAfter(nextPdiv);
            newPdiv.fadeIn('slow');
        });
    } else {
        alert('This product can not be moved down!');
    }
}