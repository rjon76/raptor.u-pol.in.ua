//italiano, 21/09/2015
var $182 = jQuery.noConflict(); 

jQuery(function() {

			//$('fieldset.draggeble').draggable({				
			$182('fieldset.draggeble legend').draggable({
				axis: 'y' ,
				opacity: 0.35,
				snap: true,
				helper: 'clone',
				cursor: 'move',
				snapMode : 'inner',
				//containment: 'parent'
			});

			// let the trash be droppable, accepting the gallery items
//			$('fieldset.draggeble').droppable({
			$182('fieldset.block legend').droppable({				
				//accept: 'fieldset.block',
				accept: 'fieldset.draggeble legend',				
				//activeClass: 'ui-state-highlight',
				tolerance: 'pointer',
				addClasses: false,
				greedy: true,
				hoverClass: 'ui-state-highlight',
				drop: function(ev, ui) {
					updateBlocks($(this).parent(), ui.draggable);
				}
			});	
	
});
function updateBlocks($parent, $item) {
	var bpId = $item.parent().attr('value');
	var $list = $parent.length ? $parent : $('#fs0');
	var bp_parent = $list.attr('value');

	 ajax.post(admin_dir+'/content/updateblock2page/pid/' + pageId + '/bpid/' + bpId , {'bp_parent':bp_parent}, {'func': 'updateBlocksSucc', 'args': {'bpId': bpId, 'bp_parent': bp_parent}});
}

function updateBlocksSucc(resp, args) {

    eval('var respData = ' + resp + ';');

    var bpId = respData['bpid'];
    var pageId = respData['pageid'];
    var blockId = respData['blockid'];
    var bp_parent = respData['bp_parent'];	
    var blockName = respData['blockname'];
    var fieldsExist = respData['fieldsexist'];
    var childsExist = respData['childsexist'];
    var order = respData['order'];
	
    var parent = $('#fs'+bp_parent);
    var fieldset = $('#fs'+bpId);	
	var table = $('#tb' + bpId);
    var imgEdit   = table.find('.editblock:first');
	var legend = fieldset.find('legend:first');
	legend.html("<a href='"+admin_dir+"/content/editblock/id/"+blockId+"/' class='' title='Edit block  - "+blockName+"'>"+blockName+" ID - "+order+"</a>");
	imgEdit.attr("onClick", "editBlock(\'fs"+bpId+"\', "+pageId+", "+bpId+", " + blockId + ", " + bp_parent + ")");

	
	fieldset.fadeOut(function() {
							fieldset.appendTo(parent).fadeIn();
		});
	
}

// ��������� ����
function addBlock(elemId, pageId, bpId, bid) {
    ajax.get(admin_dir+'/content/getblocks/pid/' + pageId + '/bpid/' + bpId + '/bid/' + bid + '/', {'func': 'addBlockSucc', 'args': {'elemId': elemId, 'pageId' : pageId, 'bpId' : bpId}});
}
function addBlockSucc(resp, args) {
    eval('var respData = ' + resp + ';');

    var params = respData['params'];
    var blocksList = respData['blocks'];

    var elemId = args['elemId'];
    var pageId = args['pageId'];
    var bpId = args['bpId'];

    var fieldset = $('#' + elemId);

    var newFieldset = $(document.createElement('fieldset'));
    var newLegend = $(document.createElement('legend'));
    var select = $(document.createElement('select'));
    var submit = $(document.createElement('button'));
    var img = $(document.createElement('img'));
	

    newFieldset.attr('class', 'block form-inline');
    newFieldset.css('textAlign', 'center');
    newLegend.html('New block');

	

    select.attr('class', 'form-control');
    select.attr('name', 'block');
	

    submit.attr('class', 'btn btn-primary');
    submit.html('Add');

    img.attr('src', admin_dir+'/images/undo.gif');
    img.attr('class', 'pointer');
    img.css('margin', '0 0 0 10px');


    var optgroupChild = $(document.createElement('optgroup'));
    optgroupChild.attr('label','Child blocks');
    var optgroupAnother = $(document.createElement('optgroup'));
    optgroupAnother.attr('label','Another blocks');
    
    for(var i in blocksList) {
        
        var option = $(document.createElement('option'));
        
        option.val(i);
        option.html(blocksList[i]['b_name']);
        
        if (blocksList[i]['child'] === true)
        {
            optgroupChild.append(option);
        }
        else
        {
            optgroupAnother.append(option);
        }

    }
    
    if (optgroupChild.find('option').length>0)
    {
        select.append(optgroupChild);
    }
    
    select.append(optgroupAnother); 
    
    submit.bind('click', function() {
        var postdata = select.fieldSerialize();
        ajax.post(admin_dir+'/content/addblock2page/pid/' + pageId + '/bpid/' + bpId + '/', postdata, {'func': 'addBlockSuccSucc', 'args': {'fs': newFieldset, 'lg': newLegend}});
        select.remove();
        submit.remove();
        img.remove();
    });

    img.bind('click', function() {
        newFieldset.remove();
    });

    newLegend.appendTo(newFieldset);
    select.appendTo(newFieldset);
    submit.appendTo(newFieldset);
    img.appendTo(newFieldset);
    newFieldset.appendTo(fieldset);
    jQuery.scrollTo(newFieldset, 400);
}
function addBlockSuccSucc(resp, args) {

    eval('var respData = ' + resp + ';');

    var fieldset = args['fs'];
    var legend = args['lg'];
	
	var bpId = respData['bpid'];
    var pageId = respData['pageid'];
    var blockId = respData['blockid'];
    var bp_parent = respData['bp_parent'];	
    var blockName = respData['blockname'];
    var fieldsExist = respData['fieldsexist'];
    var childsExist = respData['childsexist'];

    fieldset.attr('id', 'fs'+bpId);	
	legend.html("<a href='"+admin_dir+"/content/editblock/id/"+blockId+"/' class='' title='Edit block  - "+blockName+"'>"+blockName+"</a>");

    var imgDelete   = $(document.createElement('img'));
    var imgLift     = $(document.createElement('img'));
    var imgAddBlock = $(document.createElement('img'));
    var imgAddField = $(document.createElement('img'));
    var imgEdit = $(document.createElement('img'));
	
    var table = '';

    table += '<table border="0" cellpadding="0" cellspacing="0" class="blockTable" id="tb' + bpId + '">';
    table += '    <tr>';
    table += '        <th width="140">' + (fieldsExist ? 'Name' : '&nbsp;') + '</th>';
    table += '        <th width="140">' + (fieldsExist ? 'Type' : '&nbsp;') + '</th>';
    table += '        <th colspan="2">';
    table += '            <span style="float:right;width:auto;" class="bctrl">';
    table += '            <img src="'+admin_dir+'/images/addblock.gif" width="16" height="16" title="Add block" alt="Add block" class="pointer" onclick="addBlock(\'fs' + bpId + '\', ' + pageId + ', ' + bpId + ');" />';
    table += '            ' + (fieldsExist ? '<img src="'+admin_dir+'/images/addfield.gif" width="16" height="16" title="Add field" alt="Add field" class="pointer" onclick="addBlockField(\'tb' + bpId + '\', ' + pageId + ', ' + bpId + ');" />' : '');
    table += '            <img src="'+admin_dir+'/images/edit.gif" width="16" height="16" title="Edit block" alt="Edit block" class="editblock pointer" onclick="editBlock(\'fs' + bpId + '\', ' + pageId + ', ' + bpId + ','+ blockId +','+ bp_parent+');" />';
    table += '            <img src="'+admin_dir+'/images/top.gif" width="16" height="16" title="Lift" alt="Lift" class="pointer" onclick="liftBlock(' + bpId + ', ' + pageId + ');" />';
    table += '            <img src="'+admin_dir+'/images/bottom.gif" width="16" height="16" title="Pull down" alt="Pull down" class="pointer" onclick="pullDownBlock(' + bpId + ', ' + pageId + ');" />';
    table += '            <img src="'+admin_dir+'/images/delete.gif" width="16" height="16" title="Delete" alt="Delete" class="pointer" onclick="deleteBlock(' + bpId + ',' + pageId + ' );" />';
    table += '            </span>';
    table += '            ' + (fieldsExist ? 'Value' : '&nbsp;');
    table += '        </th>';
    table += '    </tr>';
    table += '</table>';

    var newTable = $(table);
    fieldset.append(newTable);

}

// ��������� ���������� � �����
function addBlockField(elemId, pageId, bpId) {
    ajax.get(admin_dir+'/content/getblockfields/pid/' + pageId + '/bpid/' + bpId + '/', {'func': 'addBlockFieldSucc', 'args': {'elemId': elemId, 'pageId' : pageId, 'bpId' : bpId}});
}
function addBlockFieldSucc(resp, args) {
    if(resp == '[]') {
        alert('All fields alredy added!');
        return;
    }

    eval('var blockFields = ' + resp + ';');

    var types = {'S': 'String','L': 'String', 'A': 'Array', 'J': 'Json Array', 'G': 'Global Extension', 'I': 'Image', 'E': 'Extension', 'W': 'Flash', 'L': 'List'};

    var elemId = args['elemId'];
    var pageId = args['pageId'];
    var bpId = args['bpId'];

    var table = $('#tb' + bpId);

    var tr = $('<tr></tr>');
    var td = $('<td colspan="3" align="center" style="padding-top:15px;" class="form-inline"></td>');
    var select = $('<select class="form-control" name="field"></select>');
    var button = $('<button class="btn btn-primary">Add</button>');
    var img = $('<img src="'+admin_dir+'/images/undo.gif" width="16" height="16" title="close" alt="close" class="pointer" style="margin-left:10px;"/>');

    for(var i in blockFields) {
        var option = $('<option value="' + blockFields[i]['bf_id'] + '">' + blockFields[i]['bf_name'] + ' (' + types[blockFields[i]['bf_type']] + ')</option>');
        select.append(option);
    }

    button.bind('click', function() {
        var postdata = select.fieldSerialize();
        ajax.post(admin_dir+'/content/addblockfield2page/pid/' + pageId + '/bpid/' + bpId + '/', postdata, {'func': 'addBlockFieldSuccSucc', 'args': {'tr': tr, 'tb': table, 'pageId': pageId}});
    });

    img.bind('click', function() {
        tr.remove();
    });

    td.append(select);
    td.append(button);
    td.append(img);
    tr.append(td);
    table.append(tr);
}
function addBlockFieldSuccSucc(resp, args) {
    eval('var respData = ' + resp + ';');

    var tr = args['tr'];
    var table = args['tb'];
    var pageId = args['pageId'];
    var bdId = respData['bdId'];
    var fieldName = respData['fieldName'];
    var fieldType = respData['fieldType'];
    var types = {'S': 'String', 'L': 'String', 'A': 'Array', 'J': 'Json Array', 'G': 'Global Extension', 'I': 'Image', 'E': 'Extension', 'W': 'Flash', 'L': 'List'};
    tr.attr('id', 'tr' + bdId);

    var newRowHTML = '';
    newRowHTML += '    <td>' + fieldName + '</td>';
    newRowHTML += '    <td>' + types[fieldType] + '</td>';
    newRowHTML += '    <td style="text-align:justify;" id="tdval' + bdId + '">&nbsp;</td>';
    newRowHTML += '    <td class="bctrl " id="tdctrl' + bdId + '">';
    newRowHTML += '        <img src="'+admin_dir+'/images/edit.gif" width="16" height="16" title="Edit" alt="Edit" class="pointer" onclick="editBlockField(' + bdId + ', ' + pageId + ');" />';
    newRowHTML += '        <img src="'+admin_dir+'/images/delete.gif" width="16" height="16" title="Delete" alt="Delete" class="pointer" onclick="deleteBlockField(' + bdId + ', ' + pageId + ');" />';
    newRowHTML += '    </td>';

    var sepHTML = '';
    sepHTML += '<tr id="sep' + bdId + '">';
    sepHTML += '    <td colspan="4" class="sep"></td>';
    sepHTML += '</tr>';

   // var newRow = $(newRowHTML);
    var newSep = $(sepHTML);

  //  tr.html('');
   //  tr.append(newRow);
	 tr.html(newRowHTML);
 
    table.append(newSep);
}


// ����������� ������ ���������� � �����
function editBlockField(bdId, pageId) {
    ajax.get(admin_dir+'/content/getfieldcontent/pid/' + pageId + '/bdid/' + bdId + '/', {'func': 'editBlockFieldSucc', 'args': {'pageId' : pageId, 'bdId' : bdId}});
}
var niceEditCounter = 0;
function editBlockFieldSucc(resp, args) {
    eval('var respData = ' + resp + ';');
    var value = respData['value'];
    var type = respData['type'];

    var ar = false;
    if(type != 'S') {
        ar = true;
    }

    var pageId = args['pageId'];
    var bdId = args['bdId'];

    var tdVal = $('#tdval' + bdId);
    var tdCtrl = $('#tdctrl' + bdId);
    var storageTdVal = tdVal.clone(true);
    var storageTdCtrl = tdCtrl.clone(true);

    var imgUndo = $('<img src="'+admin_dir+'/images/undo.gif" width="16" height="16" alt="Close" title="Close" class="pointer" />');
    var imgSave = $('<img src="'+admin_dir+'/images/save.gif" width="16" height="16" alt="Save" title="Save" class="pointer" style="margin-right:5px;" />');
    
    if(type == 'L'){
        var textarea = $('<select class="form-control col-xs-12" id="txtval' + bdId + '" name="value" size="10" multiple="true">' + value + '</select>');
    }else{
        var textarea = $('<textarea class="form-control col-xs-12" id="txtval' + bdId + '" name="value" style="min-height:250px; width:100%">' + value + '</textarea>');
    }

    if(type == 'I') {
        var imgDir = $('<img src="'+admin_dir+'/images/dir.gif" width="16" height="16" alt="Select folder" title="Select folder" class="pointer" style="margin:5px 0 0 8px;float:left;" />');

        imgDir.bind('click', function() {
            var siteDir = root_dir;
            openBrowser(textarea, siteDir);
        });
    }

    imgUndo.bind('click', function() {
        tdVal.html(storageTdVal.html());
        tdCtrl.html(storageTdCtrl.html());
    });

    imgSave.bind('click', function() {
//        var newValue = ar ? textarea.val() : tinyMCE.get(textarea.attr('id')).getContent();
//        var newValue = ar ? htmlentities(textarea.val()) : tinyMCE.get(textarea.attr('id')).getContent();
        
        if(type == 'L'){
            var newValue = '';	
            
            $('select#txtval'+bdId+' > option').each( function() {
                if($(this).is(':selected'))
                {
                     newValue += $(this).val()+'='+$(this).html()+'&';
                }
               
            });
            newValue = newValue.substring(0, newValue.length-1);
            
        }
        else
        {
            var newValue = ar ?  htmlentities(textarea.val()) : textarea.val();	
        }
        
        	
        ajax.post(admin_dir+'/content/editfieldcontent/pid/' + pageId + '/bdid/' + bdId + '/', 'value=' + encodeURIComponent(newValue), {'func': 'editBlockFieldSuccSucc', 'args': {'storageTdCtrl': storageTdCtrl, 'bdId': bdId}});

    });

    tdVal.html('');
    tdCtrl.html('');

    tdVal.append(textarea); 
    
 /*   if(!ar) {
        tinyMCE.init({
            // General options
            mode 				: "exact",
            elements			: textarea.attr('id'),
            theme 				: "advanced",
            plugins 			: "inlinepopups,advimage,advlink,fullscreen,ibrowser",
//            plugins 			: "inlinepopups,advimage,advlink,fullscreen",
			force_br_newlines 	: true,
            forced_root_block 	: '',
            cleanup 			: true,
			apply_source_formatting : true,

            // Theme options
//            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,undo,redo,|,link,unlink,image,|,sub,sup,|,forecolor,backcolor,|,code,fullscreen",
            theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,bullist,numlist,|,undo,redo,|,link,unlink,image,|,sub,sup,|,forecolor,backcolor,|,code,fullscreen,ibrowser",
			
            theme_advanced_buttons2 : "",
            theme_advanced_buttons3 : "",
            theme_advanced_toolbar_location : "top",
            theme_advanced_toolbar_align : "left",
            theme_advanced_statusbar_location : "bottom",
            theme_advanced_resizing : true,

	});
    }*/
    
    tdCtrl.append(imgSave);
    tdCtrl.append(imgUndo);

    if(type == 'I') {
        tdCtrl.append(imgDir);
    }
}
function editBlockFieldSuccSucc(resp, args) {
    eval('var value = ' + resp + ';');

    var storageTdCtrl = args['storageTdCtrl'];
    var bdId = args['bdId'];

    var tdVal = $('#tdval' + bdId);
    var tdCtrl = $('#tdctrl' + bdId);

    tdVal.html((value == '' ? '&nbsp;' : value));
    tdCtrl.html(storageTdCtrl.html());
}


function deleteBlock(bpId, pageId) {

	if (bpId==undefined || pageId==undefined)
	{
		alert('Block or Page not set!!!');
	}
	else
	{
	    if(confirm('Do You really want to delete this block?')) {
    	   ajax.get(admin_dir+'/content/deletepageblock/pid/' + pageId + '/bpid/' + bpId + '/', {'func': 'deleteBlockSucc', 'args': {'bpId' : bpId}});
	    }
	}
}
function deleteBlockSucc(resp, args) {
	 eval('var value = ' + resp + ';');
	var error = value['error']; 
    var bpId = args['bpId'];
    var fieldset = $('#fs' + bpId);
	if (error.length == 0)
	    fieldset.fadeOut('fast');
	else
		alert('Error deleting!!!');	
}



function liftBlock(bpId, pageId) {
    ajax.get(admin_dir+'/content/liftblock/pid/' + pageId + '/bpid/' + bpId + '/', {'func': 'liftBlockSucc', 'args': {'bpId' : bpId}});
}
function liftBlockSucc(resp, args) {
    eval('var isLifted = ' + resp + ';');

    if(isLifted) {
        var bpId = args['bpId'];

        var fieldset = $('#fs' + bpId);
        var prevFieldset = fieldset.prev();
        var newFieldset = fieldset.clone(true);

        newFieldset.css('display', 'none');

        fieldset.fadeOut('slow', function() {
            fieldset.remove();
            newFieldset.insertBefore(prevFieldset);
            newFieldset.fadeIn('slow');
        });
    } else {
        alert('Block can not be moved up!');
    }
}

// ������� ���� �� ��������
function pullDownBlock(bpId, pageId) {
    ajax.get(admin_dir+'/content/pulldownblock/pid/' + pageId + '/bpid/' + bpId + '/', {'func': 'pullDownSucc', 'args': {'bpId' : bpId}});
}
function pullDownSucc(resp, args) {
    eval('var isPulled = ' + resp + ';');

    if(isPulled) {
        var bpId = args['bpId'];

        var fieldset = $('#fs' + bpId);
        var nextFieldset = fieldset.next();
        var newFieldset = fieldset.clone(true);

        newFieldset.css('display', 'none');

        fieldset.fadeOut('slow', function() {
            fieldset.remove();
            newFieldset.insertAfter(nextFieldset);
            newFieldset.fadeIn('slow');
        });
    } else {
        alert('Block can not be moved down!');
    }
}


function copyBlock(bpId, pageId) {
    if(confirm('Do You really want to copy this block?')) {
		ajax.get(admin_dir+'/content/copyblock/pid/' + pageId + '/bpid/' + bpId + '/', {'func': 'copyBlockSucc', 'args': {'bpId' : bpId}});
	}
}
function copyBlockSucc(resp, args) {

	var bpId = args['bpId'];
	$(resp).replaceAll("fieldset#fs0 > fieldset");

	var jqNeed = $('#fs' + bpId);
	$('html, body').animate({
		scrollTop: jqNeed.next('.block').offset().top
	}, 1000);

}


// ������� ������ ���������� �����
function deleteBlockField(bdId, pageId) {
    if(confirm('Do You really want to delete this field?')) {
        ajax.get(admin_dir+'/content/deleteblockfield/pid/' + pageId + '/bdid/' + bdId + '/', {'func': 'deleteBlockFieldSucc', 'args': {'bdId' : bdId}});
    }
}

function deleteBlockFieldSucc(resp, args) {
    var bdId = args['bdId'];

    var tr = $('#tr' + bdId);
    var sep = $('#sep' + bdId);

    tr.fadeOut('fast', function() {
        tr.remove();
        sep.remove();
    });
}

// ��������� ����
function editBlock(elemId, pageId, bpId) {
    ajax.get(admin_dir+'/content/getblocks/pid/' + pageId + '/bpid/' + bpId + '/', {'func': 'editBlockSucc', 'args': {'elemId': elemId, 'pageId' : pageId, 'bpId' : bpId}});
}

function editBlockSucc(resp, args) {
    if(resp == '[]') {
        alert('Block type is empty!');
        return;
    }

    eval('var data= ' + resp + ';');
	var blockList = data['blocks'];
	var params = data['params'];	
//    eval('var blockList= ' + resp['blocks'] + ';');
   // var types = {'S': 'String','L': 'String', 'A': 'Array', 'J': 'Json Array', 'G': 'Global Extension', 'I': 'Image', 'E': 'Extension', 'W': 'Flash'};

    var elemId = args['elemId'];
    var pageId = args['pageId'];
    var bpId = args['bpId'];
/*    var blockId = args['blockId'];
    var bp_parent = args['bp_parent'];
*/   
	var blockId = params['b_id'];
    var bp_parent = params['bp_parent'];
    var bp_order = params['bp_order'];

    var table = $('#tb' + bpId);

    var tr = $('<tr></tr>');
    var td = $('<td colspan="3" align="center" style="padding-top:15px;"></td>');
    var form = $('<form method="post" name="form'+bpId+'" class="form-inline"></form>');	
    var select = $('<select class="form-control" name="block"></select>');
    var input = $('<input name="order" class="form-control" size="10" />');	
    var button = $('<img src="'+admin_dir+'/images/save.gif" width="16" height="16" title="save" alt="save" class="pointer" style="margin-left:10px;"/>');
    var img = $('<img src="'+admin_dir+'/images/undo.gif" width="16" height="16" title="close" alt="close" class="pointer" style="margin-left:10px;"/>');

	input.val(bp_order);

    for(var i in blockList) {
        var option = $(document.createElement('option'));
        option.val(i);
		if (i == blockId){
			option.attr('selected','selected');
		}
        option.html(blockList[i]['b_name']);
        select.append(option);
    }
	
/*	for(var i in blockFields) {
        var option = $('<option value="' + blockList[i]['bf_id'] + ((blockList[i]['bf_id']==bpId) ? 'selected' : '' ) + '">' + blockList[i]['bf_name'] + ' (' + types[blockFields[i]['bf_type']] + ')</option>');
        select.append(option);
    }*/

    button.bind('click', function() {

//        var postdata = select.fieldSerialize();
        var postdata = form.serialize();		
        ajax.post(admin_dir+'/content/editblock2page/pid/' + pageId + '/bpid/' + bpId + '/bp_parent/' + bp_parent, postdata, {'func': 'editBlockSuccSucc', 'args': {'tr': tr, 'tb': table, 'pageId': pageId}});
        tr.remove();
    });

    img.bind('click', function() {
        tr.remove();
    });
	form.append('<div class="form-group">');
	form.append('<label for="block"> Block type </label>');
    form.append(select);
	form.append('</div>');
	form.append('<div class="form-group">');
	form.append('<label for="order"> Order ID </label>');
	form.append(input);
	form.append('</div>');
    form.append(button);
    form.append(img);
    td.append(form);
    tr.append(td);
//    table.append(tr);
	tr.prependTo(table);
}

function editBlockSuccSucc(resp, args) {

    eval('var respData = ' + resp + ';');

    var bpId = respData['bpid'];
    var pageId = respData['pageid'];
    var blockId = respData['blockid'];
    var bp_parent = respData['bp_parent'];	
    var blockName = respData['blockname'];
    var fieldsExist = respData['fieldsexist'];
    var childsExist = respData['childsexist'];
    var order = respData['order'];
	
    var fieldset = $('#fs'+bpId);	
	var table = $('#tb' + bpId);
    var imgEdit   = table.find('.editblock:first');
	var legend = fieldset.find('legend:first');
	legend.html("<a href='"+admin_dir+"/content/editblock/id/"+blockId+"/' class='' title='Edit block  - "+blockName+"'>"+blockName+" ID - "+order+"</a>");
	imgEdit.attr("onClick", "editBlock(\'fs"+bpId+"\', "+pageId+", "+bpId+ ")");
}


function exportColorbox( bpId, bp_page_id) {
//	alert('todo 11');
	jQuery.fn.colorbox({
		iframe: true,
		innerWidth: '718px',
		innerHeight: '440px',
		speed: 200,
		opacity: 0.8,
		href: admin_dir+'/content/myimport/pid/'+bp_page_id+'/bpid/'+bpId
	});
	
}

function clickHiddenBlock(bpId, pageId) {
    ajax.get(admin_dir+'/content/hiddenblock/pid/' + pageId + '/bpid/' + bpId, {'func': 'succ_clickHiddenBlock', 'args': {'bpId' : bpId}});
    return false;
}
function succ_clickHiddenBlock(resp, args) {
    
    eval('var isPulled = ' + resp + ';');
    
    var bpId = args['bpId'];
    var fieldset = $('#fs' + bpId);
    
    if(isPulled) {
        //fieldset.attr("disabled","disabled");
        fieldset.addClass("disabled").find('fieldset').addClass("disabled");
    } 
    else {
        //fieldset.removeAttr("disabled");
        fieldset.removeClass("disabled").find('fieldset').removeClass("disabled");
    }
}
function clickHiddenField(bdid, pid) {
    ajax.get(admin_dir+'/content/hiddenfield/pid/' + pid + '/bdid/' + bdid, {'func': 'succ_clickHiddenField', 'args': {'bdid' : bdid}});
    return false;
}
function succ_clickHiddenField(resp, args) {

    eval('var isPulled = ' + resp + ';');
    
    var bdid = args['bdid'];
    
    var _tr = $('#tr' + bdid);

    if(isPulled) {
        //fieldset.attr("disabled","disabled");
        _tr.addClass("disabled");
    } 
    else {
        //fieldset.removeAttr("disabled");
        _tr.removeClass("disabled");
    }
    
}
/** 
* function for view overlay witch info about block (text, image/baze64) 
* @author italiano
* @edit 25.12.2014
*/
function clickBlockInfo(bid, pid) {
    
    if ($.fn.colorbox !== undefined)
    {
    	$.fn.colorbox({
    		iframe: true,
    		innerWidth: '640px',
    		innerHeight: '360px',
    		speed: 200,
    		opacity: 0.8,
    		href: admin_dir+'/content/blockinfo/pid/'+pid+'/bid/'+bid
	   });
    }
    return false;
}

$(document).ready(function() {
    
    (function($) {   

        var blocks = $('#fs0 fieldset fieldset');
        
        blocks.each(function(){
            
            var thisId = $(this).attr('id');
            
            if ($(this).children('fieldset').length>0){
                
                var link = $('<a class="toggleFieldset" href="#'+thisId+'" title="Toggle blocks to display">switching the display</a>');
/*
                if ($(this).hasClass('disabled'))
                {
                    $(this).children('fieldset').hide();
                    $(this).children('table').hide()
                }
*/
                $('legend',$(this)).eq(0).after(link);
            }
        });
        
        var links = $('.toggleFieldset');
            
        links.click(function(){
                
                var link = $(this);
                var block = $($(this).attr('href'));
                var table = block.children('table');
                var blocks = block.children('fieldset');
                var text = "";
                link.after('<div class="hiddenBlocks"><strong>Child nodes are hidden:</strong><br/></div>');
                
                blocks.each(function(){
                    
                    if ($(this).is(':hidden')){
                        table.fadeIn("fast");
                        $(this).fadeIn("fast");
                        link.removeClass('toggle');
                        block.children('.hiddenBlocks').remove();
                        
                    }
                    else{
                        table.fadeOut("fast");
                        $(this).fadeOut("fast");
                        link.addClass('toggle');
                        
                        var tag = $(this).children('legend').find('a');
                        text += tag.html()+'<br/>';
                    }
                });
                
                link.next('.hiddenBlocks').append(text);
                
                return false;
            });
        

    })(jQuery);
});
