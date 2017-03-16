{blocks->getVars assign="vars"}

<div class="slider row{if isset($vars.class)} {$vars.class}{/if}" {$vars.attr}>

	{if isset($vars.title)}
			<h2 class="title">{$vars.title}</h2>
	{/if}
            
    <div class="image-area text-center col-xs-12 col-sm-12 col-md-5 col-lg-5">
    
        {if isset($vars.image.src)}	
            
			<img src="{$vars.image.src}" title="{$vars.image.title}" class="{if isset($vars.image.class)}{$vars.image.class}{else}img-responsive{/if}"{if isset($vars.image.width)} width="{$vars.image.width}"{/if}{if isset($vars.image.height)} height="{$vars.image.height}"{/if}{if isset($vars.image.alt)} alt="{$vars.image.alt}"{/if}/> 
  
		{/if}
    </div>    
    
    <div class="items row text-center col-xs-12 col-sm-12 col-md-7 col-lg-7">
    	<div id="carousel-example-generic" class="carousel slide" data-ride="carousel" >
         		<!-- Indicators -->
  				<ol class="carousel-indicators">
	        	{foreach from=$vars.images item=item name="slides"}
            		<li data-target="#carousel-example-generic" data-slide-to="{$smarty.foreach.slides.iteration}" {if ($item.active)}class="active"{/if}></li>
	            {/foreach}
				</ol>
                
                <!-- Wrapper for slides -->
  				<div class="carousel-inner" role="listbox">
                	{foreach from=$vars.images item=item}
    				<div class="item {if ($item.active)}active{/if}">
      					<img src="{$item.src}" alt="{$item.title}"/>
                        {if isset($item.text)}
      					<div class="carousel-caption">
       						{$item.text}
      					</div>
                        {/if}
    				</div>
					{/foreach}  
                </div>
                
               <!-- Controls -->
                <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev">
                  <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span>
                  <span class="sr-only">Previous</span>
                </a>
                <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next">
                  <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span>
                  <span class="sr-only">Next</span>
                </a>         
        </div>
     </div>  

    <div class="clearfix"></div>
    
</div>