{blocks->getVars assign="vars"}
{if isset($vars.cols)}
	{assign var=cols value=$vars.cols}
{else}  
	{assign var=cols value=3}
{/if} 

<div class="features{if isset($vars.class)} {$vars.class}{/if}">

	{if isset($vars.title)}
	   <h2 class="title-main">{$vars.title}</h2>
	{/if}
    
   	{if isset($vars.text)}
	   <div class="text-main">{$vars.text}</div>
	{/if}
              
    
    <ul class="items row">
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