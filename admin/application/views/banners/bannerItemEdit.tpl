<div class="panel panel-default">
    <div class="panel-heading">

            <ul class="nav nav-pills pull-right">
                <li role="presentation"><a href="{$ADMIN_DIR}/banners/editbanner/id/{$content.banner_item.bi_banner_id}/"><span aria-hidden="true" class="glyphicon glyphicon-chevron-left"></span> Return</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/banners/list/">List category</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/banners/addbanner/">Add new category</a></li>
			</ul>
            
        <h2 class="panel-title">Edit item</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

{if $banner_item->hasErrors()}
<div class="alert alert-danger" role="alert">
	{$banner_item->printErrors() assign="errors"}
	{$errors}
</div>    
{/if}


<form action="" method="post" class="form-horizontal">
    <fieldset>
    <div class="form-group{if $banner_item->getError('bi_name')} has-error{/if}">
        <label class="col-sm-2 control-label">Name</label>
        <div class="col-sm-10">
            <input type="text" name="bi_name" class="form-control" value="{$content.banner_item.bi_name}" />
        </div>
    </div>
    
    <div class="form-group{if $banner_item->getError('bi_link')} has-error{/if}">
        <label class="col-sm-2 control-label">The template file (*.tpl)</label>
        <div class="col-sm-10">
            <input type="text" name="bi_link" class="form-control"  value="{$content.banner_item.bi_link}" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">Order</label>
        <div class="col-sm-10">
            <input type="text" name="bi_order" class="form-control"  value="{$content.banner_item.bi_order}" style="width:40px;" />
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">Type</label>
        <div class="col-sm-10">
        <label class="radio-inline">
             <input type="radio" name="bi_type" value="1" {if $content.banner_item.bi_type ==1} checked="checked"{/if} />Banner
        </label>
        <label class="radio-inline">
         <input type="radio" name="bi_type" value="2" {if $content.banner_item.bi_type ==2} checked="checked"{/if} />Overlay
        </label>
        </div>
    </div>
    <div class="form-group">
        <label class="col-sm-2 control-label">Assign to pages</label>
        <div class="col-sm-10">
        <label class="radio-inline">
        <input type="radio" name="bi_assign" value="0" {if $content.banner_item.bi_assign == 0} checked="checked"{/if} />All pages  
        </label>
        <label class="radio-inline">
        <input type="radio" name="bi_assign" value="1" {if $content.banner_item.bi_assign ==1} checked="checked"{/if} />Display on Selected 
         </label>
         <label class="radio-inline">
        <input type="radio" name="bi_assign" value="2" {if $content.banner_item.bi_assign ==2} checked="checked"{/if} />Hide on Selected 
         </label>
        </div>
    </div>
    
    <div class="form-group">
        <label class="col-sm-2 control-label">Search options</label>
        <div class="col-sm-10">
        <label class="checkbox-inline">
            <input id="checkSearch" type="checkbox" checked="checked" disabled="disabled"/>all at first
        </label>
        <label class="checkbox-inline">
            <input id="checkLike" type="checkbox"/>any match 
        </label>
        <label class="checkbox-inline">
            <input id="checkUnselect" type="checkbox"/>unselect matches
        </label>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
        
        <input placeholder="Search & select" id="inputSearch" type="text" class="form-control"/> 
        <br />
        <div class="btn-group" role="group">
        <input id="btnSearch" type="button" class="btn btn-default" value="Search"/> 
        <input id="btnReset" type="reset" class="btn btn-default" value="Reset"/>
        </div>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
        <select id="selectMselect" name="bi_pages[]" class="form-control" multiple="multiple" size="15">
        {foreach from=$content.pages item=pg}
            <option value="{$pg.pg_id}" class="{$content.langs[$pg.pg_lang].l_code}" {if $pg.selected}selected="selected"{/if}>{$pg.pg_address}</option>
        {/foreach}
        </select>
        </div>
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <div class="checkbox">
                <label>
                    <input type="checkbox" name="bi_hidden"  value="1"{if $content.banner_item.bi_hidden} checked="checked"{/if} />Hidden
                </label>
            </div>
        </div>
    </div>
    
</fieldset>    
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
           <input type="submit" class="btn btn-primary" value="Apply changes" name="editItem" />
        </div>
    </div>

</form>

</div></div>