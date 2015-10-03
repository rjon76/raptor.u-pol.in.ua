<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Edit page meta</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        {if $page.metaList}
        
        <form action="" method="post" class="form-horizontal">
        
        {foreach from=$page.metaList item=meta}
                <div class="form-group">
                	<label class="col-sm-2 control-label">Name</label>
                	<div class="col-sm-10">
                 	  <input type="text" name="name_{$meta.id}" class="form-control" value="{$meta.name}" />
                	</div>
                </div>

                <div class="form-group">
                	<label class="col-sm-2 control-label">Content</label>
                	<div class="col-sm-10">
                        <textarea name="content_{$meta.id}" class="form-control">{$meta.description|escape:"html"}</textarea>
                	</div>
                </div>
                
                <div class="form-group">
                	<label class="col-sm-2 control-label">Language</label>
                	<div class="col-sm-10">
                        <input type="text" name="lang_{$meta.id}" class="form-control" value="{$meta.lang}" style="width:60px;"/>
                	</div>
                </div>
                
        
        {/foreach}
        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-10">
        	   <input type="submit" name="editMeta" class="btn btn-primary" value="Update changes"/>
               <a href="{$ADMIN_DIR}/pages/deletemeta/id/{$page.id}/mid/{$meta.id}/" class="btn btn-danger">DELETE</a>
        	</div>
        </div>
                
        
        </form>
{else}
    <div class="alert alert-info" role="alert">No meta for this page!</div>
{/if}
    </div>
</div>