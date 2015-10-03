<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
	       <li role="presentation"><a href="{$ADMIN_DIR}/constants/list/">Constants' list</a></li>
        </ul>
        
        <h2 class="panel-title">Add Constant</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

{if $consts.consts}   
<form action="" method="post" class="form-horizontal">

        <div class="form-group">
        	<label class="col-sm-2 control-label">Constant</label>
        	<div class="col-sm-10">
                <select name="name" class="form-control">
                {foreach from=$consts.consts item=const}
                    <option value="{$const}">{$const}</option>
                {/foreach}
                </select>
        	</div>
        </div>
        
        <div class="form-group">
        	<label class="col-sm-2 control-label">Value</label>
        	<div class="col-sm-10">
        	   <input type="text" name="value" class="form-control" />
        	</div>
        </div>
        
        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-10">
        	   <input type="submit" value="Add" class="btn btn-primary" />
        	</div>
        </div>

</form>
{else}
    <div class="alert alert-info" role="alert">All constants are already added!</div>
{/if}
    </div>
</div>