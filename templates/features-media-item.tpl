{blocks->getVars assign="vars"}
    
    <div class="media">
        <div class="media-left text-sm-center">
            <img src="{$vars.image.src}" class="media-object{if isset($vars.image.class)} {$vars.image.class}{/if}"{if isset($vars.image.width)} width="{$vars.image.width}"{/if}{if isset($vars.image.height)} height="{$vars.image.height}"{/if}{if isset($vars.image.alt)} alt="{$vars.image.alt}"{/if}{if isset($vars.image.title)} title="{$vars.image.title}"{/if} {$vars.image.attr}/> 
        </div>
        <div class="media-body text-content text-left">
            {$vars.text}
        </div>
    </div> 

