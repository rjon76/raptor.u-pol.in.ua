{blocks->getVars assign="vars"}
<div{if isset($vars.id)} id="{$vars.id}"{/if}{if isset($vars.class)} class="{$vars.class}"{/if} {$vars.attr}>
{$vars.text}
{foreach from=$vars.files_to_include item=file}{include file="$file"}{/foreach}
</div>