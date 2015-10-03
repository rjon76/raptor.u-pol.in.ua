	<div class="panel panel-default">
    	<div class="panel-heading">
        	<div class="pull-right">
            <form  class="form-inline">
             <div class="form-group">
    			<label for="inputEmail3" class="control-label">Language:</label>
    			
      			<select class="form-control" onchange="window.location = '{$ADMIN_DIR}/pages/list/lang/' + this.options[this.selectedIndex].value + '/'">
    				<option value="0">All</option>
    				{foreach from=$langs item=item key=key}
     					<option value="{$key}" {if $pages.lang == $key}selected="selected"{/if}>{$item.code|upper}</option>
    				{/foreach}
				</select>
    			
			</div>
			</form>
            </div>
            <ul class="nav nav-pills pull-right">
              	 <li role="presentation"><a href="{$ADMIN_DIR}/pages/add/">Page new</a></li>
                <li role="presentation"><a href="{$ADMIN_DIR}/pages/clone/">Page cloner</a></li>
                <li role="presentation"><a href="{$ADMIN_DIR}/pages/import/">Page import</a></li>
               
			</ul>
       
       <h2 class="panel-title">Manage Pages</h2>
       <div class="clearfix"></div>
	</div>
    <div class="panel-body">     

<form action="" method="post" name="list"  class="form-inline">
<fieldset>
<div id="hint" class="alert alert-success" role="alert"></div>
<div class="rightCtrl" style="margin:5px 5px 0 0;">
    <label for="inputEmail3" class="control-label">With selected:</label>
    <select id="act1" class="form-control">
        <option value="0">&nbsp;&nbsp;&nbsp;</option>
        {if $pages.perms.admin}
            <!--option value="1">Delete</option-->
            <option value="2">Recache</option>
            <option value="3">Clearcache</option>
            <option value="4">Export</option>
            <option value="5">Clean logs</option>
        {/if}
    </select>
    <input type="button" value="Go" class="btn btn-primary" onclick="withselected(this.form,'act1')" />
</div>
<div class="table-responsive">
            
	<table class="table table-striped table-hover">
		<thead>
			<tr>
    			<th>ID</th>
    			<th>Address</th>
    			<th>Title</th>
    			<th align="center">Language</th>
    			{if $pages.perms.write}<th class="bg-info" width="50">Hidden</th>{/if}
    			{if $pages.perms.write}<th class="bg-warning" width="50">Cached</th>{/if}
    			{if $pages.perms.admin}<th class="bg-danger" width="50">Instant recache</th>{/if}
    			<th width="120"></th>
    			<th align="center" class="checkbox-column"><input type="image" src="{$ADMIN_DIR}/images/checked.gif" class="pointer" onclick="return CheckUncheckAll('chx[]', this.form);"/></th>
			</tr>
		</thead>
		<tbody>

{foreach from=$pages.list item=page}
<tr class="{$page.lang}{if $page.pg_hidden}warning{/if}" id="row_{$page.pg_id}">
    <td>{$page.pg_id}</td>
    <td>    
    <a href="{$ADMIN_DIR}/content/edit/id/{$page.pg_id}/" class="ctrl">{$page.pg_address}</a></td>
    <td>{$page.pg_title}</td>
    <td class="{$page.lang_code}" align="center">{$page.lang}</td>
    {if $pages.perms.write}<td align="center" class="bg-info" ><input type="image" class="pointer" src="{$ADMIN_DIR}/images/{if $page.pg_hidden}checked{else}unchecked{/if}.gif" onclick="hideUnhidePage({$page.pg_id}, this);return false;" /></td>{/if}
    {if $pages.perms.write}<td align="center" class="bg-warning"><input type="image" src="{$ADMIN_DIR}/images/{if $page.pg_cacheable}checked{else}unchecked{/if}.gif"{if $pages.perms.admin} class="pointer" onclick="setCacheablePage({$page.pg_id}, this);return false;"{/if} /></td>{/if}
    {if $pages.perms.admin}<td align="center" class="bg-danger"><input type="image" src="{$ADMIN_DIR}/images/{if $page.pg_cached}checked{else}unchecked{/if}.gif" class="pointer" onclick="setInstantCache({$page.pg_id}, this);return false;" /></td>{/if}
    <td align="center" class="button-column">
		{if $page.logs === true}
    <a class="info-link" href="javascript:void(0);" title="Modify page (relolad page for see new logs)" data-original-title="Modify page (relolad page for see new logs)" data-page="{$page.pg_id}"><i class="glyphicon glyphicon-calendar"></i></a> 
    {/if}
        <a href="{$header.curHost}{$page.pg_address}" target="_blank"  data-toggle="tooltip" title="Page preview" class="view" data-original-title="Page preview"><i class="glyphicon glyphicon-eye-open"></i></a>
        {if $pages.perms.write}<a href="{$ADMIN_DIR}/pages/edit/id/{$page.pg_id}/" data-toggle="tooltip" title="Edit page  {$page.pg_id}" class="update" data-original-title="Edit page  {$page.pg_id}"><i class="glyphicon glyphicon-pencil"></i></a>{/if}
        {if $pages.perms.delete}<a href="javascript:deletePage({$page.pg_id});" data-toggle="tooltip" title="Delete page {$page.pg_id}" class="delete" data-original-title="Delete page {$page.pg_id}"><i class="glyphicon glyphicon-trash"></i></a>{/if}
    
       
    </td>
    <td class="chx"><input type="checkbox" name="chx[]" value="{$page.pg_id}"  /></td>
</tr>
{/foreach}
</tbody>
</table>

<div class="rightCtrl" style="margin-right:5px;">
    With selected:
    <select id="act" class="form-control">
        <option value="0">&nbsp;&nbsp;&nbsp;</option>
        {if $pages.perms.admin}
        <!--option value="1">Delete</option-->        
        <option value="2">Recache</option>
        <option value="3">Clearcache</option>
        <option value="4">Export</option>
        <option value="5">Clean logs</option>
        
        {/if}
    </select>
    <input type="button" value="Go" class="btn btn-primary " onclick="withselected(this.form,'act')" />
</div>

</fieldset>
</form>
		</div>
	</div>
	</div>