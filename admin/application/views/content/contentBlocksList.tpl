<div class="panel panel-default">
	<div class="panel-heading">
    <ul class="nav nav-pills pull-right">
    	<li role="presentation"><a href="{$ADMIN_DIR}/content/addblock/">Add new</a></li>
    </ul>
        <h2 class="panel-title">Blocks list</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>File</th>
                    <th class="text-right">Options</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$content.blocks item=block}
                <tr{if !isset($block.used)} class="error"{/if}>
                    <td>{$block.b_id}</td>
                    <td>{$block.b_name}</td>
                    <td>{$block.b_file}</td>
                    <td class="text-right">
                        <a href="{$ADMIN_DIR}/content/editblock/id/{$block.b_id}/" class="ctrl">edit</a> | <a href="{$ADMIN_DIR}/content/deleteblock/id/{$block.b_id}/" onclick="{literal}if(!confirm('Do You really want to delete this block?')) {return;}{/literal}" class="ctrl">delete</a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>