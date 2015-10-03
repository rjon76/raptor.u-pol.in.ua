<div class="panel panel-default">
    	<div class="panel-heading">
            <h2 class="panel-title">Logs</h2>
            <div class="clearfix"></div>
		</div>
        <div class="panel-body">
<form name="filter">
<div class="table-responsive">
<table class="table table-striped">
<tr>

    <td><input class="form-control" type="text" name="log_id" value="{$filter.log_id}"/></td>
    <td><input class="form-control" type="text" name="log_user" value="{$filter.log_user}"/></td>
    <td><input class="form-control" type="text" name="log_ip" value="{$filter.log_ip}"/></td>
    <td><input class="form-control" type="text" name="log_controller" value="{$filter.log_controller}"/></td>
    <td><input class="form-control" type="text" name="log_action" value="{$filter.log_action}"/></td>
	<td><input class="form-control" type="text" name="log_request" value="{$filter.log_request}"/></td>
	<td><input class="form-control" type="text" name="log_message" value="{$filter.log_message}"/></td>
	<!--<td><input type="text" name="log_date" value="{$filter.log_date}"/></td>-->
	<td><input class="btn btn-sm btn-default" type="submit" name="filter" value="Filter"/></td>

</tr>
<tr>
    <th><strong>ID</strong></th>
    <th><strong>User</strong></th>
    <th><strong>Ip</strong></th>
    <th><strong>Controller</strong></th>
    <th><strong>Action</strong></th>
	<th><strong>Request</strong></th>
	<th><strong>Message</strong></th>
	<th><strong>Date</strong></th>
</tr>
{foreach from=$log item=item}
<tr>
    <td>{$item.log_id}</td>
    <td>{$item.log_user}</td>
    <td>{$item.log_ip}</td>
    <td>{$item.log_controller}</td>
    <td>{$item.log_action}</td>
	<td>{$item.log_request}</td>
	<td>{$item.log_message}</td>
	<td>{$item.log_date|date_format:"%Y-%m-%d %H:%M:%S"}</td>
</tr>
{/foreach}
</table>
</div>
</form>
{*
{if ($pages->pageCount)}
	<div class="paginationControl">
	<!-- Ссылка на предыдущую страницу -->
	{if (isset($pages->previous))}
	  <a href="{$ADMIN_DIR}/log/index/page/{$pages->previous}?{$query }">
		&lt; Previous
	  </a> |
	{else}
	  <span class="disabled">&lt; Previous</span> |
	{/if}
	 
	<!-- Нумерованные ссылки на страницы -->
	{foreach  from=$pages->pagesInRange item=page }
	  {if ($page != $pages->current)}
		<a href="{$ADMIN_DIR}/log/index/page/{$page}?{$query }">
			{$page}
		</a> |
	  {else}
		{$page} |
	  {/if}
	{/foreach}
	 
	<!-- Ссылка на следующую страницу -->
	{if (isset($pages->next))}
	  <a href="{$ADMIN_DIR}/log/index/page/{$pages->next}?{$query }">
		Next &gt;
	  </a>
	{else}
	  <span class="disabled">Next &gt;</span>
	{/if}
	</div>
{/if}
*}



<nav>
  <ul class="pagination">
  {if (isset($pages->previous))}
    <li>
      <a href="{$ADMIN_DIR}/log/index/page/{$pages->previous}?{$query }" aria-label="Previous">
        <span aria-hidden="true">&laquo;</span>
      </a>
    </li>
    {else}
    <li class="disabled">
        <span aria-hidden="true">&laquo;</span>
    </li>
    {/if}
	{foreach  from=$pages->pagesInRange item=page }
	  {if ($page != $pages->current)}
		<li><a href="{$ADMIN_DIR}/log/index/page/{$page}?{$query }">
			{$page}
		</a></li>
	  {else}
		<li class="active"><a href="#">{$page}<span class="sr-only">(current)</span></a></li>
	  {/if}
	{/foreach}
    {if (isset($pages->next))}
    <li>
      <a href="{$ADMIN_DIR}/log/index/page/{$pages->next}?{$query }" aria-label="Next">
        <span aria-hidden="true">&raquo;</span>
      </a>
    </li>
    {else}
    <li class="disabled">
        <span aria-hidden="true">&raquo;</span>
    </li>
    {/if}
  </ul>
</nav>

















</div></div>