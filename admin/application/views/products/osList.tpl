<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
        	<li role="presentation"><a href="{$ADMIN_DIR}/products/osadd/">OS add</a></li>
        </ul>
    
        <h2 class="panel-title">Operation Systems</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Acronim</th>
                    <th>Order</th>        
                    <th class="text-right">Options</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$os item=item}
                <tr>
                    <td>{$item.o_id}</td>
                    <td>{$item.o_value}</td>
                    <td>{$item.o_acronim}</td>
                    <td>{$item.o_order}</td>
                    <td class="text-right">
                        {if $lvals.canEdit}<a href="{$ADMIN_DIR}/products/osedit/id/{$item.o_id}/" class="ctrl">edit</a>{/if}
                        {if $lvals.canEdit && $lvals.canDelete} | {/if}
                        {if $lvals.canDelete}<a href="{$ADMIN_DIR}/products/osdelete/id/{$item.o_id}/" onclick="return confirm('Do You really want to delete this record?');" class="ctrl">delete</a>{/if}
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>
