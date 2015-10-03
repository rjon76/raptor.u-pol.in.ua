<div id="importBlock">
<form method="post" enctype="multipart/form-data" class="form">
{if ($page.import) }
	<h1>Page loaded</h1>
	<br/><br/>
    <h2>Page <span class="note params">"{$page.import.alias}"</span> import success (ID {$page.import.page_id})</h2>
    {if $page.import.locales|@count>0}
    <strong>Existing locales:</strong>
    {foreach from=$page.import.locales item=item key=lang name="langs"}
        {$lang}{if !$smarty.foreach.langs.last}, {/if}
    {/foreach}
    <br/><br/>
    {/if}
    <a class="submit" href="{$ADMIN_DIR}/content/edit/id/{$page.import.page_id}/">Edit</a>
    <a class="submit" target="_blank" href="http://{$header.curHost}{$page.import.alias}">Review</a>
    <a class="submit" href="{$ADMIN_DIR}/pages/list/#row_{$page.import.page_id}">Pages list</a>
    
    {if !empty($page.import.data)}
    <fieldset class="block">
    
    <legend>
        Some errors when importing...
    </legend>
        <table id="tbblocks" border="0" cellpadding="0" cellspacing="0" class="blockTable"> 
            <tr>
                <td>{$page.import.data}</td>
            </tr>
        </table>
        
        </fieldset>
    {/if}
    
	<br/><br/>
{else}

<h1>Processing</h1>

<fieldset class="block">
    <legend>
        <a href="#" class="toogleShow" data-toogle-id="tbpage" title="click to hide"><span class="required">*</span>Page option</a>
    </legend>
    
    <table id="tbpage" border="0" cellpadding="0" cellspacing="0" class="blockTable"> 
        		{ if ($page.check.pages) }
        			{foreach from=$page.check.pages item=ext}
        				<tr><td>Page address "{$ext}" already exists.</td></tr>
        			{/foreach}
        		{else if}
        			<tr><td class="success">Success</td></tr>
        		{/if} 
    </table>
</fieldset>

<fieldset class="block">

    <legend>
        <a href="#" class="toogleShow" data-toogle-id="tbblocks" title="click to hide"><span class="required">*</span>Checking blocks</a>
    </legend>
    
    <table id="tbblocks" border="0" cellpadding="0" cellspacing="0" class="blockTable"> 
    		{if ($page.check.blocks) }
    			{foreach from=$page.check.blocks key=id item=ext}
    				<tr>
                        <td>{$ext}</td> 
                        <td id="b_{$id}"><a onclick="importCorrect('b','{$id}'); return false" href="#" class="submit">Correct</a></td>
                    </tr>
    			{/foreach}
    		{else}
    			<tr><td class="success">Success</td></tr>
    		{/if}
    </table>

    <fieldset class="block">
    
        <legend>
            <a href="#" class="toogleShow" data-toogle-id="tbblockfiles" title="click to hide"><span class="required">*</span>Block files</a>
        </legend>
        
        <table id="tbblockfiles" border="0" cellpadding="0" cellspacing="0" class="blockTable"> 
    		{if ($page.check.blocks_file) }
    			{foreach from=$page.check.blocks_file item=ext}
    				<tr><td>{$ext}</td></tr>
    			{/foreach}
    		{else}
    			<tr><td class="success">Success</td></tr>
    		{/if}

        </table>
        
    </fieldset>
    
    <fieldset class="block">
    
        <legend>
            <a href="#" class="toogleShow" data-toogle-id="tbfields" title="click to hide"><span class="required">*</span>Block Fields</a>
        </legend>
        
        <table id="tbfields" border="0" cellpadding="0" cellspacing="0" class="blockTable"> 

        		{if ($page.check.blocks_fields || $page.check.blocks_fields_type) }
        			{foreach from=$page.check.blocks_fields key=id item=ext}
        				<tr><td>{$ext}</td> <td id="bf_{$id}"><a onclick="importCorrect('bf','{$id}'); return false" href="#" class="submit">Correct</a></td></tr>
        			{/foreach}
        			{foreach from=$page.check.blocks_fields_type key=id item=ext}
        				<tr><td>{$ext}</td></tr>
        			{/foreach}			
        		{else}
        			<tr><td class="success">Success</td></tr>
        		{/if}
        </table>
        
    </fieldset>
    
</fieldset>

{if isset($page.img)}
    <fieldset class="block">
        <legend>
            {assign var="count" value=$page.img|@count}
            <a href="#" class="toogleShow" data-toogle-id="tbimages" title="click to hide">Images {if $count > 0}({$count}) {if $count == 1}image{else}images{/if}{/if}</a>
        </legend>
        
        <table id="tbimages" border="0" cellpadding="0" cellspacing="0" class="blockTable">
		{if ($page.img) }
			{foreach from=$page.img item=ext}
				<tr><td>{$ext}</td></tr>
			{/foreach}
		{else}
			<tr><td class="success">Success</td></tr>
		{/if}
         
         </table>
         
    </fieldset>       
{/if}


<fieldset class="block">
    <legend>
        Checking lstrings:
    </legend>
      
   {if isset($page.lstrings.difference) }   
        <fieldset class="block">
        
            <legend>
            {assign var="count" value=$page.lstrings.difference|@count}
                <a href="#" class="toogleShow" data-toogle-id="tblsupdate" title="click to hide">For update differences {if $count > 0}({$count}) {if $count == 1}lstring{else}lstrings{/if}{/if}</a>
            </legend> 
          
            <table id="tblsupdate" border="0" cellpadding="0" cellspacing="0" class="blockTable">
        
        		{if ($page.lstrings.difference) }
        
        			{foreach from=$page.lstrings.difference item="differences"}
                        {foreach from=$differences item="data"}
                        
            				<tr>
                                <td> 
                                    <strong>{$data.data.nick}</strong><br/>
                                    [{$data.data.lang}] <span class="sep-to">&raquo;</span> {$data.data.sender} <span class="sep-to">&raquo;</span> {$data.data.recipient}
                                </td> 
                                <td class="ls_{$data.data.nick}{$data.data.lang}">
                                    <a onclick="importCorrect('ls','{$data.data.nick}','{$data.data.lang}'); return false;" href="#" class="submit">Update</a>
                                </td>
                            </tr>
                        {/foreach}
        			{/foreach}
                {else}
                <tr><td class="success">Success</td></tr>
        		{/if}
            </table>   
            
        </fieldset>  
    {/if}
        
    {if isset($page.lstrings.notExist) }
        <fieldset class="block">
        
            <legend>
            {assign var="count" value=$page.lstrings.notExist|@count}
                <a href="#" class="toogleShow" data-toogle-id="tblsnew" title="click to hide">For adding new {if $count > 0}({$count}) {if $count == 1}lstring{else}lstrings{/if}{/if}</a>
            </legend> 
          
            <table id="tblsnew" border="0" cellpadding="0" cellspacing="0" class="blockTable">
            
            {if ($page.lstrings.notExist) }
    			{foreach from=$page.lstrings.notExist item=data}
                
        				<tr>
                            <td><strong>{$data.nick}</strong> not exist<br/><br/></td> 
                            <td><a href="{$ADMIN_DIR}/localstring/add/?nick={$data.nick}" target="_blank" class="submit">Click to add</a></td>
                        </tr>
    			{/foreach}
            {else}
                <tr>
                    <td class="success">Success</td>
                </tr>
     		{/if}
            </table>   
            
        </fieldset>  
            
		{/if}
        
    {if isset($page.lstrings.forInsert) }
        <fieldset class="block">
        
            <legend>
                {assign var="count" value=$page.lstrings.forInsert|@count}
                <a href="#" class="toogleShow" data-toogle-id="tblsinsert" title="click to show">For adding when importing page {if $count > 0}({$count}) {if $count == 1}lstring{else}lstrings{/if}{/if}</a> 
            </legend> 
          
            <table id="tblsinsert" border="0" cellpadding="0" cellspacing="0" class="blockTable hidden">
            {if ($page.lstrings.forInsert) }
    			{foreach from=$page.lstrings.forInsert item=data}
        				<tr>
                            <td><strong>{$data.data.nick}</strong><br />
                            {foreach from=$data.data.data key="lang" item="text" name="langs"}
                                <strong>[{$lang}]</strong> <span class="sep-to">&raquo;</span> {$text}{if $marty.foreach.langs.last}{else}; {/if}
                            {/foreach}
                            </td> 
                        </tr>
    			{/foreach}
            {else}
                <tr>
                    <td class="success">Success</td>
                </tr>
     		{/if}
            </table>   
            
        </fieldset>  
            
    {/if}
        
</fieldset>  

<table border="0" cellpadding="0" cellspacing="0" class="blockTable blockSubmit">
    <tr>
        <td align="right">
    		<input type="hidden" name="load_file" value="{$page.file}"/>	
            <input type="submit" name="validate_page" value="Validate" class="submit" />	
            <input type="submit" name="import_page" value="Import" class="submit" />
        </td>
    </tr>
</table>

</form>
</div>
{/if}