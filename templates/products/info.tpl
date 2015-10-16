{blocks->getVars assign="vars"}

<div class="product-info row{if isset($vars.class)} {$vars.class}{/if}" {$vars.attr}>
    
    <div class="visible-xs image-area text-center col-xs-12 col-sm-7 col-md-7 col-lg-7">
    
        {if isset($vars.image.src)}	
            
			<img src="{$vars.image.src}" title="{$vars.image.title}" class="{if isset($vars.image.class)}{$vars.image.class}{else}img-responsive{/if}"{if isset($vars.image.width)} width="{$vars.image.width}"{/if}{if isset($vars.image.height)} height="{$vars.image.height}"{/if}{if isset($vars.image.alt)} alt="{$vars.image.alt}"{/if}/> 
  
		{/if}
    </div>  

    <div class="content row  col-xs-12 col-sm-5 col-md-5 col-lg-5">	
    
    {if isset($vars.title)}
	   <div class="title">{$vars.title}</div>
	{/if}
    
    {if isset($vars.description)}
	   <div class="description">{$vars.description}</div>
	{/if}
    {if isset($vars.price)}
	   <div class="price">{$vars.price}</div>
	{/if}

    {if isset($vars.buttons)}      
            <div class="btn-group text-center">
            {foreach from=$vars.buttons item=item name="links"}
                <a class="{if isset($item.class)}{$item.class}{else}btn btn-lg btn-primary btn-buy{/if}" href="{if $page_lang !== 'en' && strpos($item.href, 'http') === false}/{$page_lang}{/if}{$item.href}"{if isset($item.title)} title="{$item.title}"{/if} {$item.attr}>
                    {if isset($item.text)}{$item.text}{else}{$lstrings.buy}{/if}
                </a>
            {/foreach}                
            </div>  
                 
    {/if} 
    </div>  
    

    <div class="hidden-xs image-area text-center col-xs-12 col-sm-7 col-md-7 col-lg-7">
    
        {if isset($vars.image.src)}	
            
			<img src="{$vars.image.src}" title="{$vars.image.title}" class="{if isset($vars.image.class)}{$vars.image.class}{else}img-responsive{/if}"{if isset($vars.image.width)} width="{$vars.image.width}"{/if}{if isset($vars.image.height)} height="{$vars.image.height}"{/if}{if isset($vars.image.alt)} alt="{$vars.image.alt}"{/if}/> 
  
		{/if}
    </div>    

    
    <div class="clearfix"></div>
    
</div>