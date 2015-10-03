<div class="panel panel-default">
	<div class="panel-heading">

        <h2 class="panel-title col-xs-12 col-sm-10">Pages</h2>
                <div class="h1select col-xs-12 col-sm-2">
                    Language:
                    <select onchange="window.location = '{$ADMIN_DIR}/content/list/lang/' + this.options[this.selectedIndex].value + '/'">
                        <option value="0">All</option>
                        <option value="1" {if $content.lang == 1}selected="selected"{/if}>EN</option>
                        <option value="2" {if $content.lang == 2}selected="selected"{/if}>FR</option>
                        <option value="3" {if $content.lang == 3}selected="selected"{/if}>DE</option>
                        <option value="4" {if $pages.lang == 4}selected="selected"{/if}>JP</option>
                        <option value="5" {if $pages.lang == 5}selected="selected"{/if}>ES</option> 
                    </select>
        </div>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        <table class="table table-hover table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Address</th>
                    <th class="text-center">Language</th>
                    <th class="text-right">Options</th>
                </tr>
            </thead>
            <tbody>
            {foreach from=$content.pagesList item=page}
                <tr>
                    <td>{$page.pg_id}</td>
                    <td>{$page.pg_address}</td>
                    <td class="{$page.lang|lower} text-center">{$page.lang}</td>
                    <td class="text-right"><a href="{$ADMIN_DIR}/content/edit/id/{$page.pg_id}/" class="ctrl">edit content</a></td>
                </tr>
            {/foreach}
            </tbody>
        </table>
    </div>
</div>