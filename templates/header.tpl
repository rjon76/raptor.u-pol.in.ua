{blocks->getVars assign="vars"}

<div class="header-top after-xs">
	
		<div class="row relative">
            
            
            <h1 class="visible-lg visible-md col-xs-12 col-sm-12 col-md-7 col-lg-7">{$vars.h1}</h1>
        
            <div class="arrow box-230x180 hidden-md hidden-sm hidden-xs">
            
            </div>
            
            <div class="logo-area text-center col-xs-12 col-sm-12 col-md-5 col-lg-5">
                <a href="{if $page_lang !== 'en'}/{$page_lang}{/if}/" title="logo">
                    {if isset($vars.imageLg)}
                        <img class="hidden-xs{if isset($vars.imageLg.class)} {$vars.imageLg.class}{/if}" src="{$vars.imageLg.src}"{if isset($vars.imageLg.title)} title="{$vars.imageLg.title}"{/if}{if isset($vars.imageLg.alt)} alt="{$vars.imageLg.alt}"{/if}{if isset($vars.imageLg.width)} width="{$vars.imageLg.width}"{/if}{if isset($vars.imageLg.height)} height="{$vars.imageLg.height}"{/if} {$vars.imageLg.attr}/>
                    {/if}
                    
                    {if isset($vars.imageXs)}
                        <img class="visible-xs{if isset($vars.imageXs.class)} {$vars.imageXs.class}{/if}" src="{$vars.imageXs.src}"{if isset($vars.imageXs.title)} title="{$vars.imageXs.title}"{/if}{if isset($vars.imageXs.alt)} alt="{$vars.imageXs.alt}"{/if}{if isset($vars.imageXs.width)} width="{$vars.imageXs.width}"{/if}{if isset($vars.imageXs.height)} height="{$vars.imageXs.height}"{/if} {$vars.imageXs.attr}/>
                    {/if}
                </a>
            </div>
            
            <h1 class="visible-xs visible-sm col-xs-12 col-sm-12 text-sm-center text-md-center">{$vars.h1}</h1>
        
        <div class="clearfix"></div>
        
        </div>
</div>