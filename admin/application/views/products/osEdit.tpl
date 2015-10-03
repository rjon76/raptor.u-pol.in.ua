<div class="panel panel-default">
	<div class="panel-heading">
 
        <ul class="nav nav-pills pull-right">
            <li role="presentation"><a href="{$ADMIN_DIR}/products/os/"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Back</a></li>
    	</ul>
    
        <h2 class="panel-title">{if $lvals.isNewRecord}New operation system{else}{$os.o_value}{/if}</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        {if $lvals.postRes == 1}
        <div class="alert alert-success" role="alert">Record <strong>{$os.o_value}</strong> was saved successfully.</div>
        {elseif $lvals.postRes == 0}
        <div class="alert alert-danger" role="alert">Error occured while saving record. Some fields are empty or incorrectly filled in.</div>
        {/if}

        <form action="" method="post" class="form-horizontal">

           {if !$lvals.isNewRecord}
           
            <div class="form-group">
                <label class="col-sm-2 control-label">ID</label>
               	<div class="col-sm-6">
                    <input type="text" id="o_id" name="o_id" class="form-control" value="{$os.o_id}" disabled="disabled" />	
               	</div>
            </div>
            {/if}
            <div class="form-group">
                <label class="col-sm-2 control-label">Name</label>
               	<div class="col-sm-6">
                    <input type="text" id="o_value" name="o_value" class="form-control" value="{$os.o_value}" />	
               	</div>
            </div>
        
            <div class="form-group">
                <label class="col-sm-2 control-label">Acronim</label>
               	<div class="col-sm-6">
                <input type="text" id="o_acronim" name="o_acronim" class="form-control" value="{$os.o_acronim}" />	
               	</div>
            </div>
        
            <div class="form-group">
                <label class="col-sm-2 control-label">Order</label>
               	<div class="col-sm-6">
                <input type="text" id="o_order" name="o_order" class="form-control" value="{$os.o_order}" />	
               	</div>
            </div>
        
            {if $lvals.canEdit}
            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-6">
            	   <input type="submit" name="ispost" class="btn btn-primary" value="{if $lvals.isNewRecord}Add new{else}Update changes{/if}" /> 
            	</div>
            </div>
            {/if}

        </form>
    </div>
</div>
{literal}<script type= "text/javascript">/*<![CDATA[*/
var o_value = new LiveValidation('o_value', { validMessage: "Ok!", wait: 500 } );
o_value.add(Validate.Presence, {failureMessage: "Name is not specified!"});

var o_acronim = new LiveValidation('o_acronim', { validMessage: "Ok!", wait: 500 } );
o_acronim.add(Validate.Presence, {failureMessage: "Acronim is not specified!"});

var o_order = new LiveValidation('o_order', { validMessage: "Ok!", wait: 500 } );
o_order.add( Validate.Numericality, { onlyInteger: true } );

/*]]>*/</script>{/literal}