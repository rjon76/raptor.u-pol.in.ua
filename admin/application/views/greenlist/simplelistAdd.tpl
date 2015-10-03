<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
        	<li role="presentation"><a href="{$ADMIN_DIR}/greenlist/listsimple/">List</a></li>
        </ul>
        
        <h2 class="panel-title">Add Green list record</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        {if $lvals.postRes == 1}
        <div class="alert alert-success" role="alert">New Green List record was added successfully.</div>
        {elseif $lvals.postRes == 0}
        <div class="alert alert-danger" role="alert">Error occured while inserting new records. Some fields are empty or incorrectly filled in.</div>
        {/if}
        
        <form action="" method="post">
    
            <div class="form-group">
            	<label class="col-sm-2 control-label">Address</label>
            	<div class="col-sm-10">
                 	<input type="text" name="address" id="gl_addr" class="form-control" value="{$lform.address}" />
               	    <span class="help-block"></span>
            	</div>
            </div>
    
            <div class="form-group">
            	<label class="col-sm-2 control-label">Header</label>
            	<div class="col-sm-10">
                    <select name="header" class="form-control" onchange="check404(this);" onkeyup="check404(this);">
            		{html_options values=$lform.header.values selected=$lform.header.select output=$lform.header.output}
            	    </select>
               	    <span class="help-block"></span>
            	</div>
            </div>
    
            <div class="form-group">
            	<label class="col-sm-2 control-label">Destination</label>
            	<div class="col-sm-10">
                 	<input type="text" name="destination" id="gl_dest" class="form-control" value="{$lform.destination}" />
               	    <span class="help-block"></span>
            	</div>
            </div>
            
            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            	   <input type="submit" class="btn btn-primary" value="Add record" name="lform" />
            	</div>
            </div>
    
        </form>
    </div>
</div>
{literal}<script type= "text/javascript">/*<![CDATA[*/
var gl_addr = new LiveValidation('gl_addr', { validMessage: "Ok!", wait: 500 } );
gl_addr.add(Validate.Presence, {failureMessage: "Searched address is not specified!"});
gl_addr.add( Validate.Exclusion, { within: [ 'http://'], partialMatch: true, failureMessage: "Only relative addresses are allowed!" } );

var gl_dest = new LiveValidation('gl_dest', { validMessage: "Ok!", wait: 500 } );
gl_dest.add(Validate.Presence, {failureMessage: "Destination address is not specified!"});
/*gl_dest.add( Validate.Inclusion, { within: [ 'http://'], partialMatch: true, failureMessage: "Only absolute addresses are allowed!" } );*/

function check404(sel) {
    var el = document.getElementById("gl_dest");
    if(typeof(el) != "undefined" ) {
	if(sel.value == "{/literal}{$lvals.is404}{literal}") {
	    el.disabled = true;
	    el.value = "{/literal}{$lvals.addr404}{literal}"
	}
	else {
	    el.disabled = false;
	    el.value = "{/literal}{$lform.destination}{literal}"
	}
    }
}
/*]]>*/</script>{/literal}