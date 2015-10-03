<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Permissions</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">


<legend>User permissions</legend>
{if $users.permissions}
<form action="" method="post">

<table class="table table-hover table-condensed">
<thead>
<tr>
    <th>Controller</td>
    <th class="text-center">Read</th>
    <th class="text-center">Write</th>
    <th class="text-center">Delete</th>
    <th></th>
</tr>
</thead>
<tbody>

{foreach from=$users.permissions item=perm}
<tr>
    <td>{$perm.c_menu_name}</td>
    <td class="text-center"><input type="checkbox" name="{$perm.uc_id}_read" {if $perm.read}checked="checked"{/if}/></td>
    <td class="text-center"><input type="checkbox" name="{$perm.uc_id}_write" {if $perm.write}checked="checked"{/if}/></td>
    <td class="text-center"><input type="checkbox" name="{$perm.uc_id}_delete" {if $perm.delete}checked="checked"{/if}/></td>
    <td><a href="{$ADMIN_DIR}/users/delperm/user/{$perm.uc_user_id}/cont/{$perm.uc_controller_id}/">delete</a></td>
</tr>
{/foreach}
</tbody>
<tfoot>
<tr>
    <td colspan="5" class="text-right">
        <input type="submit" class="btn btn-primary" value="Update" name="updatePerms" />
    </td>
</tr>
</tfoot>
</table>
</form>
{else}
<div class="alert alert-info" role="alert">No any permissions for this User.</div>
{/if}

<legend>Add permission</legend>
{if $users.controllers}
<form action="" method="post" class="form-horizontal">


    <div class="form-group">
    	<label class="col-sm-2 control-label">Controller</label>
    	<div class="col-sm-10">
            <select name="controller" class="form-control">
            {html_options options=$users.controllers}
            </select>
    	</div>
    </div>

    <div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10">
    		<div class="checkbox">
    			<label>
    			 <input type="checkbox" name="read" /> Read	
    			</label>
    		</div>
    	</div>
    </div>
    
    <div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10">
    		<div class="checkbox">
    			<label><input type="checkbox" name="write" /> Write</label>
    		</div>
    	</div>
    </div>
    
    <div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10">
    		<div class="checkbox">
    			<label><input type="checkbox" name="delete" /> Delete</label>
    		</div>
    	</div>
    </div>

    <div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10">
    	   <input type="submit" class="btn btn-primary" value="Add" name="addPerm" />
    	</div>
    </div>  
        

</form>
{else}
<div class="alert alert-info" role="alert">All controllers already added.</div>
{/if}
</div></div>
