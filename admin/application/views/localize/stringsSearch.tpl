{literal}<script type= "text/javascript">/*<![CDATA[*/
function confirmDrop() {
    return confirm("Do you really want drop this localized string?");
}
/*]]>*/</script>{/literal}

<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Search Localized Strings</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

<form action="" method="post" class="form-inline">
    <div class="form-group">
    	<label></label>
    	<select id="subject" name="subject" class="form-control">
        <option value="nick" {if $lform.subject == nick} selected="selected" {/if}>Search by nik</option>
        {foreach from=$langs item=key}
           <option  value="{$key.code}" {if $lform.subject == $key.code} selected="selected" {/if}id="subject_{$key.code}">Search by <u>{$key.name}</u> text</option>
        {/foreach}
        </select>    
     </div>
    <div class="form-group">
	   <label></label>
        <input type="text" name="search" value="{$lform.search}" class="form-control" />
    </div>
    <input type="submit" name="lform" value="Search" class="btn btn-primary"/>
</form>

    <div class="table-responsive">
    <table class="table table-hover table-striped table-condensed">
        <thead>
            <tr>
            	<th>Nick</th>
            	<th>Text</th>
            	<th>&nbsp;</th>
            	<th>{$lvals.lang}</th>
            	{if $lvals.canEdit}
            	<th width="5%">&nbsp;</th>
            	{/if}
            	{if $lvals.canDelete}
            	<th width="5%">&nbsp;</th>
            	{/if}
            </tr>
        </thead>
        <tbody>
        {foreach from=$lstrings item=str}
    	{foreach from=$langs item=key}
        <tr>
        	<td>{$str.nick}</td>
        	<td>{$str.en|escape:"html"}</td>
        	<td class="text-center"><img title="{$key.code}" alt="{$key.code}" src="/admin/images/{$key.code}.png" width="16"/></td>
        	<td>{$str[$key.code]|escape:"html"}</td>
        	{if $lvals.canEdit}
        	<td><a href="{$ADMIN_DIR}/localstring/edit/lang/{$key.code}/id/{$str.id}/" class="ctrl">edit</a></td>
        	{/if}
        	{if $lvals.canDelete}
        	<td><a href="{$ADMIN_DIR}/localstring/delete/lang/{$key.code}/id/{$str.id}/{if $lvals.page > 1}page/{$lvals.page}/{/if}" class="ctrl" onclick="return confirmDrop();">delete</a></td>
        	{/if}
        </tr>
    	{/foreach}
        <!--<tr><td colspan="6"><hr /></td></tr>-->
        
        {/foreach}
        </tbody>
    </table>
    </div>
    </div>
</div>