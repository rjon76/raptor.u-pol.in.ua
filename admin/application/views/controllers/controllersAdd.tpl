<div class="panel panel-default">
	<div class="panel-heading">
        <h2 id="controllersAdd" class="panel-title">Add new Controller</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
        <form action="{$ADMIN_DIR}/controllers/list/" method="post" class="form-horizontal">

            <div class="form-group">
            	<label class="col-sm-2 control-label">Controller name</label>
            	<div class="col-sm-10">
                	<input type="text" name="cont_name" class="form-control" value="{$conts.val.contName}" />
                    <span class="help-block">
                    {if $conts.err.contName}<span style="color:red;">(controller name is empty)</span><br />{/if}
                    {if $conts.err.contNameExist}<span style="color:red;">(controller name is already exist)</span>{/if}
                    </span>
            	</div>
            </div>
            
            <div class="form-group">
            	<label class="col-sm-2 control-label">Controller menu name</label>
            	<div class="col-sm-10">
                	<input type="text" name="cont_menu_name" class="form-control" value="{$conts.val.contMenuName}" />
                    <span class="help-block">
                        {if $conts.err.contMenuName}<span style="color:red;">(controller menu name is empty)</span>{/if}
                    </span>
            	</div>
            </div>

            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            		<div class="checkbox">
            			<label>
            			     <input type="checkbox" name="is_site_dependent" /> Is site dependent	
            			</label>
            		</div>
            	</div>
            </div>
                    
            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            	   <input type="submit" class="btn btn-primary" value="Add new" name="contadd" />
            	</div>
            </div>
                    
        </form>
    </div>
</div>