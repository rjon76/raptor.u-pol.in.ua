<div class="panel panel-default">
	<div class="panel-heading">

        <ul class="nav nav-pills pull-right">
	       <li role="presentation"><a href="{$ADMIN_DIR}/users/list/">Users list</a></li>
        </ul>
        
        <h2 class="panel-title">Edit Group</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

<legend>Group permissions</legend>
{if $groups.permissions}
<form action="" method="post">
<table class="table table-hover table-condensed">
<thead>
<tr>
    <th>Controller</td>
    <th class="text-center">Read</th>
    <th class="text-center">Write</th>
    <th class="text-center">Delete</th>
    <th class="text-right">Options</th>
</tr>
</thead>
<tbody>
{foreach from=$groups.permissions item=perm}
<tr>
    <td>{$perm.c_menu_name}</td>
    <td class="text-center"><input type="checkbox" name="{$perm.gc_id}_read" {if $perm.read}checked="checked"{/if}/></td>
    <td class="text-center"><input type="checkbox" name="{$perm.gc_id}_write" {if $perm.write}checked="checked"{/if}/></td>
    <td class="text-center"><input type="checkbox" name="{$perm.gc_id}_delete" {if $perm.delete}checked="checked"{/if}/></td>
    <td class="text-right"><a href="{$ADMIN_DIR}/users/gdelperm/group/{$perm.gc_group_id}/cont/{$perm.gc_controller_id}/">delete</a></td>
</tr>
</tbody>
{/foreach}
<tfoot>
<tr>
    <td colspan="5" class="text-right">
        <input type="submit" class="btn btn-primary" value="Update changes" name="updatePerms" />
    </td>
</tr>
</tfoot>
</table>
</form>
{else}
<div class="alert alert-info" role="alert">No any permissions for this Group.</div>

{/if}

<legend>Add permission</legend>
{if $groups.controllers}
<form action="" method="post" class="form-horizontal">

    <div class="form-group">
    	<label class="col-sm-2 control-label">Controller</label>
    	<div class="col-sm-10">
            <select name="controller" class="form-control">
            {html_options options=$groups.controllers}
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
    			<label>
    			<input type="checkbox" name="write" /> Write 
    			</label>
    		</div>
    	</div>
    </div>
    
    <div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10">
    		<div class="checkbox">
    			<label>
    			<input type="checkbox" name="delete" /> Delete 
    			</label>
    		</div>
    	</div>
    </div>
            
    
    <div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10">
    	   <input type="submit" class="btn btn-primary" value="Add new" name="addPerm" />
    	</div>
    </div>        

</form>
{else}
<div class="alert alert-info" role="alert">All controllers already added.</div>
{/if}

    </div>
</div>