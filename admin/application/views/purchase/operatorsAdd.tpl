<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Add new operator</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

        <form action="" method="post" class="form-horizontal">

            <div class="form-group">
            	<label class="col-sm-2 control-label">Name</label>
            	<div class="col-sm-10">
             	<input type="text" name="name" class="form-control" />
            	</div>
            </div>
            
            <div class="form-group">
            	<label class="col-sm-2 control-label">Link</label>
            	<div class="col-sm-10">
           	        <input type="text" name="link" class="form-control" />
            	</div>
            </div>
            
            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            		<div class="checkbox">
            			<label>
                            <input type="checkbox" name="default" /> Default	
            			</label>
            		</div>
            	</div>
            </div>

            {foreach from=$operators.languages item=lang}
            <div class="form-group">
            	<label class="col-sm-2 control-label">{$lang.l_code|upper} ID</label>
            	<div class="col-sm-10">
                    <input type="text" name="lang_{$lang.l_code}_id" class="form-control" style="width:100px;" />
            	</div>
            </div>
            {/foreach}
            
            <div class="form-group">
            	<div class="col-sm-offset-2 col-sm-10">
            	   <input type="submit" name="addOperator" value="Add new" class="btn btn-primary" />
            	</div>
            </div>
                    
        </form>
    </div>
</div>