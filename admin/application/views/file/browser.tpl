{literal}
<script type="text/javascript">
$(document).ready( function() {
    $('#commander').fileTree({
            root: '{/literal}{$browser.site_dir}{literal}',
            script: admin_dir+'/files/filetree/'
        }, function(file) {
            alert(file);

    });
});
</script>
{/literal}
<h1>File Manager</h1>

<fieldset>
    <legend>/root/</legend>

    <div id="commander">

    </div>

</fieldset>