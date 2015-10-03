<div class="panel panel-default">
	<div class="panel-heading">
        <ul class="nav nav-pills pull-right">
            <li role="presentation"><a href="{$ADMIN_DIR}/purchase/additional/"><span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> Back</a></li>
    	</ul>
        <h2 class="panel-title">Edit additional offer</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    

    <form action="" method="post" class="form-horizontal">
        
        <div class="form-group">
        	<label class="col-sm-2 control-label">Text</label>
        	<div class="col-sm-10">
                <input type="text" name="text" class="form-control" value="{$purchase.val.af_text}" />
        	</div>
        </div>

                
        <div class="form-group">
        	<label class="col-sm-2 control-label">Default price</label>
        	<div class="col-sm-10">
                <input type="text" name="default_price" class="form-control" value="{$purchase.val.af_default_price}" />
        	</div>
        </div>
                
        <div class="form-group">
        	<label class="col-sm-2 control-label">Percent form total price</label>
        	<div class="col-sm-10">
                <input type="text" name="price_percent" class="form-control" value="{$purchase.val.af_price_percent}" />
        	</div>
        </div>
                
        <div class="form-group">
        	<label class="col-sm-2 control-label">Element5 (ID)</label>
        	<div class="col-sm-10">
                <input type="text" name="element5_contract_id" class="form-control"  value="{$purchase.val.af_contract_id}" />
        	</div>
        </div>
                
        <div class="form-group">
        	<label class="col-sm-2 control-label">Plimus (ID)</label>
        	<div class="col-sm-10">
                <input type="text" name="plimus_contract_id" class="form-control"  value="{$purchase.val.af_contract_id}" />
        	</div>
        </div>
                
        <div class="form-group">
        	<label class="col-sm-2 control-label">CleverBrige (ID)</label>
        	<div class="col-sm-10">
                <input type="text" name="cleverbrige_contract_id" class="form-control" value="{$purchase.val.af_contract_id}" />
        	</div>
        </div>

        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-10">
        	   <input type="submit" value="Update changes" class="btn btn-primary" name="updateFeature" />
        	</div>
        </div>

    </form>
    </div>
</div>