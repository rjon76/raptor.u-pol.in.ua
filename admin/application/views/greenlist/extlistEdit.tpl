<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
        	<li role="presentation"><a href="{$ADMIN_DIR}/greenlist/listext/">List</a></li>
            <li role="presentation"><a href="{$ADMIN_DIR}/greenlist/addext/">Add record</a></li>
        </ul>
    
        <h2 class="panel-title">Edit Ext Green List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
        {if $lvals.postRes == 1}
        <div class="alert alert-success" role="alert">Green List record was saved successfully.</div>
        {elseif $lvals.postRes == 0}
        <div class="alert alert-danger" role="alert">Error occured while saving record. Some fields are empty or incorrectly filled in.</div>
        {/if}

        <form action="" method="post" class="form-horizontal">
            
            <div class="form-group">
            	<label class="col-sm-2 control-label">Expression</label>
            	<div class="col-sm-10">
                 	<input type="text" name="expression" id="gl_expr" class="form-control" value="{$lform.expression}" />
                	<span class="help-block"></span>
            	</div>
            </div>
       	    
            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            		<div class="checkbox">
            			<label>
            			     <input type="checkbox" name="regular" id="ckregular" value="1"{if $lform.regular == "1"} checked="checked"{/if} /> Is regular	
            			</label>
            		</div>
            	</div>
            </div>
                
            <div class="form-group">
            	<label class="col-sm-2 control-label">Header</label>
            	<div class="col-sm-10">
                    <select name="header" id="gl_header" class="form-control" onchange="check404(this);" onkeyup="check404(this);">
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
            	<label class="col-sm-2 control-label">Order</label>
            	<div class="col-sm-10">
                 	<input type="text" name="order" id="gl_order" class="form-control" style="width:40px;" value="{$lform.order}" />
                	<span class="help-block"></span>
            	</div>
            </div>

            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            	   <input type="submit" class="btn btn-primary" value="Save" name="lform" />
            	</div>
            </div>

        </form>
    </div>
</div>
{literal}<script type= "text/javascript">/*<![CDATA[*/
var gl_expr = new LiveValidation('gl_expr', { validMessage: "Ok!", wait: 500 } );
gl_expr.add(Validate.Presence, {failureMessage: "Expression is not specified!"});

var gl_dest = new LiveValidation('gl_dest', { validMessage: "Ok!", wait: 500 } );
gl_dest.add(Validate.Presence, {failureMessage: "Destination address is not specified!"});
/*gl_dest.add( Validate.Inclusion, { within: [ 'http://'], partialMatch: true, failureMessage: "Only absolute addresses are allowed!" } );*/

var gl_order = new LiveValidation('gl_order', { validMessage: "Ok!", wait: 500 } );
gl_order.add( Validate.Numericality );

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
check404(document.getElementById("gl_header"));
/*]]>*/</script>{/literal}