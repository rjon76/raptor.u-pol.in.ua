<div id="header">
    <div class="left">
        Howdy, <span>{$header.username}</span>. [<a href="{$ADMIN_DIR}/auth/logout/">Log Out</a>], <a href="{$ADMIN_DIR}/pages/faq/" target="_blank">FAQ</a>
        &nbsp;&nbsp;Change site:
        <select name="siteName" onchange="setSite(this.value)">
        {html_options options=$header.sites.names selected=$header.sites.selected}
        </select>
    </div>
    <strong>Site: <a href="http://{$header.curSite}" target="_blank">{$header.curSite}</a></strong>
</div>

<div id="conts">
    {foreach from=$header.controllers.names item=controller}
    <span {if $controller.c_id == $header.controllers.selected}class="selected"{/if}>
        <a href="{$ADMIN_DIR}/{$controller.c_name}/">{$controller.c_menu_name}</a>
    </span>
    {/foreach}
</div>

<div id="subConts">
    {foreach from=$header.actions.names item=action}
    <span {if $header.actions.selected == $action.name}class="selected"{/if}>
        <a href="{$ADMIN_DIR}/{$header.curController}/{$action.name}/{foreach from=$action.params key=key item=value}{$key}/{$value}/{/foreach}">{$action.menu_name}</a>
    </span>
    {/foreach}
</div>