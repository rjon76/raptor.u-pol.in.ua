<div class="panel panel-default">
	<div class="panel-heading">
        <h2 class="panel-title">Add new field</h2>
        <div class="clearfix"></div>
    </div>
    <div class="panel-body">

<form action="" method="post" class="form-horizontal">
    <div class="form-group">
    	<label class="col-sm-2 control-label">Name</label>
    	<div class="col-sm-6">
            <input type="text" name="name" class="form-control" />
    	</div>
    </div>
    <div class="form-group">
    	<label class="col-sm-2 control-label">Default value</label>
    	<div class="col-sm-6">
            <textarea name="default" class="form-control" rows="6"></textarea>
    	</div>
    </div>
    <div class="form-group">
    	<label class="col-sm-2 control-label">Type</label>
    	<div class="col-sm-6">
        <select class="form-control" name="type">
            <option value="S">String</option>
            <option value="A">Array</option>
            <option value="J">Json Array</option>
            <option value="G">Global Extension</option>
            <option value="E">Extension</option>
            <option value="I">Image</option>
            <option value="W">Flash</option>
            <option value="L">List</option>
        </select>
    	</div>
    </div>

    <div class="form-group">
    	<div class="col-sm-offset-2 col-sm-6">
            <input type="submit" class="btn btn-primary" value="Add new" name="addField" />	
    	</div>
    </div>

    

</form>

        <div class="alert alert-info" role="alert">
            <dl>
                <dt>For Array,Image and Flash use the following syntax input: &lt;i key="key"&gt;value&lt;/i&gt;</dt>
                <dd>
                    <ul>
                        <li><strong>Where:</strong>
                            <ul>
                                <li><span class="note params">key</span> - key</li>
                                <li><span class="note params">value</span> - value</li>
                            </ul>
                        </li>
                    </ul>
                </dd>
            </dl>
        </div>
        
    </div>
</div>