<div class="panel panel-default">
	<div class="panel-heading">
 
        <ul class="nav nav-pills pull-right">
            <li role="presentation"><a href="{$ADMIN_DIR}/products/platforms/"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Back</a></li>
    	</ul>
    
        <h2 class="panel-title">{if $lvals.isNewRecord}New operation system{else}{$platforms.platform_name}{/if}</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
        {if $lvals.postRes == 1}
        <div class="alert alert-success" role="alert">Product <strong>{$product.p_title}</strong> was saved successfully.</div>
        {elseif $lvals.postRes == 0}
        <div class="alert alert-danger" role="alert">Error occured while saving record. Some fields are empty or incorrectly filled in.</div>
        {/if}

        <form action="" method="post" class="form-horizontal">

           {if !$lvals.isNewRecord}
            <div class="form-group">
                <label class="col-sm-2 control-label">ID</label>
               	<div class="col-sm-6">
                    <input type="text" id="platform_id" class="form-control" name="platform_id" value="{$platforms.platform_id}" disabled="disabled" />        	
               	</div>
            </div>
            {/if}
            
            <div class="form-group">
                <label class="col-sm-2 control-label">Name</label>
               	<div class="col-sm-6">
                    <input type="text" id="platform_name" name="platform_name" class="form-control" value="{$platforms.platform_name}" />        	
               	</div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Acronim</label>
               	<div class="col-sm-6">
                    <input type="text" id="platform_acronim" name="platform_acronim" class="form-control" value="{$platforms.platform_acronim}" />        	
               	</div>
            </div>
            
            <div class="form-group">
                <label class="col-sm-2 control-label">Nick</label>
               	<div class="col-sm-6">
                <input type="text" id="platform_nick" name="platform_nick" class="form-control" value="{$platforms.platform_nick}" />        	
               	</div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Order</label>
               	<div class="col-sm-6">
                <input type="text" id="platform_order" name="platform_order" class="form-control" value="{$platforms.platform_order}" />       	
               	</div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label">Soft nick</label>
               	<div class="col-sm-6">
                <input type="text" id="platform_soft_nick 	" name="platform_soft_nick" class="form-control" value="{$platforms.platform_soft_nick}" />        	
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
var platform_name = new LiveValidation('platform_name', { validMessage: "Ok!", wait: 500 } );
platform_name.add(Validate.Presence, {failureMessage: "Name is not specified!"});

var platform_acronim = new LiveValidation('platform_acronim', { validMessage: "Ok!", wait: 500 } );
platform_acronim.add(Validate.Presence, {failureMessage: "Acronim is not specified!"});

var platform_nick = new LiveValidation('platform_nick', { validMessage: "Ok!", wait: 500 } );
platform_nick.add(Validate.Presence, {failureMessage: "Nick is not specified!"});

var platform_soft_nick = new LiveValidation('platform_soft_nick', { validMessage: "Ok!", wait: 500 } );
platform_soft_nick.add(Validate.Presence, {failureMessage: "Soft nick is not specified!"});

var platform_order = new LiveValidation('platform_order', { validMessage: "Ok!", wait: 500 } );
platform_order.add( Validate.Numericality, { onlyInteger: true } );

/*]]>*/</script>{/literal}