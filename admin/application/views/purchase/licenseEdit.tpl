<div class="panel panel-default">
	<div class="panel-heading">
    
        <ul class="nav nav-pills pull-right">
            <li role="presentation"><a href="{$ADMIN_DIR}/purchase/prices/id/{$license.data.l_pid}/"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Back</a></li>
    	</ul>
            
        <h2 class="panel-title">{$license.productTitle}</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

    <legend>{$license.data.l_name}</legend>

    <form action="" method="post" class="form-horizontal">
                <div class="form-group">
                	<label class="col-sm-2 control-label">Max user number</label>
                	<div class="col-sm-10">
                	   <input type="text" name="usernumber" class="form-control" value="{$license.data.l_usernumber}" />
                	</div>
                </div>
                
                <div class="form-group">
                	<label class="col-sm-2 control-label">Min user number</label>
                	<div class="col-sm-10">
                	   <input type="text" name="min_usernumber" class="form-control" value="{$license.data.l_min_usernumber}" />
                	</div>
                </div>

                <div class="form-group">
                	<label class="col-sm-2 control-label">Users in one licese</label>
                	<div class="col-sm-10">
                	   <input type="text" name="users_in_license" class="form-control" value="{$license.data.l_users_in_license}" />
                	</div>
                </div>
                
                <div class="form-group">
                	<label class="col-sm-2 control-label">Parent License</label>
                	<div class="col-sm-10">
                        <select class="form-control" name="parent">
                            <option></option>
                        {foreach from=$license.licensesList item=lic}
                            <option value="{$lic.l_id}" {if $lic.l_id == $license.data.l_parentid}selected="selected"{/if}>{$lic.l_name}</option>
                        {/foreach}
                        </select>
                	</div>
                </div>

                <div class="form-group">
                	<label class="col-sm-2 control-label">Type</label>
                	<div class="col-sm-10">
                        <select class="form-control" name="type">
                            <option value="H" {if $license.data.l_type == 'H'}selected="selected"{/if}>Home</option>
                            <option value="B" {if $license.data.l_type == 'B'}selected="selected"{/if}>Business</option>
                            <option value="S" {if $license.data.l_type == 'S'}selected="selected"{/if}>Single License</option>
                            <option value="SL" {if $license.data.l_type == 'SL'}selected="selected"{/if}>Single License [for 1 Computer]</option>
                            <option value="C" {if $license.data.l_type == 'C'}selected="selected"{/if}>Company License</option>
                            <option value="FN" {if $license.data.l_type == 'FN'}selected="selected"{/if}>For non-commercial use</option>
                            <option value="FC" {if $license.data.l_type == 'FC'}selected="selected"{/if}>For commercial use</option>
                            <option value="FE" {if $license.data.l_type == 'FE'}selected="selected"{/if}>For end users</option>
                            <option value="LP" {if $license.data.l_type == 'LP'}selected="selected"{/if}>License packs</option>
                        </select>
                	</div>
                </div>

                <div class="form-group">
                	<label class="col-sm-2 control-label">Name</label>
                	<div class="col-sm-10">
                	   <input type="text" name="name" class="form-control" value="{$license.data.l_name}" />
                	</div>
                </div>

                <div class="form-group">
                	<label class="col-sm-2 control-label">Wiki link</label>
                	<div class="col-sm-10">
                	   <input type="text" name="wiki_link" class="form-control" value="{$license.data.l_wiki_link}" />
                	</div>
                </div>
                
                <div class="form-group">
                	<div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                    	   <label><input type="checkbox" name="mailus"{if $license.data.l_mailus == 'Y'} checked="checked"{/if} /> Mail us</label>
                    	</div>
                    </div>
                </div>
                
                <div class="form-group">
                	<div class="col-sm-offset-2 col-sm-10">
                        <input type="submit" value="Update" class="btn btn-primary" />
                    </div>
                </div>

    </form>
    </div>
</div>