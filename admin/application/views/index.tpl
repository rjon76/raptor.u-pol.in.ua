<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en"  lang="en">
<head>
	<base href="{$ADMIN_DIR}/" />
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />

    <title>{$header.curSite} administration panel</title>
	<link rel="stylesheet" href="{$ADMIN_DIR}/styles/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="{$ADMIN_DIR}/styles/styles.css" />
    {foreach from=$page_css item=css}
    <link rel="stylesheet" type="text/css" href="{$ADMIN_DIR}/styles/{$css}" />
    {/foreach}
    <script language="javascript">
        var base_url='{$BASE_URL}';
    	var root_dir='{$ROOT_DIR}';
		var admin_dir='{$ADMIN_DIR}';
        var SiteDir = '{$header.SiteDir}';
        var SiteHostname='{$header.SiteHostname}';
    </script>
  
    <script type="text/javascript" src="{$ADMIN_DIR}/js/jquery-1.9.1.js"></script>
    <script type="text/javascript" src="{$ADMIN_DIR}/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="{$ADMIN_DIR}/js/jquery.form.js"></script>
    <script type="text/javascript" src="{$ADMIN_DIR}/js/ajax.js"></script>
    <script type="text/javascript" src="{$ADMIN_DIR}/js/basic.js"></script>
    {foreach from=$page_js item=js}
    <script type="text/javascript" src="{$ADMIN_DIR}/js/{$js}"></script>
    {/foreach}    
</head>
<body>

{if $files_to_include[0] != "login.tpl"}
{include file="header.tpl"}
<div class="container-fluid">
	<div class="row">
    	<div class="col-lg-12">
{foreach from=$files_to_include item=file}
    {include file="$file"}
{/foreach}

{else}
{include file="login.tpl"}
{/if}
		</div>
    </div>
</div>
</body>
</html>