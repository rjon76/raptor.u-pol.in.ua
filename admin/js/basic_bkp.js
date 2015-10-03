/*
 Устоновить куку
*/
function setCookie(cName, value) {
    var exdate = new Date();
    exdate.setDate(365);
    document.cookie = cName + "=" + escape(value)+ ";path=/" + ";expires=" + exdate + ";domain=" + location.host;
}

/*
 Устоновить текущий сайт
*/
function setCurSite(siteId) {
    setCookie('cur_site_id', siteId);
    window.location.href = '/';
}

/*
 Замена состояния набора чекбоксов на противоположный
*/
var xchecked='';
function CheckUncheckAll(block, xform) {

    if(xchecked != '') {
        xchecked = ''
    } else {
        xchecked = 'checked'
    }

    for(var i = 0; i < xform.elements.length; i++) {
        var e = xform.elements[i];

        if(typeof(e.name) != 'undefined' && e.name == block) {

            e.checked=xchecked;
        }
    }
    return false;
}

/*
 Мнеяем статус "hidden" для странички
*/
function hideUnhidePage(pageId, elem) {
    ajax.get('/pages/hidden/id/'+pageId+'/', {'func': 'hideUnhidePageSucc', 'args': elem});
}
function hideUnhidePageSucc($hidden, elem) {
    elem.src = '/images/' + ($hidden == '1' ? '' : 'un') + 'checked.gif';
}

/*
 Мнеяем статус "hidden" для странички
*/
function setCacheablePage(pageId, elem) {
    ajax.get('/pages/cacheable/id/'+pageId+'/', {'func': 'setCacheablePageSucc', 'args': elem});
}
function setCacheablePageSucc($cacheable, elem) {
    elem.src = '/images/' + ($cacheable == '1' ? '' : 'un') + 'checked.gif';
}

/*
 Удаляем страницу
*/
function deletePage(pageId) {
    if(confirm('Do You really want to delete this page?')) {
        ajax.get('/pages/delete/id/' + pageId + '/', {'func': 'deletePageSucc', 'args': pageId});
    }
}

function deletePageSucc(resp, pageId) {
    $('#row_' + pageId).remove();
    $('#hint').html('Page has been deleted!');
    $('#hint').fadeIn('slow');
}

/*
 Удаляем страници
*/
function deletePages(form) {
    if(confirm('Do You really want to delete this pages?')) {
        var postdata = $(form).formSerialize();
        ajax.post('/pages/deletesel/', postdata, {'func': 'deletePagesSucc', 'args': form});
    }
}

function deletePagesSucc(resp, form) {

    var ids = [];
    for(var i = 0; i < form.elements.length; i++) {
        var e = form.elements[i];

        if(typeof(e.name) != 'undefined' && e.name == 'chx[]') {
            ids[i] = e.value;

        }
    }

    for(var i in ids) {
        $('#row_' + ids[i]).remove();
    }

    $('#hint').html('Pages has been deleted!');
    $('#hint').fadeIn('slow');
}

/*
 Кешируем страници
*/
function cachePages(form) {
    if(confirm('Do You really want to recache this pages?')) {
        $('body').css('cursor', 'wait');
        var postdata = $(form).formSerialize();
        ajax.post('/pages/cachesel/', postdata, {'func': 'cachePagesSucc', 'args': form});
    }
    else {
        clearActions(form);
    }
}

function cachePagesSucc(resp, form) {

    alert(resp);

    if('0' == resp) {
        $('#hint').html('Some errors occured while recaching!');
    }
    else {
        $('#hint').html('Pages has been recached!');
    }
    $('#hint').fadeIn('slow');

    clearActions(form);
    $('body').css('cursor', 'default');
}

function withselected(form, name) {
    $('#hint').fadeOut('fast');
    var act = document.getElementById(name);

    if(act.options[act.selectedIndex].value == 1) {
        deletePages(form);
    } else if(act.options[act.selectedIndex].value == 2) {
        cachePages(form);
    }
}

/*
 Меняем значение айдишника сайта в куках и обновляем страницу
*/
function setSite(siteId) {
    setCookie('cur_site_id', siteId);
    window.location = '/pages/list/';
}

function clearActions(form) {
    for(var i = 0; i < form.elements.length; i++) {
        var e = form.elements[i];
        if(typeof(e.name) != 'undefined' && e.name == 'chx[]') {
            e.checked = '';
        }
    }
    var act = document.getElementById('act1');
    act.selectedIndex = 0;
    var act = document.getElementById('act');
    act.selectedIndex = 0;
}

function setInstantCache(pageId, elem) {
    ajax.get('/pages/instantcache/id/'+pageId+'/', {'func': 'setInstantCacheSucc', 'args': elem});
}
function setInstantCacheSucc($instantcache, elem) {
    elem.src = '/images/' + ($instantcache == '1' ? '' : 'un') + 'checked.gif';
}