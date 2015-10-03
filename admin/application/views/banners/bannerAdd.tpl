<div class="panel panel-default">
    <div class="panel-heading">
    
            <ul class="nav nav-pills pull-right">
  				<li role="presentation"><a href="{$ADMIN_DIR}/banners/list/">Category List</a></li>
			</ul>
            
        <h2 class="panel-title">Add new category</h2>
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
<fieldset>
    <div class="form-group{if $model->getError('banner_name')} has-error{/if}">
        <label class="col-sm-2 control-label">Name</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="banner_name" value="{$content.val.banner_name}" />
        </div>
    </div>
    
    <div class="form-group{if $model->getError('banner_alias')} has-error{/if}">
        <label class="col-sm-2 control-label">Alias</label>
        <div class="col-sm-10">
            <input type="text" class="form-control" name="banner_alias" value="{$content.val.banner_alias}" />
        </div>
    </div>
</fieldset>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <input type="submit" class="btn btn-primary" value="Submit" name="addBanner" />
        </div>
    </div>

</form>
</div></div>