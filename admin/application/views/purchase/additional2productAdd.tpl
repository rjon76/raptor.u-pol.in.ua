<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Add additional offer to product</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

    <form action="" method="post" class="form-inline">
        <div class="form-group">
        	<label>Additional feature</label>                
                <select name="feature" class="form-control">
                    {foreach from=$purchase.features item=feature}
                    <option value="{$feature.af_id}">{$feature.af_text}</option>
                    {/foreach}
                </select>
         </div>

        <input type="submit" value="Add" name="addOffer" class="btn btn-primary" />

    </form>
    </div>
</div>