
$(document).ready(function() {
	/* Build the DataTable with third column using our custom sort functions */
	oTable = $('#lstrlist').dataTable( {
		"aaSorting": [ [0,'asc'] ],
		"aoColumns": [ false,false,false,false,false,false,false,false,false, ]
	} );
});

function editWindow(lng,sid){
	$.getJSON(admin_dir+'/localstring/getstr/lid/' + sid + '/lang/' + lng + '/', {},
		function(json){
    		form = '';
            form += '<div class="panel panel-default"><div class="panel-heading"><a class="pull-right" href="javascript:closeWindow(\'editw\')"><strong>close</strong></a><h3 class="panel-title">' + json.nick + '</h3></div><div class="clearfix"></div><div class="panel-body">';
            form += '<div class="alert alert-info" role="alert" id="msg" style="display:none;"></div>';
    		form += "<form action='"+admin_dir+"/localstring/save/' id='editLStrForm' method='post'>";
   			//form += "<table class='table'  align='right' width='100%' align='center' border='0'>";
     		form += '<div class="form-group"><label>Original text:</label>';
     		form += '<input class="form-control" type="text" disabled="disabled" value="' + json.en_text + '" />';
            form += '</div>';
            form += '<div class="form-group"><label>';
     		if(json.lang != 'en'){
     			form += "Translate:";
     		}else{
     			form += "New text:";
     		}
            form += '</label>';
     		form += "<textarea class='form-control' id='txted' name='txted'>" + json.text + "</textarea></div>";
            
     		if(json.lang != 'en'){
         		form += '<div class="checkbox"><label><input type="checkbox" name="isTrans" value="1" ';
         		if(json.isT == 1){
         		form += ' checked="checked" ';
         		}
         		form += "/> Translated </div>";
     		}
     		form += "<input class='btn btn-primary' type='button' name='submit' value='Save changes' id='submitLstr' onclick='saveLStr(\"editLStrForm\")' />";
     		form += "<input type='hidden' name='lid' value='" + json.id + "'><input type='hidden' name='lang' value='" + json.lang + "'></form>";
     		form += "</div></div>";
            $("#editw").empty();
     		$("#editw").append(form);
     		$("#editw").show();
     		$("#wCont").show();

   		}
	);
}

function editNickWindow(sid){
	$.getJSON(admin_dir+'/localstring/editnick/lid/' + sid + '/lang/en/', {},
		function(json){
    		form =  "";
            form += '<div class="panel panel-default"><div class="panel-heading"><a class="pull-right" href="javascript:closeWindow(\'editw\')"><strong>close</strong></a><h3 class="panel-title">Edit ' + json.nick + '</h3></div><div class="clearfix"></div><div class="panel-body">';
            form += '<div class="alert alert-info" role="alert" id="msg" style="display:none;"></div>';
    		form += "<form action='"+admin_dir+"/localstring/savenick/' id='editLStrForm' method='post'>";
     		form += '<div class="form-group"><label>New nick:</label>';
     		form += "<input class='form-control' type='text' name='newnick' value='" + json.nick + "' /></div>";
     		form += "<input class='btn btn-primary' type='button' name='submit' value='Save changes' id='submitLstr' onclick='saveLStr(\"editLStrForm\")' />";
     		form += "<input type='hidden' name='lid' value='" + json.id + "'><input type='hidden' name='lang' value='" + json.lang + "'></form>";
     		form += '</div></div>';
            
            $("#editw").empty();
     		$("#editw").show();
     		$("#wCont").show();
     		$("#editw").append(form);
   		}
	);
}






function closeWindow(divId){
	$("#"+divId).hide();
	$("#wCont").hide();
}

function saveLStr(formId){
	var options = {
    	target:     '#editw',
    	url:        admin_dir+'/localstring/save/',
    	type:        'POST',
    	success:    function() {
    	}
	};

	$("#"+formId).ajaxForm(options);
	$("#"+formId).ajaxSubmit({
  		success: function(t) {
    		parent.location.reload(true);
  		}
	});

	$("#msg").empty();
	$("#msg").append('<font color="green"><strong>SAVED</strong></font>').show();
}
 
function deleteLocalstring(Id) {
    if(confirm('Do You really want to delete this string?')) {
        ajax.get(admin_dir+'/localstring/delete/id/' + Id + '/', {'func': 'deleteLocalstringSucc', 'args': Id});
    }
	return false;
}

function deleteLocalstringSucc(resp, Id) {
	if (resp==''){
	    $('#row_' + Id).remove();
    	$('#hint').html('String has been deleted!');
	}
	else {
	   	$('#hint').html(resp);
	}
	    $('#hint').fadeIn('slow');
}
function withStringselected(form, name) {
    $('#hint').fadeOut('fast');
    var act = document.getElementById(name);

    if(act.options[act.selectedIndex].value == 1) {
        deleteLocalstrings(form);
    }
}

function deleteLocalstrings(form) {
    if(confirm('Do You really want to delete this strings?')) {
        var postdata = $(form).formSerialize();
        ajax.post(admin_dir+'/localstring/deletesel/', postdata, {'func': 'deleteLocalstringsSucc', 'args': form});
    }
	return false;
}

function deleteLocalstringsSucc(resp, form)
 {
    var ids = [];
	if (resp!=''){
		ids = eval(resp);		
	    for(var i in ids) {
    	    $('#row_' + ids[i]).remove();
	    }
	    $('#hint').html('Strings has been deleted!');
	    $('#hint').fadeIn('slow');			
	}

}

