<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Add new item</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        {if $menu_item->hasErrors()}
        <div class="alert alert-danger" role="alert">
        	{$menu_item->printErrors() assign="errors"}
        	{$errors}
        </div>    
        {/if}
        
        <form action="" method="post" class="form-horizontal">
        
            <div class="form-group{if $menu_item->getError('mi_name')} has-error{/if}">
            	<label class="col-sm-2 control-label">Name</label>
            	<div class="col-sm-10">
                 	<input type="text" name="mi_name" class="form-control" value="{$content.menu_item.mi_name}" />
               	    <span class="help-block"></span>
            	</div>
            </div>
        
        
            <div class="form-group">
            	<label class="col-sm-2 control-label">Link</label>
            	<div class="col-sm-10">
                    <input type="text" name="mi_link" class="form-control"  value="{$content.menu_item.mi_link}" />
                    <span class="help-block"></span>
            	</div>
            </div>
        
            <div class="form-group">
            	<label class="col-sm-2 control-label">Parent</label>
            	<div class="col-sm-10">
                    <select class="form-control" name="mi_parent_id">
                        <option value="">&nbsp;</option>
                        {foreach from=$content.menu_items item=item}
                        <option value="{$item.mi_id}" {if $content.menu_item.mi_parent_id == $item.mi_id} selected="selected"{/if}>{$item.mi_name}</option>
                        {/foreach}
                    </select>
                	 <span class="help-block"></span>
            	</div>
            </div>
        
        
            <div class="form-group">
            	<label class="col-sm-2 control-label">Order</label>
            	<div class="col-sm-10">
             	<input type="text" name="mi_order" class="form-control"  value="{$content.menu_item.mi_order}" />
                <span class="help-block"></span>
            	</div>
            </div>
        
            <div class="form-group">
            	<label class="col-sm-2 control-label">Title</label>
            	<div class="col-sm-10">
             	<input type="text" name="mi_title" class="form-control"  value="{$content.menu_item.mi_title}" />
                <span class="help-block"></span>
            	</div>
            </div>
        
            <div class="form-group">
            	<label class="col-sm-2 control-label">Attributes</label>
            	<div class="col-sm-10">
                 	<input type="text" name="mi_attr" class="form-control"  value="{$content.menu_item.mi_attr}" />
                    <span class="help-block"></span>
            	</div>
            </div>
        
            <div class="form-group">
            	<label class="col-sm-2 control-label">CSS Class</label>
            	<div class="col-sm-10">
                 	<input type="text" name="mi_class" class="form-control"  value="{$content.menu_item.mi_class}" />
                    <span class="help-block"></span>
            	</div>
            </div>
        
            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            		<div class="checkbox">
            			<label>
            			     <input type="checkbox" name="mi_hidden" value="1" {if $content.menu_item.mi_hidden} checked="checked"{/if} /> Hidden	
            			</label>
            		</div>
            	</div>
            </div>
        
            <div class="form-group">
            	<label class="col-sm-2 control-label">Pages not view</label>
            	<div class="col-sm-10">
                    <select name="mi_pages_not_view[]" class="form-control" multiple="multiple" size="10">
                    {foreach from=$content.pages item=pg}
                        <option value="{$pg.pg_id}" class="{$content.langs[$pg.pg_lang].l_code}" {if $pg.selected}selected="selected"{/if}>{$pg.pg_address}</option>
                    {/foreach}
                    </select>
                    <span class="help-block"></span>
            	</div>
            </div>
            
            <div class="form-group">
            	<label class="col-sm-2 control-label">Link alias</label>
            	<div class="col-sm-10">
               	    <textarea class="form-control" name="mi_link_alias">{$content.menu_item.mi_link_alias}</textarea>
                    <span class="help-block"></span>
            	</div>
            </div>
        
            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            	<input type="submit" class="btn btn-primary" value="Add" name="addItem" />
            	</div>
            </div>
        
        </form>
    </div>
</div>