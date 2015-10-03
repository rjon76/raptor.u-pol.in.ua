<!DOCTYPE html>
<html lang="{$page_lang}">
<head>
    <title>{$page_title}</title>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no"/>
    <meta http-equiv='X-UA-Compatible'  content="IE=edge" />
	{if count($smarty.get) > 0 && $page_address.page_address == '/'}
           <link rel="canonical" href="{$page_address.domain_address}"/>
    {/if}
    {if count($smarty.get) > 1 && $page_address.page_address !== '/'}
           <link rel="canonical" href="{$page_address.domain_address}{$page_address.page_address}"/>
    {/if}
	{foreach from=$page_meta item=meta}
	    <meta {if strpos($meta.name|@strtolower, 'name=') !== false || strpos($meta.name|@strtolower, 'http-equiv=') !== false || strpos($meta.name|@strtolower, 'property=') !== false}{$meta.name}{else}name="{$meta.name}"{/if} {if ($meta.lang)}lang="{$meta.lang}"{/if} content="{$meta.description}" />
    {/foreach}

	{if ($use_min)}
		{if $page_css}<link rel="stylesheet" type="text/css" href="/min/?b=styles&amp;f={foreach from=$page_css item=css name=css}{$css}{if !$smarty.foreach.css.last},{/if}{/foreach}" />{/if}
		{if $page_js}<script async type="text/javascript" src="/min/?b=js&amp;f={foreach from=$page_js item=js name=js}{$js}{if !$smarty.foreach.js.last},{/if}{/foreach}"></script>{/if}
	{else}
		{foreach from=$page_css item=css} <link rel="stylesheet" type="text/css" href="/styles/{$css}" /> {/foreach}
		{foreach from=$page_js item=js} <script type="text/javascript" src="/js/{$js}"></script> {/foreach}
	{/if}

    <!--[if lte IE 6]>
		<link rel="stylesheet" href="/styles/ie6.css" type="text/css" media="screen" />
        <script type="text/javascript" src="/js/pngfix.js"></script>
		<script type="text/javascript" src="/js/ie6.js"></script>
 
	<![endif]-->
    <!--[if IE 7]>
		<link rel="stylesheet" href="/styles/ie7.css" type="text/css" media="screen" />
	<![endif]-->
    <!--[if gte IE 9]>
    		<link rel="stylesheet" type="text/css" href="/styles/ie9.css" media="screen" />
	<![endif]-->
    
</head>
{blocks->getVars assign="vars"}
<body{if isset($vars.attr)} {$vars.attr}{/if}>
    
    {if isset($vars.text)}{$vars.text}{/if}
    
    {foreach from=$vars.files_to_include item=file}
        {include file="$file"}
    {/foreach}

</body>
</html>