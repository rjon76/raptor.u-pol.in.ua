{if isset($content.pages)}
<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title toogle-area"><a href="#" class="toggle-click" data-toogle="hiddenBlock">For pages <span id="item">+</span></a></h2>
        <div class="clearfix"></div>
    </div>
    <div id="hiddenBlock" class="panel-body" style="display:none;">

        
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>ID</th>
                    <th>URL</th>
                    <th class="text-right">Options</th>
                </tr>
                </thead>
                <tbody>
                {foreach from=$content.pages item=page}
                    <tr>
                        <td>{$page.pg_id}</td>
                        <td><a href="{$ADMIN_DIR}/content/edit/id/{$page.pg_id}/" target="_blank">{$page.pg_address}</a></td>
                        <td class="text-right"><a href="{$ADMIN_DIR}/content/edit/id/{$page.pg_id}/" target="_blank">edit page</a></td>
                    </tr>
                {/foreach}
                </tbody>
            </table>
 
    </div>
</div>
        

{/if}