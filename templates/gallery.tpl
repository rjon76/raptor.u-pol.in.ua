{blocks->getVars assign="vars"}
{if isset($vars.cols)}
	{assign var=cols value=$vars.cols}
{else}  
	{assign var=cols value=3}
{/if} 
<div class="gallery row{if isset($vars.class)} {$vars.class}{/if}" {$vars.attr}>

	{if isset($vars.title)}
			<h2 class="title">{$vars.title}</h2>
	{/if}
            
    <div class="image-area text-center col-xs-12 col-sm-12 col-md-5 col-lg-5">
    
        {if isset($vars.image.src)}	
            
			<img src="{$vars.image.src}" title="{$vars.image.title}" class="{if isset($vars.image.class)}{$vars.image.class}{else}img-responsive{/if}"{if isset($vars.image.width)} width="{$vars.image.width}"{/if}{if isset($vars.image.height)} height="{$vars.image.height}"{/if}{if isset($vars.image.alt)} alt="{$vars.image.alt}"{/if}/> 
  
		{/if}
    </div>    
    
    <ul class="items row text-center col-xs-12 col-sm-12 col-md-7 col-lg-7">
	{foreach from=$vars.files_to_include item=file name="features"}
               <li class="{if isset($vars.class)}{$vars.class}{else}col-xs-12 col-sm-6 col-md-4 col-lg-4{/if}">
                {include file="$file"}
               </li>
                {if $cols == 3}
                    {if $smarty.foreach.features.iteration % 2==0}<li class="clearfix hidden-xs visible-sm hidden-md hidden-lg"></li>{/if}
                    {if $smarty.foreach.features.iteration % $cols==0}<li class="clearfix hidden-xs hidden-sm visible-md visible-lg"></li>{/if}
                {elseif $cols == 2}
                    {if $smarty.foreach.features.iteration % $cols==0}<li class="clearfix"></li>{/if} 
                {elseif $cols == 4}
                    {if $smarty.foreach.features.iteration % 2==0}<li class="clearfix visible-xs visible-sm hidden-md hidden-lg"></li>{/if}
                    {if $smarty.foreach.features.iteration % 4==0}<li class="clearfix hidden-xs hidden-sm visible-md visible-lg"></li>{/if}
                {/if}
                
                
                
                
    {/foreach}
    </ul>  

    <div class="clearfix"></div>
    
</div>