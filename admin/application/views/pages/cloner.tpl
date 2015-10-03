{literal}
<script type="text/javascript">

if (window.jQuery) {
    
    var $182 = jQuery.noConflict(); 
    
    $(document).ready(function() {
    
        (function ($) {   
            $182("#formClone #target, #formClone #source").select2();
        })(jQuery);
      
    });
}


</script>
{/literal}

{if $cloner.hasBeenCloned}
	<div class="alert alert-success" role="alert">Page has been cloned!</div>
{/if}
{if  count($cloner.err) > 0 }
<div class="alert alert-danger" role="alert">
	{if $cloner.err.sourceNotExist}Source page is not exist!<br/>{/if}
	{if $cloner.err.targetNotExist}Target page is not exist!<br/>{/if}
	{if $cloner.err.equalIds}Source page and target page could not be the same!<br/>{/if}
</div>
{/if}

<form  id="formClone" action="" method="post" class="form-horizontal">
	<div class="panel panel-default">
    	<div class="panel-heading">
			
            <ul class="nav nav-pills pull-right">
  				<li role="presentation"><a href="{$ADMIN_DIR}/pages/list/">Pages list</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/pages/add/">Add new page</a></li>
                <li role="presentation"><a href="{$ADMIN_DIR}/pages/import/">Page import</a></li>
                
			</ul>
            
            <h2 class="panel-title">Pages cloner</h2>
            <div class="clearfix"></div>
		</div>
        <div class="panel-body">
			<fieldset>


			<div class="form-group">
            	<label for="parent"  class="col-sm-2 control-label">Source page</label>
            	 <div class="col-xs-10 col-md-6">
                 	<select id="source" name="source">
                		{foreach from=$cloner.pagesList item=pg}
                		<option value="{$pg.pg_id}" class="{$cloner.langs[$pg.pg_lang].code}">{$pg.pg_address}</option>
                		{/foreach}
            		</select>
            	</div>
        	</div>
            
			<div class="form-group">
            	<label for="target"  class="col-sm-2 control-label">Target page</label>
            	 <div class="col-xs-10 col-md-6">
                 	<select id="target" name="target[]"  multiple="multiple">
                		{foreach from=$cloner.pagesList item=pg}
                		<option value="{$pg.pg_id}" class="{$cloner.langs[$pg.pg_lang].code}">{$pg.pg_address}</option>
                		{/foreach}
            		</select>
            	</div>
        	</div>
            
 			<div class="form-group">
    			<div class="col-sm-offset-2 col-sm-10">
      				<div class="checkbox">
        				<label>
          					<input type="checkbox" checked="checked" name="page_data" />  Clone with page data (title, css, js, etc.)
        				</label>
      				</div>
    			</div>
  			</div>
            
 			<div class="form-group">
    			<div class="col-sm-offset-2 col-sm-10">
      				<div class="checkbox">
        				<label>
          					<input type="checkbox" checked="checked" name="page_meta" /> Clone with page meta
        				</label>
      				</div>
    			</div>
  			</div>

    </fieldset>
      		<div class="form-group">
    			<div class="col-sm-offset-2 col-sm-10">
      			<button type="submit" class="btn btn-primary">Clone</button>
    			</div>
  			</div>
		</div>
    </div>
</form>
