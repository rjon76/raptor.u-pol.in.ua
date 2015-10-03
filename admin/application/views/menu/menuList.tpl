<div class="panel panel-default">
	<div class="panel-heading">
    
            <ul class="nav nav-pills pull-right">
  				<li role="presentation"><a href="{$ADMIN_DIR}/menu/addmenu/">Add new menu</a></li>
			</ul>
    
        <h2 class="panel-title">Menus</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
    <table class="table table-hover table-striped">
        <thead>
            <tr>
                <th><strong>ID</strong></th>
                <th><strong>Name</strong></th>
                <th><strong>Alias</strong></th>
                <th class="text-right">Options</th>
            </tr>
        </thead>
        <tbody>
        {foreach from=$content.menus item=menu}
            <tr>
                <td>{$menu.m_id}</td>
                <td>{$menu.m_name}</td>
                <td>{$menu.m_alias}</td>
                <td class="text-right">
                    <a href="{$ADMIN_DIR}/menu/editmenu/id/{$menu.m_id}/" class="ctrl">edit</a> | <a href="{$ADMIN_DIR}/menu/deletemenu/id/{$menu.m_id}/" onclick="{literal}if(!confirm('Do You really want to delete this item?')) return false;{/literal}" class="ctrl">delete</a>
                </td>
            </tr>
        {/foreach}
        </tbody>
    </table>
    </div>
</div>