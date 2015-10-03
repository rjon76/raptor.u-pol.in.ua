<div class="panel panel-default">
    <div class="panel-heading">
                    
        <ul class="nav nav-pills pull-right">
            <li role="presentation"><a href="{$ADMIN_DIR}/blacklists/list/">List</a></li>
            <li role="presentation"><a href="{$ADMIN_DIR}/blacklists/add/">Add ip</a></li>
        </ul>
        
        <h2 class="panel-title">Generate file</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        {if $sitesList}
            <div class="alert alert-info" role="alert">
            {foreach from=$sitesList key=key item=item}
                {if $item}<p class="text-success">{$key} (Ok)</p>{else}<p class="text-danger">{$key} (Error)</p>{/if}
            {/foreach}
            </div>
        {else}

            <form action="" method="post">
            
                    <div class="form-group">
                                           
                            <div class="checkbox">
                                <label for="generate">
                                    <input id="generate" type="checkbox" value="1" name="generate" />Confirm generate
                                </label>
                            </div>
                    </div>
    
                    <input type="submit" class="btn btn-primary" value="Generate" name="ispost" />
            </form>

        {/if}
        
    </div>
</div>