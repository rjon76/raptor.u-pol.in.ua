<div class="panel panel-default">
    <div class="panel-heading">
        <h2 class="panel-title">Banners List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

{if $content.banner_items}
<table class="table table-striped">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Link</th>
    <th>Order</th>          
    <th>Hidden</th>     
    <th></th>        
</tr>
{foreach from=$content.banner_items item=item}
<tr>
    <td>{$item.bi_id}</td>
    <td>{$item.bi_name|indent:$item.level}</td>
    <td>{$item.bi_link}</td>
    <td>{$item.bi_order}</td>
    <td>{if $item.bi_hidden=="1"}+{/if}</td>                
    <td>
        <a href="{$ADMIN_DIR}/banners/edititem/id/{$item.bi_banner_id}/miid/{$item.bi_id}/" class="ctrl">edit</a> |
        <a href="{$ADMIN_DIR}/banners/deleteitem/id/{$item.bi_banner_id}/miid/{$item.bi_id}/" onclick="{literal}if(!confirm('Do You really want to delete this item?')) return false;{/literal}" class="ctrl">delete</a>
    </td>
</tr>
{/foreach}
</table>
{else}
<div class="alert alert-info" role="alert">
    No items for this menu.
</div>
{/if}
</div></div>