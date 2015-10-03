<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
        	<li role="presentation"><a href="{$ADMIN_DIR}/products/changelog/id/{$product.p_id}/">Edit change log</a></li>
        	<li role="presentation"><a href="{$ADMIN_DIR}/products/list/">Product product</a></li>
            <li role="presentation"><a href="{$ADMIN_DIR}/products/platforms/">Platform list</a></li>
            <li role="presentation"><a href="{$ADMIN_DIR}/products/os/">OS list</a></li>
        </ul>
        
        <h2 class="panel-title">{if $lvals.isNewRecord}New product{else}{$product.p_title}{/if}</h2>
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
           	<div class="col-sm-10">
            <input type="text" id="p_id" name="p_id" class="form-control" value="{$product.p_id}" disabled="disabled" />	
           	</div>
       </div>
        {/if}
        
        <div class="form-group">
        	<label class="col-sm-2 control-label">Title</label>
        	<div class="col-sm-10">
            <input type="text" id="ptitle" name="p_title" class="form-control" value="{$product.p_title}" />
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">Menu title</label>
        	<div class="col-sm-10">
            <input type="text" id="pmenutitle" name="p_menu_title" class="form-control" value="{$product.p_menu_title}" />
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">Category</label>
        	<div class="col-sm-10">
            <select name="p_cat" class="form-control">
            {html_options values=$cats.values selected=$cats.select output=$cats.names}
            </select>
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">Platform</label>
        	<div class="col-sm-10">
            <select name="p_platform" class="form-control">
            {html_options values=$plat.values selected=$plat.select output=$plat.names}
            </select>
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">Version</label>
        	<div class="col-sm-10">
            <input type="text" id="pversion" name="p_version" class="form-control" value="{$product.p_version}" />
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">Build</label>
        	<div class="col-sm-10">
            <input type="text" name="p_build" class="form-control" value="{$product.p_build}" />
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">Release date</label>
        	<div class="col-sm-10">
            <input type="text" id="pdate" name="p_date" class="form-control" value="{$product.p_date}" />
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">Nick</label>
        	<div class="col-sm-10">
            <input type="text" name="p_nick" id="pnick" class="form-control" value="{$product.p_nick}" />
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">Download</label>
        	<div class="col-sm-10">
            <input type="text" id="pdownload" name="p_download" class="form-control" value="{$product.p_download}" />
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">Count downloads</label>
        	<div class="col-sm-10">
            <input type="text" id="pdownloads" name="p_downloads" class="form-control" value="{$product.p_downloads}" />
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">FAQ Link</label>
        	<div class="col-sm-10">
            <input type="text" id="pfaqlink" name="p_faq_link" class="form-control" value="{$product.p_faq_link}" />
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">Language</label>
        	<div class="col-sm-10">
            <select name="p_languages[]" multiple="multiple" class="form-control">
            {html_options values=$langs.values selected=$langs.select output=$langs.names}
            </select>
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">OS</label>
        	<div class="col-sm-10">
            <select name="p_os[]" multiple="multiple" class="form-control">
            {html_options values=$os.values selected=$os.select output=$os.names}
            </select>
        	</div>
        </div>
        
        <div class="form-group">
        	<label class="col-sm-2 control-label">Order</label>
        	<div class="col-sm-10">
            <input type="text" id="porder" name="p_order" class="form-control" value="{$product.p_order}" />
        	</div>
        </div>
        
        <div class="form-group">
        	<label class="col-sm-2 control-label">Wiki link</label>
        	<div class="col-sm-10">
            <input type="text" id="wiki_link" name="p_wiki_link" class="form-control" value="{$product.p_wiki_link}" />
        	</div>
        </div>
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">Page link</label>
        	<div class="col-sm-10">
            <input type="text" id="page_link" name="p_page_link" class="form-control" value="{$product.p_page_link}" />
        	</div>
        </div>
        
        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-10">
        		<div class="checkbox">
        			<label>
        			<input type="checkbox" id="pfeat" name="p_featured" value="1"{if $product.p_featured == "1"} checked="checked"{/if} /> Product is featured	
        			</label>
        		</div>
        	</div>
        </div>
    
        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-10">
        		<div class="checkbox">
        			<label>
        			<input type="checkbox" id="pfree" name="p_free" value="1"{if $product.p_free == "1"} checked="checked"{/if} /> Product is free	
        			</label>
        		</div>
        	</div>
        </div>
        
        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-10">
        		<div class="checkbox">
        			<label>
        			<input type="checkbox" id="pblocked" name="p_blocked" value="1"{if $product.p_blocked == "1"} checked="checked"{/if} />	Product is blocked
        			</label>
        		</div>
        	</div>
        </div>
    	
        {if $lvals.canEdit}
        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-10">
        	   <input type="submit" name="ispost" class="btn btn-primary" value="{if $lvals.isNewRecord}Add new{else}Update changes{/if}" />  
        	</div>
        </div>
    
        {/if}
    
        </form>
    </div>
</div>
{literal}<script type= "text/javascript">/*<![CDATA[*/
var ptitle = new LiveValidation('ptitle', { validMessage: "Ok!", wait: 500 } );
ptitle.add(Validate.Presence, {failureMessage: "Product title is not specified!"});

var pmenutitle = new LiveValidation('pmenutitle', { validMessage: "Ok!", wait: 500 } );
pmenutitle.add(Validate.Presence, {failureMessage: "Product menu title is not specified!"});

var pversion = new LiveValidation('pversion', { validMessage: "Ok!", wait: 500 } );
pversion.add(Validate.Presence, {failureMessage: "Product version is not specified!"});

var pnick = new LiveValidation('pnick', { validMessage: "Ok!", wait: 500 } );
pnick.add(Validate.Presence, {failureMessage: "Product nick is not specified!"});

var pdownload = new LiveValidation('pdownload', { validMessage: "Ok!", wait: 500 } );
pdownload.add(Validate.Presence, {failureMessage: "Product executable is not specified!"});

var porder = new LiveValidation('porder', { validMessage: "Ok!", wait: 500 } );
porder.add( Validate.Numericality, { onlyInteger: true } );

var pdate = new LiveValidation('pdate', { validMessage: "Ok!", wait: 500 } );
pdate.add(Validate.Presence, {failureMessage: "Release date is not specified!"});
pdate.add( Validate.Format,
              { pattern: /^\d\d\.\d\d\.\d\d\d\d$/i, failureMessage: "Not a valid date format!" } );

/*]]>*/</script>{/literal}