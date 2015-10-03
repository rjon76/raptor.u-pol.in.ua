<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
        	
        	{foreach from=$langs item=key}
        	<li role="presentation"><a href="{$ADMIN_DIR}/localstring/edit/lang/{$key.code}/id/{$lvals.id}/" title="{$key.name}"><img src="{$ADMIN_DIR}/images/{$key.code}.png" alt="{$key.name}" width="16" /></a></li>
        	{/foreach}
            <li role="presentation"><a href="{$ADMIN_DIR}/localstring/list/">List</a></li>
            <li role="presentation"><a href="{$ADMIN_DIR}/localstring/add/">Add</a></li>
        </ul>
        
        <h2 class="panel-title">Edit Localized String</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
    <legend>Current language is <strong>{$lvals.lang|upper}</strong> / <a href="{$ADMIN_DIR}/localstring/list/lang/{$lvals.lang}/">List {$lvals.lang|upper} Localizations</a></legend>
    
    {if $lvals.postRes == 1}
    <div class="alert alert-success" role="alert">Changes for localized string &quot;{$lform.nick}&quot; were saved successfully.</div>
    {elseif $lvals.postRes == 0}
    <div class="alert alert-danger" role="alert">Error occured. Some fields are empty or incorrectly filled in.</div>
    {/if}

    <form action="" method="post" class="form-horizontal">
    
        <div class="form-group">
        	<label class="col-sm-2 control-label">* Nick</label>
        	<div class="col-sm-6">
         	  <input type="text" name="nick" class="form-control" value="{$lform.nick}" />
        	</div>
        </div>
        
        <div class="form-group">
        	<label class="col-sm-2 control-label">English text</label>
        	<div class="col-sm-6">
         	  
              <textarea class="form-control" disabled="disabled">{$lform.en_text|escape:"html"}</textarea>
        	</div>
        </div>
        
        <div class="form-group">
        	<label class="col-sm-2 control-label">* Text</label>
        	<div class="col-sm-6">
         	  <textarea name="text" class="form-control" rows="5" cols="2">{$lform.text}</textarea>
        	</div>
        </div>
    
        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-6">
        	   <input type="submit" class="btn btn-primary" value="Save changes" name="lform" />
        	</div>
        </div>
    
    </form>
    </div>
</div>