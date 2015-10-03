{blocks->getVars assign="vars"}

 {if isset($vars.screen)}
		
        <div class="object hidden-xs">
		{if isset($vars.screen.href)}
            	<a href="{$vars.screen.href}"  class="screenshot {$vars.screen.class}" rel="{if isset($vars.screen.rel)}{$vars.screen.rel}{else}screenshot{/if}">
		{/if}  	
        {if isset($vars.screen.src)}	
            
			<img rel="{if isset($vars.screen.rel)}{$vars.screen.rel}{else}screenshot{/if}" src="{$vars.screen.src}" title="{$vars.screen.title}" class="media-object" {if isset($vars.screen.width)}width="{$vars.screen.width}"{/if} {if isset($vars.screen.height)}height="{$vars.screen.height}"{/if} alt="{$vars.screen.alt}"/> 
  
		{/if}
        
		{if isset($vars.screen.href)}
			</a>
		{/if}             
	</div>
    
  		<div class="object visible-xs">

        {if isset($vars.screen.src)}	
            
			<img src="{$vars.screen.src}" title="{$vars.screen.title}" class="media-object" {if isset($vars.screen.width)}width="{$vars.screen.width}"{/if} {if isset($vars.screen.height)}height="{$vars.screen.height}"{/if} alt="{$vars.screen.alt}"/> 
  
		{/if}
        
           
	</div>
    
    
	{/if}
