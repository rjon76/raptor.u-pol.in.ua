<nav class="navbar navbar-default navbar-static-top">
      <div class="container-fluid">
        <div class="navbar-header">
          <button aria-controls="navbar" aria-expanded="false" data-target="#navbar" data-toggle="collapse" class="navbar-toggle collapsed" type="button">
            <span class="sr-only">Toggle navigation</span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
            <span class="icon-bar"></span>
          </button>
          <a href="http://{$header.curSite}" target="_blank" class="navbar-brand">{$header.curSite}</a>
        </div>
        <div class="navbar-collapse collapse" id="navbar">
          <ul class="nav navbar-nav">
              {foreach from=$header.controllers.names item=controller}
              	{if $header.actions.names && $controller.c_id == $header.controllers.selected}
               	 	<li class="{if $controller.c_id == $header.controllers.selected}active{/if} {if $header.actions.names}dropdown{/if}">
        				<a  aria-expanded="false" role="button" data-toggle="dropdown" class="dropdown-toggle" href="#">{$controller.c_menu_name} <span class="caret"></span></a>
                        
                       <ul role="menu" class="dropdown-menu">
                       	
    {foreach from=$header.actions.names item=action}
    <li {if $header.actions.selected == $action.name}class="active"{/if}>
        <a href="{$ADMIN_DIR}/{$header.curController}/{$action.name}/{foreach from=$action.params key=key item=value}{$key}/{$value}/{/foreach}" >{$action.menu_name}</a>
    </li>
    {/foreach}
    </ul>
                        
    				</li>
                {else}
                 	<li {if $controller.c_id == $header.controllers.selected}class="active"{/if} >
        				<a href="{$ADMIN_DIR}/{$controller.c_name}/">{$controller.c_menu_name}</a>
    				</li>
                {/if}
              
    
    {/foreach}

           
          </ul>
			<!--<form class="navbar-form navbar-right">
           Change site:
        	<select name="siteName" onchange="setSite(this.value)">
        		{html_options options=$header.sites.names selected=$header.sites.selected}
        	</select>
		</form> -->
        <ul class="nav navbar-nav navbar-right">
			<li class="dropdown">
				<a href="#" data-toggle="dropdown" class="dropdown-toggle">Howdy, <span>{$header.username}</span> <span class="caret"></span></a>
                <ul class="dropdown-menu" id="yw2">
					<li><a href="{$ADMIN_DIR}/auth/logout/" tabindex="-1">Log Out</a></li>
                	<li><a href="{$ADMIN_DIR}/pages/faq/">FAQ</a></li>
                    <li class="divider"></li>
                    <li class="dropdown-header">Change site</li>
                     {foreach from=$header.sites.names item=item key=key}
                     <li><a href="{$ADMIN_DIR}/pages/setsite/site_id/{$key}" >{$item}</a></li>
                     {/foreach}
				</ul>
			</li>
		</ul>
         
        </div><!--/.nav-collapse -->
     
<!--      {if $header.actions.names}
     <ol class="breadcrumb">
    {foreach from=$header.actions.names item=action}
    <li {if $header.actions.selected == $action.name}class="active"{/if}>
        <a href="{$ADMIN_DIR}/{$header.curController}/{$action.name}/{foreach from=$action.params key=key item=value}{$key}/{$value}/{/foreach}">{$action.menu_name}</a>
    </li>
    {/foreach}
    </ol>

      {/if}--> 
      </div>
</nav>

