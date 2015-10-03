{literal}
<script type="text/javascript">

if (window.jQuery) {
    
    var $182 = jQuery.noConflict(); 
    
    $(document).ready(function() {
    
        (function ($) {   
                    $182.datepicker.setDefaults({
                        showOn: 'both',
                        buttonImageOnly: true,
                        buttonImage: admin_dir+'/images/calendar.gif',
                        buttonText: 'Calendar'
                    });

                    $182('#end_date').datepicker({dateFormat: 'yy-mm-dd'});
        })(jQuery);
      
    });
}


</script>
{/literal}

<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Add new coupon</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">
    

    <form action="" method="post" class="form-horizontal">
        <div class="form-group">
        	<label class="col-sm-2 control-label">Name</label>
            <div class="col-sm-8">
            <input type="text" name="name" class="form-control" />
            </div>
         </div>

        <div class="form-group">
        	<label class="col-sm-2 control-label">Code</label>
            <div class="col-sm-8">
            <input type="text" name="code" class="form-control" />
            </div>
         </div>

        <div class="form-group">
        	<label class="col-sm-2 control-label">Discount</label>
            <div class="col-sm-8">
            <input type="text" name="percent" class="form-control" />
            </div>
         </div>

        <div class="form-group">
        	<label class="col-sm-2 control-label">End date</label>
            <div class="col-sm-8">
                <input id="end_date" type="text" name="date" class="form-control" placeholder="yy-mm-dd" />  
         </div>
         </div>

        <div class="form-group">
        	<label class="col-sm-2 control-label">Operator</label>
            <div class="col-sm-8">
                <select name="operator" class="form-control">
                    {foreach from=$purchase.operatorsList item=operator}
                    <option value="{$operator.op_id}">{$operator.op_name}</option>
                    {/foreach}
                </select>
                </div>
         </div>

            <div class="form-group">
        	   <label class="col-sm-2 control-label">Valid licenses</label>
               <div class="col-sm-8">
                <select name="valid_licenses[]" class="form-control" multiple="multiple" size="12">
                    {foreach from=$purchase.productsList.prods item=category key=cat_name}
                    {foreach from=$category item=prod}
                    {if $purchase.licensesList[$prod.p_id]}
                        <optgroup label="{$prod.p_title}">
                        {foreach from=$purchase.licensesList[$prod.p_id] item=license}
                        <option value="{$license.l_id}">{$license.l_name}</option>
                        {/foreach}
                        </optgroup>
                    {/if}
                    {/foreach}
                    {/foreach}
                </select>
                </div>
            </div>

            <div class="form-group">
            	<label class="col-sm-2 control-label">Unvalid licenses</label>
                <div class="col-sm-8">
                <select name="unvalid_licenses[]" class="form-control" multiple="multiple" size="12">
                    {foreach from=$purchase.productsList.prods item=category key=cat_name}
                    {foreach from=$category item=prod}
                    {if $purchase.licensesList[$prod.p_id]}
                        <optgroup label="{$prod.p_title}">
                        {foreach from=$purchase.licensesList[$prod.p_id] item=license}
                        <option value="{$license.l_id}">{$license.l_name}</option>
                        {/foreach}
                        </optgroup>
                    {/if}
                    {/foreach}
                    {/foreach}
                </select>
                </div>
             </div>


        <div class="form-group">
        	<div class="col-sm-offset-2 col-sm-8">
        	   <input type="submit" name="addCoupon" value="Add new coupon" class="btn btn-primary" />
        	</div>
        </div>
        

    </form>
        </div>
</div>