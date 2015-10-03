<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
        	<li role="presentation"><a href="{$ADMIN_DIR}/localstring/list/">List</a></li>
        </ul>
        
        <h2 class="panel-title">Add Localized String</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
    

        {if $lvals.postRes == 1}
        <div class="alert alert-success" role="alert">Localized string &quot;{$lform.nick}&quot; was added successfully.</div>
        {elseif $lvals.postRes == 0}
        <div class="alert alert-danger" role="alert">Error occured while inserting new records. Some fields are empty or incorrectly filled in.</div>
        {/if}

        <form action="" method="post" class="form-horizontal">
        
            <div class="form-group{if isset($lvals.error.nick)} has-error{/if}">
            	<label class="col-sm-2 control-label">Nick (32 characters)</label>
            	<div class="col-sm-10">
             	<input type="text" name="nick" class="form-control" value="{$lform.nick}" maxlength="32" size="32" />
            	<span class="help-block">{$lvals.error.nick}</span>
            	</div>
            </div>
        
            {foreach from=$langs item=key}
            <div class="form-group{if isset($lvals.error[$key.code])} has-error{/if}">
            	<label class="col-sm-2 control-label">{$key.code|upper} text</label>
            	<div class="col-sm-10">
                 	<textarea name="{$key.code}_text" class="form-control">{$lform[$key.code]}</textarea>
                	<span class="help-block">{$lvals.error[$key.code]}</span>
            	</div>
            </div>
            {/foreach}
            
            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            	   <input type="submit" class="btn btn-primary" value="Add new" name="lform" />
            	</div>
            </div>
        
        </form>
        
    </div>
</div>