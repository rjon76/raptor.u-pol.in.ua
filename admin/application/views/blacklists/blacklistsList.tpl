<div class="panel panel-default">
    	<div class="panel-heading">
                    
            <ul class="nav nav-pills pull-right">
  				<li role="presentation"><a href="{$ADMIN_DIR}/blacklists/add/">Add ip</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/blacklists/generate/">Generate file</a></li>
			</ul>
        
        
            <h2 class="panel-title">Blacklists</h2>
            <div class="clearfix"></div>
		</div>
        <div class="panel-body">

<form name="filter">
<table class="table">
<tr>


	<td width="5%"></td>
    <td width="15%"><input class="form-control" type="text" name="bl_id" value="{$filter.bl_id}"/></td>
    <td width="15%"><input class="form-control" type="text" name="bl_ip" value="{$filter.bl_ip}"/></td>
    <td width="50%">
		<select name="bl_site_id" class="form-control">
		<option label="Select" value="">Select</option>
		{html_options options=$sitesList selected=$filter.bl_site_id}
		</select>
	</td>
	<td width="15%"><input type="submit" name="filter" value="Filter" class="btn btn-sm btn-default"/></td>


</tr>
</table>
</form>
<form action="{$ADMIN_DIR}/blacklists/delete" method="post" name="filter">
<table class="table table-striped">
<tr>
	<th width="5%"></th>
    <th width="15%"><strong>ID</strong></th>
    <th width="15%"><strong>Ip</strong></th>
    <th width="50%"><strong>Not use for site</strong></th>
    <th width="15%"></th>
</tr>
{foreach from=$blacklists item=item}
<tr>
	<td><input type="checkbox" name="del_bl_id[]" value="{$item.bl_id}"/></td>
    <td>{$item.bl_id}</td>
    <td>{$item.bl_ip}</td>
    <td>{$item.bl_site}</td>
    <td>
        {if $lvals.canEdit}<a href="{$ADMIN_DIR}/blacklists/edit/id/{$item.bl_id}/" class="ctrl">edit</a>{/if}
        {if $lvals.canEdit && $lvals.canDelete} | {/if}
        {if $lvals.canDelete}<a href="{$ADMIN_DIR}/blacklists/delete/id/{$item.bl_id}/" onclick="return confirm('Do You really want to delete this record?');" class="ctrl">delete</a>{/if}
    </td>
</tr>
{/foreach}
<tr>
	<td></td>
    <td></td>
    <td></td>
	<td></td>
    <td><input type="submit" name="delete" value="Delete Selected" class="btn btn-md btn-primary"/></td>
</tr>
</table>
</form>
{if ($pages->pageCount)}
	<div class="paginationControl">
	<!-- Ссылка на предыдущую страницу -->
	{if (isset($pages->previous))}
	  <a href="{$ADMIN_DIR}/blacklists/list/page/{$pages->previous}?{$query }">
		&lt; Previous
	  </a> |
	{else}
	  <span class="disabled">&lt; Previous</span> |
	{/if}
	 
	<!-- Нумерованные ссылки на страницы -->
	{foreach  from=$pages->pagesInRange item=page }
	  {if ($page != $pages->current)}
		<a href="{$ADMIN_DIR}/blacklists/list/page/{$page}?{$query }">
			{$page}
		</a> |
	  {else}
		{$page} |
	  {/if}
	{/foreach}
	 
	<!-- Ссылка на следующую страницу -->
	{if (isset($pages->next))}
	  <a href="{$ADMIN_DIR}/blacklists/list/page/{$pages->next}?{$query }">
		Next &gt;
	  </a>
	{else}
	  <span class="disabled">Next &gt;</span>
	{/if}
	</div>
{/if}

</div></div>
