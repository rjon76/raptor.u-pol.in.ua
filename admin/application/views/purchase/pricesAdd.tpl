    <legend>Add new license</legend>

    <form action="" method="post" class="form-horizontal">

                <div class="form-group">
                    <label class="col-sm-2 control-label">License name</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="name" />
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-sm-2 control-label">Parent License</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="parent">
                            <option></option>
                        {foreach from=$purchase.licensesList item=license}
                            <option value="{$license.l_id}">{$license.l_name}</option>
                        {/foreach}
                        </select>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-sm-2 control-label">Type</label>
                    <div class="col-sm-10">
                        <select class="form-control" name="type">
                            <option value="H">Home</option>
                            <option value="B">Business</option>
                            <option value="S">Single License</option>
                            <option value="SL">Single License [for 1 Computer]</option>
                            <option value="C">Company License</option>
                            <option value="FN">For non-commercial use</option>
                            <option value="FC">For commercial use</option>
                            <option value="FE">For end users</option>
                            <option value="LP">License packs</option>
                        </select>
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-sm-2 control-label">Price (USD)</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="price"/>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="col-sm-2 control-label">Max user number</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="usernumber" value="1" />
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-sm-2 control-label">Min user number</label>
                    <div class="col-sm-10">
                        <input type="text" class="form-control" name="min_usernumber" value="1" />
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-sm-2 control-label">Users in one licese</label>
                    <div class="col-sm-10">
                        <input type="text" name="users_in_license" class="form-control" value="1" />
                    </div>
                </div>
            
                <div class="form-group">
                    <label class="col-sm-2 control-label">Wiki link</label>
                    <div class="col-sm-10">
                        <input type="text" name="wiki_link" class="form-control" value="" />
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" name="default"/> is default
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-sm-10 col-sm-offset-2">
                        <input type="submit" value="Add license" class="btn btn-primary" name="addLicense" />
                    </div>
                </div>
    </form>