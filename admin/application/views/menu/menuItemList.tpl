<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Menu Item List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        {if $content.menu_items}
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>Id</th>
                    <th>Name</th>
                    <th>Link</th>
                    <th>Order</th> 
                    <th>Parent</th>        
                    <th>Level</th>            
                    <th>Hidden</th>    
                    <th class="text-right">Options</th>         
                </tr>
            </thead>
            <tbody>
            {foreach from=$content.menu_items item=item}
                <tr>
                    <td>{$item.mi_id}</td>
                    <td>{$item.mi_name|indent:$item.level}</td>
                    <td>{$item.mi_link}</td>
                    <td>{$item.mi_order}</td>
                    <td>{$item.parent}</td>
                    <td>{$item.mi_level}</td>    
                    <td>{if $item.mi_hidden=="1"}+{/if}</td>                
                    <td class="text-right">
                        <a href="{$ADMIN_DIR}/menu/edititem/id/{$item.mi_menu_id}/miid/{$item.mi_id}/" class="ctrl">edit</a> | <a href="{$ADMIN_DIR}/menu/deleteitem/id/{$item.mi_menu_id}/miid/{$item.mi_id}/" onclick="{literal}if(!confirm('Do You really want to delete this item?')) return false;{/literal}" class="ctrl">delete</a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        {else}
            <div class="alert alert-info" role="alert">
            No items for this menu.
            </div>
        {/if}
    </div>
</div>