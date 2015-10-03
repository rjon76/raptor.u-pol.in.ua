{literal}<script type= "text/javascript">/*<![CDATA[*/
function confirmDrop() {
    return confirm("Do you really want drop this string?");
}
/*]]>*/</script>{/literal}
<h1>Languages List</h1>

<fieldset>
<table id="grlist" border="0" cellpadding="5" cellspacing="1" class="form" style="margin:10px 0;" width="100%">
    <tr>
	<th>id</th>
	<th>Name</th>
	<th>Code</th>
	<th>Address Code</th>
	<th>Order</th>
	<th>Blocked</th>            
	{if $glVals.canEdit}
	<th>&nbsp;</th>
	{/if}
	{if $glVals.canDelete}
	<th>&nbsp;</th>
	{/if}
    </tr>
    {foreach from=$langs.langs item=str}
    <tr>
	<td>{$str.l_id}</td>
	<td>{$str.l_name}</td>
	<td>{$str.l_code}</td>
	<td>{$str.l_addrcode}</td>
	<td>{$str.l_order}</td>                
	<td align="center">{if $str.l_blocked}Yes{else}No{/if}</td>
	{if $glVals.canEdit}
	<td><a href="{$ADMIN_DIR}/languages/edit/id/{$str.id}/" class="ctrl">edit</a></td>
	{/if}
	{if $glVals.canDelete}
	<td><a href="{$ADMIN_DIR}/languages/delete/id/{$str.id}/" class="ctrl" onclick="return confirmDrop();">delete</a></td>
	{/if}
    </tr>
    {/foreach}
</table>
</fieldset>