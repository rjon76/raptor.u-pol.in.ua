{if $page.flash.success}
<div class="alert alert-success" role="alert">{$page.flash.success}</div>
{/if}
{if $page.flash.error }
<div class="alert alert-danger" role="alert">
{$page.flash.error}
</div>
{/if}
<form action="" method="post">
<div class="panel panel-default">
    	<div class="panel-heading">
			
            <ul class="nav nav-pills pull-right">
              	<li role="presentation"><a href="{$ADMIN_DIR}/pages/list/">Pages list</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/pages/clone/">Page cloner</a></li>
                <li role="presentation"><a href="{$ADMIN_DIR}/pages/import/">Page import</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/pages/meta/id/{$page.page_id}/">Edit pages meta</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/pages/content/id/{$page.page_id}/">Edit pages content</a></li>
                <li role="presentation"><input type="submit" name="editpage" value="Save" class="btn btn-primary" /></li>
			</ul>
            
            <h2 class="panel-title"><strong>Edit page</strong></h2>
            <div class="clearfix"></div>
		</div>
        <div class="panel-body">
        	<div class="col-sm-12 col-lg-6">
			<fieldset>
				<legend>Page data</legend>
                
                <div class="form-group {if $page.err.address}has-error{/if}">
    				<label for="address">Address</label>
    				<input type="text" name="address" class="form-control"  value="{$page.val.address}" placeholder="Enter page URL">
                    {if $page.err.address}<span class="help-block error">(Page with this address already exist)</span>{/if}
  				</div>
				
                <div class="form-group">
    				<label for="title">Title</label>
    				<input type="text" name="title" class="form-control"  value="{$page.val.title}" placeholder="Enter page title">
                   
  				</div>
                
                <div class="form-group">
    				<label for="menu_title">Menu title</label>
    				<input type="text" name="menu_title" class="form-control"  value="{$page.val.menu_title}" placeholder="Enter menu  title">
                   
  				</div>
				
                <div class="form-group">
    				<label for="parent">Parent page</label>
    				 <select name="parent" class="form-control">
        				<option value="0">&nbsp;</option>
    					{foreach from=$page.pages item=pg}
        					<option value="{$pg.pg_id}" class="{$page.langs[$pg.pg_lang].code}" {if $page.val.parent == $pg.pg_id}selected="selected"{/if}>{$pg.pg_address}</option>
    					{/foreach}
    				</select>
                   
  				</div> 
 
                 <div class="form-group">
    				<label for="lang">Language</label>
    				<select name="lang" class="form-control">
    				{foreach from=$page.langs item=lang key=id}
        			<option value="{$id}" class="{$lang.code}" {if $page.val.lang == $id}selected="selected"{/if}>{$lang.code}</option>
    				{/foreach}
    				</select>
                   
  				</div>
		</fieldset>                               
    
    	<fieldset>
    		<legend>Relative pages (<a id="selectLocation" href="javascript:void(0);">Select location</a>):</legend>
			
            <div class="form-group col-lg-6">
				<label for="relativePagesSource">All pages</label><br/>
				<select id="relativePagesSource" class="form-control" name="relative_source[]" multiple  size="10">
        		{foreach from=$page.pages item=pg}
            	{if !$pg.selected}
                <option value="{$pg.pg_id}" class="bg-{$page.langs[$pg.pg_lang].code}">{$pg.pg_address}</option>
            	{/if}
        		{/foreach}
        		</select>
			</div>
        	<div class="form-group col-lg-6">
      			<label for="relativePagesTarget">Selected pages</label><br/>
                <select id="relativePagesTarget" class="form-control" name="relative[]" multiple  size="10">
        		{foreach from=$page.pages item=pg}
            	{if $pg.selected}
                <option value="{$pg.pg_id}" class="bg-{$page.langs[$pg.pg_lang].code}">{$pg.pg_address}</option>
            	{/if}
        		{/foreach}
        		</select>
            
            </div>
   </fieldset>     
</div>

<div class="col-sm-12 col-lg-6">
			<fieldset>
				<legend>Custom options</legend>
                
                <div class="form-group {if $page.err.css}has-error{/if}">
    				<label for="css">CSS</label>
    				<input type="text" name="css" class="form-control"  value="{$page.val.css}" placeholder="Enter css">
  				</div>

                <div class="form-group {if $page.err.jscript}has-error{/if}">
    				<label for="jscript">JavaScript</label>
    				<input type="text" name="jscript" class="form-control"  value="{$page.val.jscript}" placeholder="Enter jscript">
  				</div>

                <div class="form-group {if $page.err.priority}has-error{/if}">
    				<label for="priority">Priority</label>
    				<input type="text" name="priority" class="form-control"  value="{$page.val.priority}" placeholder="Enter priority">
  				</div>

				<div class="checkbox">
  					<label>
    					<input type="checkbox" name="cacheable" {if $page.val.cacheable}checked="checked"{/if}/> Cacheable
  					</label>
				</div>
				<div class="checkbox">
  					<label>
    					<input type="checkbox" name="hidden" {if $page.val.hidden}checked="checked"{/if}/> Hidden
  					</label>
				</div>
				<div class="checkbox">
  					<label>
    					<input type="checkbox" name="indexed" {if $page.val.indexed}checked="checked"{/if}/> Indexed
  					</label>
				</div>                
				
                <div class="form-group">
                	<label for="options">Different options</label><br/>
       				<select name="options[]" class="form-control" multiple size="6" >
        				<option value="noAsync" {if isset($page.options.noAsync)}selected="selected"{/if}>(noAsync) Do not use the asynchronous loading *.js files</option>
        				<option value="noGoogle" {if isset($page.options.noGoogle)}selected="selected"{/if}>(noGoogle) Do not use google analytics </option>
        				<option value="noCanonical" {if isset($page.options.noCanonical)}selected="selected"{/if}>(noCanonical) Do not use "Canonical" tag</option>
        				<option value="noIndex" {if isset($page.options.noIndex)}selected="selected"{/if}>(noIndex) Add meta tag "noindex,nofollow"</option>
        				<option value="useCustomCSS" {if isset($page.options.useCustomCSS)}selected="selected"{/if}>(useCustomCSS) Use custom css files (not auto include style.css)</option>
    				</select>
    			</div>
 				
                <div class="form-group">
                	<label for="extensions">Extensions</label><br/>
    				    <select name="extensions[]" class="form-control" multiple size="10">
    					{foreach from=$page.exts item=ext}
        					<option value="{$ext.id}" {if $ext.selected}selected="selected"{/if}>{$ext.name}</option>
    					{/foreach}
    				</select>
   				</div>
		</fieldset>
</div>
<div class="col-lg-12">
		<fieldset>
			<legend>Screen page* <a href="javascript:void(0);" onclick="getScreenPage({$page.page_id},'refresh');">[refresh screen]</a> <a href="javascript:void(0);" onclick="getScreenPage({$page.page_id},'update');">[update screen]</a></legend>
*This option is only available on an external server, storage folder screenshots (/tmp/)<br /><br />

<img id="screenPage{$page.page_id}" class="screen-page" src="{$page.screen}"{if $page.screen == ""} style="display:none;"{/if} /> 

		</fieldset> 
</div>
</form>
