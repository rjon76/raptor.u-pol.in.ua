function editPrice(licenseId, currencyId, element) {

    var elems = drawTextInput(element);

    elems.yes.bind('click', function() {
        ajax.post(
            admin_dir+'/purchase/ajaxeditprice/lid/' + licenseId + '/cid/' + currencyId + '/',
            'value=' + encodeURIComponent(elems.input.val()),
            {'func': 'editPriceSucc', 'args': {'a': $(element), 'td': elems.input.parent()}}
        );
    });
}
function editPriceSucc(newPrice, args) {

    var td = args.td;
    var a = args.a;

    a.html(newPrice);
    td.empty();
    td.append(a);
}

function editBundlePrice(priceId, element) {

    var elems = drawTextInput(element);

    elems.yes.bind('click', function() {
        ajax.post(
            admin_dir+'/purchase/ajaxeditbundleprice/id/' + priceId  + '/',
            'value=' + encodeURIComponent(elems.input.val()),
            {'func': 'editBundlePriceSucc', 'args': {'a': $(element), 'td': elems.input.parent()}}
        );
    });
}
function editBundlePriceSucc(newPrice, args) {

    var a = args.a;
    var td = args.td;

    a.html(newPrice);
    td.empty();
    td.append(a);
}

function editOfferPrice(priceId, element) {
    var elems = drawTextInput(element);

    elems.yes.bind('click', function() {
        ajax.post(
            admin_dir+'/purchase/ajaxeditofferprice/id/' + priceId  + '/',
            'value=' + encodeURIComponent(elems.input.val()),
            {'func': 'editOfferPriceSucc', 'args': {'a': $(element), 'td': elems.input.parent()}}
        );
    });
}
function editOfferPriceSucc(newPrice, args) {
    var a = args.a;
    var td = args.td;

    a.html(newPrice);
    td.empty();
    td.append(a);
}


function drawTextInput(element) {
    var innerHTML = $(element).html();
    var input = $(document.createElement('input'));
    var parent = $(element).parent();
    var storage = parent.html();

    input.attr('type', 'text');
    input.attr('class', 'textSmall');
    input.css('width', 80);
    input.css('margin', 0);
    input.val(innerHTML);

    var yes = $(document.createElement('a'));
    var no = $(document.createElement('a'));

    yes.html('yes');
    yes.css('font-size', '10px');
    yes.css('color', 'red');
    yes.attr('href', 'javascript:void(0)');
    no.html('no');
    no.css('font-size', '10px');
    no.css('color', 'blue');
    no.attr('href', 'javascript:void(0)');

    no.bind('click', function() {
        parent.html(storage);
    });

    parent.append(input);
    parent.append('<br/>');
    parent.append(yes);
    parent.append('/');
    parent.append(no);
    $(element).remove();

    return {'yes': yes,
            'no': no,
            'input': input};
}


function couponSortSubmit(by,fid){
	$("#by").val(by);
	$("#"+fid).submit();
}

function blockCoupon(cupId,elem) {
	if($(elem).attr('src') == admin_dir+'/images/unchecked.gif')
	{action = 'Y';}else
	{action = 'N';}
    ajax.get(admin_dir+'/purchase/ajaxblockcoupon/coupid/'+cupId+'/act/'+action+'/', {'func': 'blockUnblockCoupSucc', 'args': elem});
}

function blockUnblockCoupSucc(resoponse, elem) {
	src = admin_dir+'/images/' + (resoponse == '0' ? '' : 'un') + 'checked.gif';
	elem.src = src;
}

