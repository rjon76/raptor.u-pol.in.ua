<div class="panel panel-default">
    <div class="panel-heading">
    
            <ul class="nav nav-pills pull-right">
  				<li role="presentation"><a href="{$ADMIN_DIR}/banners/addbanner/">Add new category</a></li>
			</ul>
    
        <h2 class="panel-title">Banners category</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Alias</th>
                    <th class="text-right">Options</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$content.banners item=banner}
                <tr>
                    <td>{$banner.banner_id}</td>
                    <td>{$banner.banner_name}</td>
                    <td>{$banner.banner_alias}</td>
                    <td class="text-right">
                        <a href="{$ADMIN_DIR}/banners/editbanner/id/{$banner.banner_id}/" class="ctrl">edit</a> | <a href="{$ADMIN_DIR}/banners/deletebanner/id/{$banner.banner_id}/" onclick="{literal}if(!confirm('Do You really want to delete this item?')) return false;{/literal}" class="ctrl">delete</a>
                    </td>
                </tr>
            {/foreach}
            </tbody>
        </table>
        
    </div>
</div>