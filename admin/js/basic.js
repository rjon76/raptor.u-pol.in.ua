/*
 Устоновить куку
*/

function setCookie(cName, value) {
    var exdate = new Date();
    exdate.setDate(365);
    document.cookie = cName + "=" + escape(value)+ ";path=/" + ";expires=" + exdate + ";domain=" + location.host;
}

/*
function setCookie(name, value, days) {
        
            if(days == "null" || days === null){
                days = 365;
            }
            
	        var date = new Date();
	        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
	        var expires = "; expires=" + date.toGMTString();

	    document.cookie = name + "=" + value + expires + "; path=/" + ";domain=" + location.host;
	}
 */
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
    ajax.get(admin_dir+'/pages/hidden/id/'+pageId+'/', {'func': 'hideUnhidePageSucc', 'args': elem});
}
function hideUnhidePageSucc($hidden, elem) {
    elem.src = admin_dir+'/images/' + ($hidden == '1' ? '' : 'un') + 'checked.gif';
}

/*
 Мнеяем статус "hidden" для странички
*/
function setCacheablePage(pageId, elem) {
    ajax.get(admin_dir+'/pages/cacheable/id/'+pageId+'/', {'func': 'setCacheablePageSucc', 'args': elem});
}
function setCacheablePageSucc($cacheable, elem) {
    elem.src = admin_dir+'/images/' + ($cacheable == '1' ? '' : 'un') + 'checked.gif';
}

/*
 Удаляем страницу
*/
function deletePage(pageId) {
    if(confirm('Do You really want to delete this page?')) {
        ajax.get(admin_dir+'/pages/delete/id/' + pageId + '/', {'func': 'deletePageSucc', 'args': pageId});
    }
}

function deletePageSucc(resp, pageId) {
    $('#row_' + pageId).remove();
    $('#hint').html('Page has been deleted!');
    $('#hint').fadeIn('slow');
}
/*
 Удаляем log страницы
*/
function clearLogsPages(form) {
    if(confirm('Do You really want to delete logs for this pages?')) {
        var postdata = $(form).formSerialize();
        ajax.post(admin_dir+'/pages/logsel/', postdata, {'func': 'clearLogsPagesSucc', 'args': form});
    }
}

function clearLogsPagesSucc(resp,form) {

    var ids = [];
    for(var i = 0; i < form.elements.length; i++) {
        var e = form.elements[i];

        if(typeof(e.name) != 'undefined' && e.name == 'chx[]') {
            ids[i] = e.value;
            e.checked = false;

        }
    }

    for(var i in ids) {
        $('#row_' + ids[i]).find('.info-link').remove();
        $('#row_' + ids[i]).next('.info-row' + ids[i]).remove();
    }

    $('#hint').html('Logs for Pages has been cleared!');
    $('#hint').fadeIn('slow');
}
/*
 Удаляем страници
*/
function deletePages(form) {
    if(confirm('Do You really want to delete this pages?')) {
        var postdata = $(form).formSerialize();
        ajax.post(admin_dir+'/pages/deletesel/', postdata, {'func': 'deletePagesSucc', 'args': form});
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
        ajax.post(admin_dir+'/pages/cachesel/', postdata, {'func': 'cachePagesSucc', 'args': form});
    }
    else {
        clearActions(form);
    }
}
function cachePage(id) {
	    $('#hint').fadeOut('fast');
    if(confirm('Do You really want to recache this page?')) {
        $('body').css('cursor', 'wait');
		var postdata = 'chx[]='+id;
        ajax.post(admin_dir+'/pages/cachesel/', postdata, {'func': 'cachePageSucc', 'args': id});
    }
}

function cachePageSucc(resp, id) {
 //   alert(resp);
    if('0' == resp) {
        $('#hint').html('Some errors occured while recaching!');
    }
    else {
        $('#hint').html('Pages has been recached!');
    }
    $('#hint').fadeIn('slow');

    $('body').css('cursor', 'default');
}

function cachePagesSucc(resp, form) {
 //   alert(resp);
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
function clearCachePages(form) {
    if(confirm('Do You really want to clear cache this pages?')) {
        $('body').css('cursor', 'wait');
        var postdata = $(form).formSerialize();
        ajax.post(admin_dir+'/pages/cacheclear/', postdata, {'func': 'clearCachePagesSucc', 'args': form});
    }
    else {
        clearActions(form);
    }
}
function clearCachePage(id) {
    $('#hint').fadeOut('fast');
    if(confirm('Do You really want to clear cache this page?')) {
        $('body').css('cursor', 'wait');
		var postdata = 'chx[]='+id;
		ajax.post(admin_dir+'/pages/cacheclear/', postdata, {'func': 'clearCachePageSucc', 'args': id});
    }
}
function clearCachePageSucc(resp, id) {
    if('0' == resp) {
        $('#hint').html('Some errors occured while clear caching!');
    }
    else {
        $('#hint').html('Cache pages has been cleared!');
    }
    $('#hint').fadeIn('slow');

    $('body').css('cursor', 'default');
}

function clearCachePagesSucc(resp, form) {
    if('0' == resp) {
        $('#hint').html('Some errors occured while clear caching!');
    }
    else {
        $('#hint').html('Cache pages has been cleared!');
    }
    $('#hint').fadeIn('slow');

    clearActions(form);
    $('body').css('cursor', 'default');
}
function exportPages(form) {
    if(confirm('Do You really want to export this pages?')) {
        $('body').css('cursor', 'wait');
        var postdata = $(form).formSerialize();
        ajax.post(admin_dir+'/pages/export/', postdata, {'func': 'exportPagesSucc', 'args': form});
    }
    else {
        clearActions(form);
    }
}
function exportPagesSucc(resp, form) {
 //   alert(resp);
    if('0' == resp) {
        $('#hint').html('Some errors occured while recaching!');
    }
    else {
        $('#hint').html('Pages has been export!');
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
	else if(act.options[act.selectedIndex].value == 3) {
        clearCachePages(form);
    }
   	else if(act.options[act.selectedIndex].value == 4) {
        exportPages(form);
    }
   	else if(act.options[act.selectedIndex].value == 5) {
        clearLogsPages(form);
    }
}

/*
 Меняем значение айдишника сайта в куках и обновляем страницу
*/
function setSite(siteId) {
    setCookie('cur_site_id', siteId);
    window.location = admin_dir+'/pages/list/';
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
    ajax.get(admin_dir+'/pages/instantcache/id/'+pageId+'/', {'func': 'setInstantCacheSucc', 'args': elem});
}
function setInstantCacheSucc($instantcache, elem) {
    elem.src = admin_dir+'/images/' + ($instantcache == '1' ? '' : 'un') + 'checked.gif';
}
function get_html_translation_table (table, quote_style) {
    // +      input by: Ratheous
    // %          note: It has been decided that we're not going to add global
    // %          note: dependencies to php.js, meaning the constants are not
    // %          note: real constants, but strings instead. Integers are also supported if someone
    // %          note: chooses to create the constants themselves.
    // *     example 1: get_html_translation_table('HTML_SPECIALCHARS');
    // *     returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}
    
    var entities = {}, hash_map = {}, decimal = 0, symbol = '';
    var constMappingTable = {}, constMappingQuoteStyle = {};
    var useTable = {}, useQuoteStyle = {};
    
    // Translate arguments
    constMappingTable[0]      = 'HTML_SPECIALCHARS';
    constMappingTable[1]      = 'HTML_ENTITIES';
    constMappingQuoteStyle[0] = 'ENT_NOQUOTES';
    constMappingQuoteStyle[2] = 'ENT_COMPAT';
    constMappingQuoteStyle[3] = 'ENT_QUOTES';

    useTable       = !isNaN(table) ? constMappingTable[table] : table ? table.toUpperCase() : 'HTML_SPECIALCHARS';
    useQuoteStyle = !isNaN(quote_style) ? constMappingQuoteStyle[quote_style] : quote_style ? quote_style.toUpperCase() : 'ENT_COMPAT';

    if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
        throw new Error("Table: "+useTable+' not supported');
        // return false;
    }

    entities['38'] = '&amp;';
  /*  if (useTable === 'HTML_ENTITIES') {
        entities['160'] = '&nbsp;';
        entities['161'] = '&iexcl;';
        entities['162'] = '&cent;';
        entities['163'] = '&pound;';
        entities['164'] = '&curren;';
        entities['165'] = '&yen;';
        entities['166'] = '&brvbar;';
        entities['167'] = '&sect;';
        entities['168'] = '&uml;';
        entities['169'] = '&copy;';
        entities['170'] = '&ordf;';
        entities['171'] = '&laquo;';
        entities['172'] = '&not;';
        entities['173'] = '&shy;';
        entities['174'] = '&reg;';
        entities['175'] = '&macr;';
        entities['176'] = '&deg;';
        entities['177'] = '&plusmn;';
        entities['178'] = '&sup2;';
        entities['179'] = '&sup3;';
        entities['180'] = '&acute;';
        entities['181'] = '&micro;';
        entities['182'] = '&para;';
        entities['183'] = '&middot;';
        entities['184'] = '&cedil;';
        entities['185'] = '&sup1;';
        entities['186'] = '&ordm;';
        entities['187'] = '&raquo;';
        entities['188'] = '&frac14;';
        entities['189'] = '&frac12;';
        entities['190'] = '&frac34;';
        entities['191'] = '&iquest;';
        entities['192'] = '&Agrave;';
        entities['193'] = '&Aacute;';
        entities['194'] = '&Acirc;';
        entities['195'] = '&Atilde;';
        entities['196'] = '&Auml;';
        entities['197'] = '&Aring;';
        entities['198'] = '&AElig;';
        entities['199'] = '&Ccedil;';
        entities['200'] = '&Egrave;';
        entities['201'] = '&Eacute;';
        entities['202'] = '&Ecirc;';
        entities['203'] = '&Euml;';
        entities['204'] = '&Igrave;';
        entities['205'] = '&Iacute;';
        entities['206'] = '&Icirc;';
        entities['207'] = '&Iuml;';
        entities['208'] = '&ETH;';
        entities['209'] = '&Ntilde;';
        entities['210'] = '&Ograve;';
        entities['211'] = '&Oacute;';
        entities['212'] = '&Ocirc;';
        entities['213'] = '&Otilde;';
        entities['214'] = '&Ouml;';
        entities['215'] = '&times;';
        entities['216'] = '&Oslash;';
        entities['217'] = '&Ugrave;';
        entities['218'] = '&Uacute;';
        entities['219'] = '&Ucirc;';
        entities['220'] = '&Uuml;';
        entities['221'] = '&Yacute;';
        entities['222'] = '&THORN;';
        entities['223'] = '&szlig;';
        entities['224'] = '&agrave;';
        entities['225'] = '&aacute;';
        entities['226'] = '&acirc;';
        entities['227'] = '&atilde;';
        entities['228'] = '&auml;';
        entities['229'] = '&aring;';
        entities['230'] = '&aelig;';
        entities['231'] = '&ccedil;';
        entities['232'] = '&egrave;';
        entities['233'] = '&eacute;';
        entities['234'] = '&ecirc;';
        entities['235'] = '&euml;';
        entities['236'] = '&igrave;';
        entities['237'] = '&iacute;';
        entities['238'] = '&icirc;';
        entities['239'] = '&iuml;';
        entities['240'] = '&eth;';
        entities['241'] = '&ntilde;';
        entities['242'] = '&ograve;';
        entities['243'] = '&oacute;';
        entities['244'] = '&ocirc;';
        entities['245'] = '&otilde;';
        entities['246'] = '&ouml;';
        entities['247'] = '&divide;';
        entities['248'] = '&oslash;';
        entities['249'] = '&ugrave;';
        entities['250'] = '&uacute;';
        entities['251'] = '&ucirc;';
        entities['252'] = '&uuml;';
        entities['253'] = '&yacute;';
        entities['254'] = '&thorn;';
        entities['255'] = '&yuml;';
    }
*/
/*    if (useQuoteStyle !== 'ENT_NOQUOTES') {
        entities['34'] = '&quot;';
    }
    if (useQuoteStyle === 'ENT_QUOTES') {
        entities['39'] = '&#39;';
    }
    entities['60'] = '&lt;';
    entities['62'] = '&gt;';
*/

    // ascii decimals to real symbols
    for (decimal in entities) {
        symbol = String.fromCharCode(decimal);
        hash_map[symbol] = entities[decimal];
    }
    
    return hash_map;
}
function htmlentities (string, quote_style) {
    // -    depends on: get_html_translation_table
    // *     example 1: htmlentities('Kevin & van Zonneveld');
    // *     returns 1: 'Kevin &amp; van Zonneveld'
    // *     example 2: htmlentities("foo'bar","ENT_QUOTES");
    // *     returns 2: 'foo&#039;bar'

    var hash_map = {}, symbol = '', tmp_str = '', entity = '';
    tmp_str = string.toString();
    
    if (false === (hash_map = this.get_html_translation_table('HTML_ENTITIES', quote_style))) {
        return false;
    }
    hash_map["'"] = '&#039;';
    for (symbol in hash_map) {
        entity = hash_map[symbol];
        tmp_str = tmp_str.split(symbol).join(entity);
    }
    
    return tmp_str;
}
/*-----------------------*/
function htmlentitiesall(s){	
	var div = document.createElement('div');
	var text = document.createTextNode(s);
	div.appendChild(text);
	return div.innerHTML;
}

function importCorrect(type, Id, _default) {
    if (_default == null)
        _default = '';
        
	ajax.post( admin_dir+'/pages/importcorrect/?' + type + '_id=' + Id + '&default=' + _default, {  'load_file': $("input[name=load_file]").val() }, { 'func': 'importCorrectSucc', 'args': {'type': type,'Id' : Id,'_default' : _default}} );
	return false;
}

function importCorrectSucc(resp, args) {
	if(resp == '[]') {
		alert('Block type is empty!');
		return;
	}
	eval('var data= ' + resp + ';');
	var type = args['type'];
	var Id = args['Id'];
    var _default = args['_default'];
    
    if (data) 
    {
        
        if (type == 'ls')
        {
            $("td."+type+"_"+Id+_default).html("Fixed");
            $("td."+type+"_"+Id+_default).parent('tr').addClass('fixed');
        }
        else
        {
            $("#"+type+"_"+Id).html("Fixed");
            $("#"+type+"_"+Id).parent('tr').addClass('fixed');
        }

	} else 
    {
        if (type=='ls')
        {
            $("td."+type+"_"+Id+_default).html("Something wrong");
            $("td."+type+"_"+Id+_default).parent('tr').addClass('wrong');
        }
        else
        {
            $("#"+type+"_"+Id).html("Something wrong");
            $("#"+type+"_"+Id).parent('tr').addClass('wrong');
        }
		
	}	
}

if (window.jQuery) {
$(document).ready(function() {

        $(".toogle-area").each(function(){
        
                $('.toggle-click', $(this)).bind('click', function () {
                
                    var toogleBlock = $('#' + $(this).attr('data-toogle'));
                
            		if (toogleBlock.is(":visible")){  
            		  $('#item').html('+');
            			toogleBlock.hide();
                        $(this).removeClass('open');
            		}
                    else{
                        $('#item').html('-');
            			toogleBlock.show();
                        $(this).addClass('open');
            		}
                    
                    return false;
    
    	       });
        });

        (function($){
                var toogles = $('.toogleShow');
                
                if (toogles.length > 0)
                {
                    
                    toogles.each(function(){
        
        
                            $(this).bind('click', function()
                            {
                            
                                    var block = $('#'+$(this).attr('data-toogle-id'));
                                    
                                    if (block !== undefined)
                                    {
                                        
                                        if (block.is(':visible'))
                                        {
                                           block.fadeOut('fast'); 
                                           $(this).attr('title','click to show');
                                        }
                                        else{
                                            block.fadeIn('fast'); 
                                            $(this).attr('title','click to hide');
                                        }
                                        
                                    }
                                    
                                    return false;                       
                               
                                
                                
                            });
        
                    });
        
                }
                
        })(jQuery);
        
    // /pages/list/ logs action at page content
    (function($){
        var toogles = $('.row .info-link');
        
        if (toogles.length > 0)
        {
            toogles.each(function(){

                var pid = $(this).attr('data-page'); var parent = $(this).parent().parent('tr'); var link = $(this); 
                            
                $(this).bind('click',function(){
                    var rowInfo = $('.info-row'+pid);
                    if(rowInfo.length>0)
                    {
                        if (rowInfo.is(':visible'))
                        {
                            rowInfo.fadeOut("fast");
                            link.removeClass('active');
                            parent.removeClass('active'); 
                        }
                        else
                        {
                            rowInfo.fadeIn("fast");
                                                              
                            toogles.filter('.active').removeClass('active');
                            link.addClass('active');
                                                            
                            $('.row.active').removeClass('active');
                            parent.addClass('active'); 
                        }
                                                    
                    }
                    else
                    {
                                                    
                        $.ajax({
                            url: admin_dir+'/pages/pagelogs/',
                            data:{'pid':pid},
                            type: "GET",
                            success: function(response) {

                                if (response)
                                {
                                    var row = $("#row_"+pid);
                                    if (row.length>0)
                                    {
                                        row.after(response);
                                    }
                                    
                                    toogles.filter('.active').removeClass('active');
                                    link.addClass('active');
                                                                                
                                    $('.row.active').removeClass('active');
                                    parent.addClass('active'); 
                                }
                            }
                            
                        });


                    }
                                        
                });
            });
        }      
    })(jQuery);
        
        // get screen for page
        // /pages/edit/id/
        getScreenPage = function(pid,type){
            
            var _image = $('#screenPage'+pid);
            
            if (_image.length>0){
                _image.attr('src',admin_dir+'/images/loading.gif');
            }

            $.ajax({
                url: admin_dir+'/pages/pagescreen/',
                data:{'pid':pid,'type':type},
                type: "GET",
                success: function(response) {
    
                        if (response && response !="")
                        {
                            
                            if (_image.length>0)
                            {
                               _image.attr('src',response).fadeIn("fast"); 
                            }
                            
                        }
                    }
                                
            });
        };

        /* function for select & unselect in <select> */
        (function ($) {       
            var inputSearch     = $("#inputSearch");
            var btnSearch       = $("#btnSearch");
            var _options        = $("#selectMselect option");
            var _select         = $("#selectMselect");
            var checkSearch     = $('#checkSearch');  
            
           	btnSearch.bind("click",function(){
           	    
                var bool_checkUnselect = $('#checkUnselect').is(':checked');
                var bool_checkLike = $('#checkLike').is(':checked')
                
                var _search = inputSearch.val();
                
                if (_search != '')
                {
                    _options.each(function(){

                    if ($(this).text() == _search){
                            
                            if (bool_checkUnselect)
                            {
                                $(this).removeAttr('selected');
                            }
                            else
                            {
                                $(this).attr('selected','selected');
                            }
                            
                    }

                    if (checkSearch.is(':checked'))
                    {
                        if (_search != '.')
                        {
                            
                            var re = new RegExp('^' + _search + '', 'i');
                            
                            if (bool_checkLike)
                            {
                                re = new RegExp('' + _search + '', 'i');
                            }
                  	   
                            if (re.test($(this).text()))
                            { 
                                if (bool_checkUnselect)
                                {
                                    $(this).removeAttr('selected');
                                }
                                else
                                {
                                    $(this).attr('selected','selected');
                                }
                                    
                            }
                        }
                               
                        
                    }    
                        

                    });
                }
        	});
        })(jQuery);
        
        // function for Relative pages select
        // /pages/edit/id/
        
        (function ($) { 
            
            var _source         = $("#relativePagesSource");
            var _target         = $("#relativePagesTarget");
            
            $('form').submit(function(){
                
                _target.find('option').attr('selected','selected');
                
            });
            
            //selected all location pages
            $('form #selectLocation').bind('click', function(){
                var _search = String($("form input[name=address]").val());
                var _searchOrig = _search;
                var _lang = $("form select[name=lang] option:selected").text();
                
                if (_lang != "en")
                {
                    _search = _search.substring(3); 
                }

                if (_search != '')
                {
                            $('option',_source).each(function(){
                             
                             var $option = $(this);
                             
                             $('form select[name=lang]').find('option').each(function(){
                                
                                var optionStr = String($option.text());
                                
                                if ($(this).text() == "en"){
                                    var langStr = _search; 
                                }
                                else{
                                    var langStr = '/'+$(this).text()+_search;
                                }
                                
                                if (langStr == optionStr && optionStr != _searchOrig){
                                    _target.append($option);
                                }   
                            });
                            });


                }
                
                return false;
                
            }); 
            
            $("#relativePagesTarget").bind('click', function(){
            
                            _options = $('option',$(this));
                            _options.unbind();
                            _options.dblclick(function(){
                                
                                var _class =  $(this).attr('class');
                                
                                var _opt = $('option[class*='+_class+']',_source);
                                
                                if (_opt.length>0)
                                {
                                    _opt.last().after($(this));
                                }
                                else{
                                    _source.append($(this));
                                }
                                
                            });

            });
            
            $("#relativePagesSource").bind('click', function(){
                
                            _options = $('option',$(this));
                            _options.unbind();
                            _options.dblclick(function(){
                                
                                var _class = $(this).attr('class');
    
                                var _opt = $('option[class*='+_class+']',_target);
                                
                                if (_opt.length>0)
                                {
                                    _opt.last().after($(this));
                                }
                                else{
                                    _target.append($(this));
                                }
                                
                            });


                    });
            
        })(jQuery);
        
});
}