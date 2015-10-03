{blocks->getVars assign="vars"}

<div class="footer row">
			
			<div class="description text-left text-md-center text-sm-center col-xs-12 col-sm-12 col-md-4 col-lg-4 ">
                {$vars.description}
            </div>        
			<div class="text text-left text-md-center text-sm-center sm-top-20 md-top-20 col-xs-12 col-sm-12 col-md-4 col-lg-4 ">
                {$vars.text}
            </div>  
			<div class="hidden-xs map-area text-center sm-top-20 md-top-20 col-xs-12 col-sm-12 col-md-4 col-lg-4">
                <a href="/images/map-big.gif"  class="screenshot {$vars.screen.class}">
			     <img src="/images/logo-map.png" title="{$vars.screen.title}" class="media-object" {if isset($vars.screen.width)}width="{$vars.screen.width}"{/if} {if isset($vars.screen.height)}height="{$vars.screen.height}"{/if} alt="{$vars.screen.alt}"/> 
                 </a>
            </div>  
			
            <div class="visible-xs map-area text-center sm-top-20 md-top-20 col-xs-12 col-sm-12 col-md-4 col-lg-4">
			     <img src="/images/logo-map-xs.png" title="{$vars.screen.title}" class="media-object" width="320"/> 

            </div> 
        <div class="clearfix"></div>
</div>