<fieldset>
    <legend>Uploader</legend>

        <div id="demo">

        <form action="{$ADMIN_DIR}/fency.upload.php" method="post" enctype="multipart/form-data" id="form-demo" class="form">
	<fieldset id="demo-fallback">
		<legend>File Upload</legend>
		<p>
			Selected your photo to upload.<br />
			<strong>This form is just an example fallback for the unobtrusive behaviour of FancyUpload.</strong>
		</p>
		<label for="demo-photoupload">
			Upload Photos:
			<input type="file" name="photoupload" id="demo-photoupload" />
		</label>
	</fieldset>

	<div id="demo-status" class="hide">
		<p>
			<a href="#" id="demo-browse-all">Browse Files</a> |
			<a href="#" id="demo-browse-images">Browse Only Images</a> |
			<a href="#" id="demo-clear">Clear List</a> |
			<a href="#" id="demo-upload">Upload</a>

		</p>
                Directory: (<a href="javascript:void(0);" class="sctrl" onclick="alert('Не реализовано!');">Open browser</a>)<br/>
                <input type="text" size="20" name="directory" id="directory" value="/images/" class="text" />
		<div>
			<strong class="overall-title">Overall progress</strong><br />
			<img src="{$ADMIN_DIR}/images/upload/bar.gif" class="progress overall-progress" />
		</div>
		<div>
			<strong class="current-title">File Progress</strong><br />
			<img src="{$ADMIN_DIR}/images/upload/bar.gif" class="progress current-progress" />
		</div>
		<div class="current-text"></div>
	</div>

	<ul id="demo-list"></ul>

        </form>
	</div>
</fieldset>