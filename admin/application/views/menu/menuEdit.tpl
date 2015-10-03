<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
        	<li role="presentation"><a href="{$ADMIN_DIR}/menu/list/">Menu list</a></li>
            <li role="presentation"><a href="{$ADMIN_DIR}/menu/addmenu/">Add new menu</a></li>
        </ul>
    
        <h2 class="panel-title">Edit menu</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
        {if $model->hasErrors()}
        <div class="alert alert-danger" role="alert">
        	{$model->printErrors() assign="errors"}
        	{$errors}
        </div>    
        {/if}

        <form action="" method="post" class="form-horizontal">
        
            <div class="form-group{if $model->getError('m_name')} has-error{/if}">
            	<label class="col-sm-2 control-label">Name</label>
            	<div class="col-sm-10">
                 	<input type="text" class="form-control" name="m_name" value="{$content.val.m_name}" />
               	    <span class="help-block"></span>
            	</div>
            </div>
            
            <div class="form-group{if $model->getError('m_alias')} has-error{/if}">
            	<label class="col-sm-2 control-label">Alias</label>
            	<div class="col-sm-10">
                 	<input type="text" class="form-control" name="m_alias" value="{$content.val.m_alias}" />
               	    <span class="help-block"></span>
            	</div>
            </div>
    
            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            	   <input type="submit" class="btn btn-primary" value="Update" name="updateMenu" />
            	</div>
            </div>
        
        </form>
        
    </div>
</div>