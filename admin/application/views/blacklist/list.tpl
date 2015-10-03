<h1>Black list keys</h1>
{if $content.count_pages > 0}
<div class="paging">
    <ul class="line">
    {if isset($content.prev)}
    	<li><a href="{$base_url}/{$header.curController}/{$header.actions.selected}/page/{$content.prev}/" title="Prev page">&larr;</a></li>
    {/if}
    {section name=pg start=$content.from loop=$content.count_pages+1}
	{if $smarty.section.pg.index == $content.page}
	<li>{$smarty.section.pg.index}</li>
	{else}
	<li><a href="{$base_url}/{$header.curController}/{$header.actions.selected}/page/{$smarty.section.pg.index}/">{$smarty.section.pg.index}</a></li>
	{/if}
    {/section}
    {if isset($content.next)}
    	<li><a href="{$base_url}/{$header.curController}/{$header.actions.selected}/page/{$content.next}/" title="Next page">&rarr;</a></li>
    {/if}
    </ul>
</div>
{/if}
<div id="hint"></div>
<form action="" method="post" name="list">
<fieldset>

<table border="0" cellpadding="6" cellspacing="1" class="form">
<tr>
    <td colspan="2" >Search keys:
  <input name="bl_name" value="{if isset($smarty.post.bl_name)}{$smarty.post.bl_name}{/if}" width="128" class="text" style="width:600px">
  <input type="submit" style="margin-left:5px;" class="submit" value="Search" name="blform">  </td>
    <td align="center" colspan="4">
	Action:
    <select id="act1">
        <option value="0">&nbsp;&nbsp;&nbsp;</option>
        {if $content.canDelete}<option value="1">Delete</option>{/if}
    </select>
    <input type="button" value="Go" class="sbmt" onclick="return withBlacklistselected(this.form,'act1')" />
    </td>
</tr>


<tr>
    <td align="center"><strong>ID</strong></td>
    <td align="center" width="750"><strong>Key number</strong></td>
    <td align="center" width="50"><strong>Attempt</strong></td>
    <td align="center"  width="50"><strong>Not active</strong></td>   
    <td width="100" align="center" ><strong>Action</strong></td>
     <td width="40" align="center" ><strong>Select</strong></td>
</tr>
{foreach from=$content.data item=item}
<tr id="row_{$item.bl_id}" class="hover">
    <td>{$item.bl_id}</td>
    <td >{$item.bl_name}</td>
    <td align="right">{$item.bl_count}</td>
    <td align="center">{if ($item.bl_hidden !== '0')}+{/if}</td>
    <td>
        <a href="{$base_url}/{$header.curController}/edit/id/{$item.bl_id}/" class="ctrl">edit</a> |
        <a href="{$base_url}/{$header.curController}/delete/id/{$item.bl_id}/" onclick="{literal}if(!confirm('Do You really want to delete this item?')) return false;{/literal}" class="ctrl">delete</a>
    </td>
    <td class="chx"><input type="checkbox" name="chx[]" value="{$item.bl_id}" /></td> 
</tr>
{/foreach}
</table>
</fieldset>
</form>
{if $content.count_pages > 0}
<div class="paging">
    <ul class="line">
    {if isset($content.prev)}
    	<li><a href="{$base_url}/{$header.curController}/{$header.actions.selected}/page/{$content.prev}/" title="Prev page">&larr;</a></li>
    {/if}
    {section name=pg start=$content.from loop=$content.count_pages+1}
	{if $smarty.section.pg.index == $content.page}
	<li>{$smarty.section.pg.index}</li>
	{else}
	<li><a href="{$base_url}/{$header.curController}/{$header.actions.selected}/page/{$smarty.section.pg.index}/">{$smarty.section.pg.index}</a></li>
	{/if}
    {/section}
    {if isset($content.next)}
    	<li><a href="{$base_url}/{$header.curController}/{$header.actions.selected}/page/{$content.next}/" title="Next page">&rarr;</a></li>
    {/if}
    </ul>
</div>
{/if}
