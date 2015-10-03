function activeDeactiveChangelog(chId, elem, lang) {
    ajax.get(admin_dir+'/products/status/build/'+chId+'/lang/'+lang+'/', {'func': 'activeDeactiveChangelogSucc', 'args': elem});
	if($("#active"+chId).css('color') == 'Green'){
		$("#active"+chId).css('color', 'grey');
	}else{
		$("#active"+chId).css('color', 'Green');
	}
}

function activeDeactiveChangelogSucc(resoponse, elem) {
    elem.src = admin_dir+'/images/' + (resoponse == '0' ? '' : 'un') + 'checked.gif';
}

$(document).ready(function(){
    $("#ajax-append").click(function() { 
		if(parseInt($("input[name^='chOrderNew']:last").val()) != false){
			counter = parseInt($("input[name^='chOrder']:last").val());
		}else{
			counter = parseInt($("input[name^='chOrderNew']:last").val());
		}	
		ord = counter+10;
		html  = '<tr><td><input class="text" value="'+ord+'" name="chOrder[]" size="3" maxlength="4" style="width: 50%;" type="text"></td><td colspan="2"><input class="text" value="" name="chItem[]" style="width: 100%;" type="text"></td></tr>';
		$('#chTab tbody').append(html); 
		$('#chTab').trigger('update'); 
		counter += 10;
        return false; 
    });
    $("#ajax-append-edit").click(function() { 
		if(parseInt($("input[name^='chOrderNew']:last").val()) != false){
			counter = parseInt($("input[name^='chOrder']:last").val());
		}else{
			counter = parseInt($("input[name^='chOrderNew']:last").val());
		}	
		ord = counter + 10;
		html  = '<tr><td><input class="text" value="'+ord+'" name="chOrderNew[]" size="3" maxlength="4" style="width: 50%;" type="text"></td><td colspan="2"><input class="text" value="" name="chItemNew[]" style="width: 100%;" type="text"></td></tr>';
		$('#chTab tbody').append(html); 
		$('#chTab').trigger('update'); 
		counter += 10;
        return false; 
    });
	
});  
