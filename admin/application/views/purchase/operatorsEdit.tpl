<div class="panel panel-default">
	<div class="panel-heading">
            <ul class="nav nav-pills pull-right">
            <li role="presentation"><a href="{$ADMIN_DIR}/purchase/operators/"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Back</a></li>
    	</ul>
        <h2 class="panel-title">Edit operator / {$operator.val.op_name}</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">


    <form action="" method="post" class="form-horizontal">
        <div class="form-group">
        	<label class="col-sm-2 control-label">Name</label>
        	<div class="col-sm-10">
                <input type="text" name="name" class="form-control" value="{$operator.val.op_name}" />
        	</div>
        </div>
        
        <div class="form-group">
        	<label class="col-sm-2 control-label">Link</label>
        	<div class="col-sm-10">
                <input type="text" name="link" class="form-control" value="{$operator.val.op_link}" />
        	</div>
        </div>

        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-10">
        		<div class="checkbox">
        			<label>
                        <input type="checkbox" name="default" {if $operator.val.op_default == 'Y'}checked="checked"{/if} /> Default	
        			</label>
        		</div>
        	</div>
        </div>
        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-10">
        		<div class="checkbox">
        			<label>
                        <input type="checkbox" name="blocked" {if $operator.val.op_blocked == 'Y'}checked="checked"{/if} /> Blocked
        			</label>
        		</div>
        	</div>
        </div>

        {foreach from=$operator.languages item=lang}
        <div class="form-group">
        	<label class="col-sm-2 control-label">{$lang.l_code|upper} ID</label>
        	<div class="col-sm-10">
                <input type="text" name="lang_{$lang.l_code}_id" class="form-control" style="width:100px;" value="{$operator.val.op_langs[$lang.l_code]}" />
        	</div>
        </div>
        {/foreach}

        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-10">
        	   <input type="submit" name="updateOperator" value="Updat changes" class="btn btn-primary" />
        	</div>
        </div>
                

    </form>
    </div>
</div>