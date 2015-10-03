{blocks->getVars assign="vars"}

{if isset($vars.title)}
    <div class="title-content">{$vars.title}</div>
{/if}

{if isset($vars.text)}
    <div class="text-content">{$vars.text}</div>
{/if}
