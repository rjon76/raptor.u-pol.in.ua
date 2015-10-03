<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
	       <li role="presentation"><a href="{$ADMIN_DIR}/constants/list/">Constants' list</a></li>
           <li role="presentation"><a href="{$ADMIN_DIR}/constants/add/"><span aria-hidden="true" class="glyphicon glyphicon-plus"></span> Add constant</a></li>
        </ul>
        
        <h2 class="panel-title">Edit Constant</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

<form action="" method="post" class="form-horizontal">

        <div class="form-group">
        	<label class="col-sm-2 control-label">Value ({$consts.val.name})</label>
        	<div class="col-sm-4">
                {if $consts.val.name == 'siteClosed' ||
                    $consts.val.name == 'trailingSlash' ||
                    $consts.val.name == '404exist' ||
                    $consts.val.name == 'isCacheable' ||
                    $consts.val.name == 'use_min'}
                <select name="value" class="form-control">
                    <option value="0" {if $consts.val.value == "0"}selected="selected"{/if}>No</option>
                    <option value="1" {if $consts.val.value == "1"}selected="selected"{/if}>Yes</option>
                </select>
                {elseif $consts.val.name == 'addrType'}
                <select name="value" class="form-control">
                    <option value="oldschool" {if $consts.val.value == "oldschool"}selected="selected"{/if}>oldschool</option>
                    <option value="searchfriendly" {if $consts.val.value == "searchfriendly"}selected="selected"{/if}>searchfriendly</option>
                    <option value="mixed" {if $consts.val.value == "mixed"}selected="selected"{/if}>mixed</option>
                </select>
                {elseif $consts.val.name == 'un404page' ||
                        $consts.val.name == 'loginPage'}
                <select name="value" class="form-control">
                    {foreach from=$consts.val.pages item=page}
                    <option value="{$page.pg_id}" {if $consts.val.value == $page.pg_id}selected="selected"{/if} class="{if $page.pg_lang == 1}en{elseif $page.pg_lang == 2}fr{elseif $page.pg_lang == 3}de{/if}">{$page.pg_address}</option>
                    {/foreach}
                </select>
                {else}
                <input type="text" name="value" class="form-control" value="{$consts.val.value}" />
                {/if}
        	</div>
        </div>

        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-4">
        	   <input type="submit" value="Edit" class="btn btn-primary" />
        	</div>
        </div>

</form>
    </div>
</div>