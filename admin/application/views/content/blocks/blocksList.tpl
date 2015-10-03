<script type="text/javascript">
var pageId = {$content.pageId};
{literal}
{/literal}
</script>
<div class="panel panel-default">
    	<div class="panel-heading">
			{if $content.perms.write}
            <ul class="nav nav-pills pull-right">
                <li role="presentation"><a href="{$ADMIN_DIR}/pages/list/">Pages list</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/pages/clone/">Page cloner</a></li>
                <li role="presentation"><a href="{$ADMIN_DIR}/pages/import/">Page import</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/pages/meta/id/{$content.pageId}/">Edit pages meta</a></li>
  				<li role="presentation"><a href="{$ADMIN_DIR}/content/params/id/{$content.pageId}/">Edit pages</a></li>
      
            
  				<li role="presentation"><a href="#" onclick="cachePage({$content.pageId}); return false;" >Recache page</a></li>
  				<li role="presentation"><a href="#" onclick="clearCachePage({$content.pageId}); return false;" >Clear cache</a></li>
                <li role="presentation"><a href="{$ADMIN_DIR}/pages/myexport/id/{$content.pageId}">Export page</a></li>
                
			</ul>
            {/if}
            <h2 class="panel-title">Page content edit [<a href="http://{$content.siteHostname}{$content.pageAddress}" target="_blank" title="Preview">{$content.pageAddress}</a>]</h2>
            <div class="clearfix"></div>
		</div>
        <div class="panel-body">
        
<div id="hint"></div>
<fieldset id="fs0">
<legend style="color:#aaa;">Blocks</legend>
<div style="padding:5px 10px; border-bottom:1px solid #333" >
<span class="bctrl pull-right">
<img src="{$ADMIN_DIR}/images/addblock.gif" width="16" height="16" title="Add block" alt="Add block" class="pointer" onclick="addBlock('fs0', '{$content.pageId}', '0');" />
</span>
<div class="clearfix"></div>
</div>
{$content.blocksList}

</fieldset>
</div>
</div>

<div class="commanderLayer" id="commander">
    <a href="javascript:closeBrowser();" class="sctrl">close</a>
    <div id="commanderDisp">
    </div>
</div>