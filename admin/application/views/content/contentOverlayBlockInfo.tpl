<center>

{if $vars.text !="" || $vars.image != ""}
    {if $vars.text != ""}
        <h3>Description</h3>
        <textarea rows="10" style="width:100%; border:none;">
            {$vars.text}
        </textarea>
        <hr/>
    {/if}
    {if $vars.image != ""}
        <img class="base64" src="{$vars.image}" style="max-width:600px;display:block;margin:0 auto;"/> 
    {/if}
{else}
    <h3 style="margin-top:100px">No data to display ...</h3>
{/if}

</center>
