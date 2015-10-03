<h1>
<div class="h1select">
Language:
<select onchange="window.location = '/pages/list/lang/' + this.options[this.selectedIndex].value + '/'">
    <option value="0">All</option>
    <option value="1" {if $pages.lang == 1}selected="selected"{/if}>EN</option>
    <option value="2" {if $pages.lang == 2}selected="selected"{/if}>FR</option>
    <option value="3" {if $pages.lang == 3}selected="selected"{/if}>DE</option>
</select>
</div>
Pages</h1>

<form action="" method="post" name="list">
<fieldset>
<div id="hint"></div>
<div class="rightCtrl" style="margin:5px 5px 0 0;">
    With selected:
    <select id="act1">
        <option value="0">&nbsp;&nbsp;&nbsp;</option>{if $pages.perms.admin}<option value="1">Delete</option><option value="2">Recache</option>{/if}
    </select>
    <input type="button" value="Go" class="sbmt" onclick="withselected(this.form,'act1')" />
</div>
<table border="0" cellpadding="5" cellspacing="1" class="form" style="margin:10px 0;" width="100%">
<tr class="th">
    <td><strong>ID</strong></td>
    <td><strong>Address</strong></td>
    <td><strong>Menu title</strong></td>
    <td><strong>Title</strong></td>
    <td align="center"><strong>Language</strong></td>
    {if $pages.perms.write}<td style="font-size:10px;text-align:center;background:#f9f9f9;" width="50">Hidden</td>{/if}
    {if $pages.perms.write}<td style="font-size:10px;text-align:center;background:#eee;" width="50">Cached</td>{/if}
    {if $pages.perms.admin}<td style="font-size:10px;text-align:center;background:#f9f9f9;" width="50">Instant recache</td>{/if}
    <td width="90"></td>
    <td align="center"><input type="image" src="/images/checked.gif" class="pointer" onclick="return CheckUncheckAll('chx[]', this.form);"/></td>
</tr>

{foreach from=$pages.list item=page}
<tr {if $page.pg_hidden}class="hdn"{/if} id="row_{$page.pg_id}">
    <td>{$page.pg_id}</td>
    <td><a href="/content/edit/id/{$page.pg_id}/" class="ctrl">{$page.pg_address}</a></td>
    <td>{$page.pg_menu_title}</td>
    <td>{$page.pg_title}</td>
    <td class="{$page.lang_code}" align="center">{$page.lang}</td>
    {if $pages.perms.write}<td align="center" style="background:#f9f9f9;"><input type="image" class="pointer" src="/images/{if $page.pg_hidden}checked{else}unchecked{/if}.gif" onclick="hideUnhidePage({$page.pg_id}, this);return false;" /></td>{/if}
    {if $pages.perms.write}<td align="center" style="background:#eee;"><input type="image" src="/images/{if $page.pg_cacheable}checked{else}unchecked{/if}.gif"{if $pages.perms.admin} class="pointer" onclick="setCacheablePage({$page.pg_id}, this);return false;"{/if} /></td>{/if}
    {if $pages.perms.admin}<td align="center" style="background:#f9f9f9;"><input type="image" src="/images/{if $page.pg_cached}checked{else}unchecked{/if}.gif" class="pointer" onclick="setInstantCache({$page.pg_id}, this);return false;" /></td>{/if}
    <td align="center">
        {if $pages.perms.write}<a href="/pages/edit/id/{$page.pg_id}/" class="ctrl">edit</a>{/if}
        {if $pages.perms.delete}| <a href="javascript:deletePage({$page.pg_id});" class="ctrl">delete</a>{/if}
    </td>
    <td class="chx"><input type="checkbox" name="chx[]" value="{$page.pg_id}" /></td>
</tr>
{/foreach}
</table>

<div class="rightCtrl" style="margin-right:5px;">
    With selected:
    <select id="act">
        <option value="0">&nbsp;&nbsp;&nbsp;</option>{if $pages.perms.admin}<option value="1">Delete</option><option value="2">Recache</option>{/if}
    </select>
    <input type="button" value="Go" class="sbmt" onclick="withselected(this.form,'act')" />
</div>

</fieldset>
</form>