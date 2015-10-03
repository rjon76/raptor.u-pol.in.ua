<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
	       <li role="presentation"><a href="{$ADMIN_DIR}/constants/add/"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span> Add constant</a></li>
        </ul>
        
        <h2 class="panel-title">Constants List</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    
    <div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th><strong>Id</strong></th>
                <th><strong>Name</strong></th>
                <th><strong>Value</strong></th>
                <th><strong>Comment</strong></th>
                <th><strong>Parent Id</strong></th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody>
            {foreach from=$consts.consts item=const}
                <tr>
                    <td class="text-center">{$const.c_id}</td>
                    <td style="color:#009;">{$const.c_name}</td>
                    <td>
                        {if $const.c_name == 'siteClosed' ||
                            $const.c_name == 'trailingSlash' ||
                            $const.c_name == '404exist' ||
                            $const.c_name == 'isCacheable' ||
                            $const.c_name == 'use_min'}
                            {if $const.c_value == 0}No{else}Yes{/if}
                        {else}
                            {$const.c_value}
                        {/if}
                    </td>
                    <td>{$const.c_comment}</td>
                    <td class="text-center">{$const.c_parent}</td>
                    <td>
                        <a href="{$ADMIN_DIR}/constants/edit/id/{$const.c_id}/" class="ctrl">edit</a> |
                        <a href="{$ADMIN_DIR}/constants/delete/id/{$const.c_id}/" class="ctrl">delete</a>
                    </td>
                </tr>
            {/foreach}
        </tbody>
    </table>
    </div>
    </div>
</div>