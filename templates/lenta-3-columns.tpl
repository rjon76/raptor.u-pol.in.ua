{blocks->getVars assign="vars"}

<div class="lenta-3-columns row{if isset($vars.class)} {$vars.class}{/if}" {$vars.attr}>
    <div class="left-text text-left hidden-xs hidden-sm col-xs-12 col-sm-12 col-md-4 col-lg-4">
        {$vars.left_text}
    </div> 
    
    <div class="image-area text-center col-xs-12 col-sm-12 col-md-4 col-lg-4">
        {if isset($vars.image.src)}	
			<img src="{$vars.image.src}" title="{$vars.image.title}" class="{if isset($vars.image.class)}{$vars.image.class}{else}img-responsive{/if}"{if isset($vars.image.width)} width="{$vars.image.width}"{/if}{if isset($vars.image.height)} height="{$vars.image.height}"{/if}{if isset($vars.image.alt)} alt="{$vars.image.alt}"{/if}/> 
		{/if}
    </div>    
    
    <div class="right-text text-right hidden-xs hidden-sm col-xs-12 col-sm-12 col-md-4 col-lg-4">
        {$vars.right_text}
    </div> 
    
    <div class="clearfix"></div>
    
</div>