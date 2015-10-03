<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Edit field</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

<form action="" method="post" class="form-horizontal">
    <div class="form-group">
    	<label class="col-sm-2 control-label">Name</label>
    	<div class="col-sm-6">
            <input type="text" name="name" class="form-control" value="{$content.val.name}" />
    	</div>
    </div>

    <div class="form-group">
    	<label class="col-sm-2 control-label">Default value</label>
    	<div class="col-sm-6"> 
            <textarea name="default" class="form-control" rows="6">{$content.val.default}</textarea>
    	</div>
    </div>
       
    <div class="form-group">
    	<label class="col-sm-2 control-label">Type</label>
    	<div class="col-sm-6">
            <select class="form-control" name="type">
                <option value="S" {if $content.val.type == 'S'}selected="selected"{/if}>String</option>
                <option value="A" {if $content.val.type == 'A'}selected="selected"{/if}>Array</option>
                <option value="J" {if $content.val.type == 'J'}selected="selected"{/if}>Json Array</option>
                <option value="G" {if $content.val.type == 'G'}selected="selected"{/if}>Global Extension</option>
                <option value="E" {if $content.val.type == 'E'}selected="selected"{/if}>Extension</option>
                <option value="I" {if $content.val.type == 'I'}selected="selected"{/if}>Image</option>
                <option value="W" {if $content.val.type == 'W'}selected="selected"{/if}>Flash</option>
                <option value="L" {if $content.val.type == 'L'}selected="selected"{/if}>List</option>
            </select>
    	</div>
    </div>

    <div class="form-group">
    	<div class="col-sm-offset-2 col-sm-6">
   	        <input type="submit" value="Update changes" class="btn btn-primary" name="editField" />
    	</div>
    </div>
   

</form>
    </div>
</div>