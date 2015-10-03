{blocks->getVars assign="vars"}
<div class="wrap{if isset($vars.background_color)} {$vars.background_color}{/if}{if isset($vars.wrap_class)} {$vars.wrap_class}{/if}" {$vars.wrap_attr}>
    {if isset($vars.anchor)}<a href="#" id="{$vars.anchor}"></a>{/if}
    {if !isset($vars.no_container)}
        <div class="{if isset($vars.fluid)}container-fluid{else}container{/if}{if isset($vars.class)} {$vars.class}{/if}" {$vars.attr}>
    {/if}
    {foreach from=$vars.files_to_include item=file}{include file="$file"}{/foreach}
    {if !isset($vars.no_container)}
        </div>
    {/if}
</div>