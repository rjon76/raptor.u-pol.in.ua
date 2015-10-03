<div class="panel panel-default">
	<div class="panel-heading">
 
        <ul class="nav nav-pills pull-right">
        	<li role="presentation"><a href="{$ADMIN_DIR}/products/platformadd/">Platform add</a></li>
        </ul>
    
        <h2 class="panel-title">Platforms</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Acronim</th>
                    <th>Nick</th>
                    <th>Order</th>        
                    <th>Soft Nick</th>
                    <th class="text-right">Options</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$platforms item=item}
                <tr>
                    <td>{$item.platform_id}</td>
                    <td>{$item.platform_name}</td>
                    <td>{$item.platform_acronim}</td>
                    <td>{$item.platform_nick}</td>
                    <td>{$item.platform_order}</td>
                    <td>{$item.platform_soft_nick}</td>            
                    <td class="text-right">
                        {if $lvals.canEdit}<a href="{$ADMIN_DIR}/products/platformedit/id/{$item.platform_id}/" class="ctrl">edit</a>{/if}
                        {if $lvals.canEdit && $lvals.canDelete} | {/if}
                        {if $lvals.canDelete}<a href="{$ADMIN_DIR}/products/platformdelete/id/{$item.platform_id}/" onclick="return confirm('Do You really want to delete this record?');" class="ctrl">delete</a>{/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>