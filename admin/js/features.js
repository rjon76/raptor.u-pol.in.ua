function upFeature(pId, fId, lang) {
    ajax.get(admin_dir+'/products/featureup/pid/' + pId + '/fid/' + fId + '/lang/' + lang + '/', {'func': 'upFeatureSucc', 'args': {'fId' : fId}});
}

function upFeatureSucc(resp, args) {
    eval('var canUp = ' + resp + ';');

    if(canUp) {
        var fId = args['fId'];

        var fdiv = $('#feat' + fId);
	//var nowOrder = $('#order' + fId);
	//var nowOrder = $("#feat" + fId + "> input[name='forder[]']");
	//alert(nowOrder.name);
        var prevFdiv = fdiv.prev();
        var newFdiv = fdiv.clone(true);

        newFdiv.css('display', 'none');

        fdiv.fadeOut('slow', function() {
            fdiv.remove();
            newFdiv.insertBefore(prevFdiv);
            newFdiv.fadeIn('slow');
        });
    } else {
        alert('This feature can not be moved up!');
    }
}

function downFeature(pId, fId, lang) {
    ajax.get(admin_dir+'/products/featuredown/pid/' + pId + '/fid/' + fId + '/lang/' + lang + '/', {'func': 'downFeatureSucc', 'args': {'fId' : fId}});
}

function downFeatureSucc(resp, args) {
    eval('var canDown = ' + resp + ';');

    if(canDown) {
	var fId = args['fId'];

        var fdiv = $('#feat' + fId);
        var nextFdiv = fdiv.next();
        var newFdiv = fdiv.clone(true);

        newFdiv.css('display', 'none');

        fdiv.fadeOut('slow', function() {
	    fdiv.remove();
            newFdiv.insertAfter(nextFdiv);
            newFdiv.fadeIn('slow');
        });
    } else {
        alert('This feature can not be moved down!');
    }
}