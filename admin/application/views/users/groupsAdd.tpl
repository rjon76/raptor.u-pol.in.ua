<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Add new Group</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    

<form action="{$ADMIN_DIR}/users/list/" method="post" class="form-horizontal">


    <div class="form-group">
    	<label class="col-sm-2 control-label">Group name</label>
    	<div class="col-sm-10">
        <input type="text" name="group_name" class="form-control" value="{$users.val.groupName}" />
        <span class="help-block">
            {if $users.err.groupName}<span style="color:red;">(group name is empty)</span>{/if}
            {if $users.err.groupNameExist}<br /><span style="color:red;">(group name is already exist)</span>{/if}
            </span>	
    	</div>
    </div>
    
    <div class="form-group">
    	<div class="col-sm-offset-2 col-sm-10">
            <input type="submit" class="btn btn-primary" value="Add" name="groupadd" />	
    	</div>
    </div>


</form>
    </div>
</div>
