<div class="panel panel-default">
	<div class="panel-heading">
    <ul class="nav nav-pills pull-right">
    	<li role="presentation"><a href="{$ADMIN_DIR}/controllers/list/#controllersAddTitle">Add new Controller</a></li>
    </ul>
        <h2 class="panel-title">Controllers List</h2>
        <div class="clearfix"></div>
    </div>
    
    <div class="panel-body">
    
        <table class="table table-hover table-condensed">
            <thead>
                <tr>
                    <th>Menu name</th>
                    <th class="text-center">Is site related:</th>
                    <th class="text-right">Options</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$conts.contsList item=cont}
                <tr>
                    <td>{$cont.c_menu_name}</td>
                    <td class="text-center">{if $cont.c_is_site_dependent}Yes{else}No{/if}</td>
                    <td  class="text-right">
                        <a href="{$ADMIN_DIR}/controllers/edit/id/{$cont.c_id}/" class="ctrl">edit</a> | <a href="{$ADMIN_DIR}/controllers/delete/id/{$cont.c_id}/" class="ctrl">delete</a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>