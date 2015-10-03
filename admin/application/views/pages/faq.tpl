<div id="area-faq">
    <h1>FAQ</h1>
    
    <dl id="globalExt">
        <dt>How to use the global expansion? <a title="Copy this link" class="clickCopy" href="http://{$header.adminHost}/{$header.curController}/faq/#globalExt">copy link</a></dt>
        <dd>
        Anywhere in the text to insert, exp: <span class="note info">{literal}{globalExt method=getDownloads params=143}{/literal}</span>, where
        <p>
        - <span class="note params">@params</span> - options for sending (is there product ID); <br /> 
        - <span class="note params">@method</span> - necessary action (is there get the number of the product download).
        </p>
            <ul>
            <li><strong>Available methods:</strong>
                <ul>
                    <li>getDownloads - get the number of downloads(@params = product ID*)</li>
                    <li>getPrice - get product price (@params = product ID*, license ID), if you do not specify a license ID will be used by default license</li>
                    <li>getProductName - get product name (@params = product ID*)</li>
                </ul>
            </li>
            </ul>
        </dd>
    </dl>
    
    <dl id="variableArray">
        <dt>How to fill a variable whose type "Array", "Image", "Flash"? <a title="Copy this link" class="clickCopy" href="http://{$header.adminHost}/{$header.curController}/faq/#variableArray">copy link</a></dt>
        <dd>
        If the variable is of type "Array", "Image", "Flash" is to be used next typing filling:  <span class="note info">&lt;i key="key"&gt;value&lt;/i&gt;</span>, get an array of <span class="note params">key = value</span>. Exp.:
        <p>

        &lt;i key="buynow"&gt;<br />
           &lt;i key="href"&gt;/contacts.html&lt;/i&gt;<br />
           &lt;i key="attr"&gt;style="margin-top:10px;"&lt;/i&gt;<br />
        &lt;/i&gt;
        <br /><br />
        or
        <br /><br />

        &lt;i key="src"&gt;/images/logo.png&lt;/i&gt;<br />
        &lt;i key="width"&gt;560&lt;/i&gt;<br />
        &lt;i key="height"&gt;100&lt;/i&gt;<br />
        &lt;i key="alt"&gt;The best product&lt;/i&gt;<br />
        &lt;i key="title"&gt;The best product&lt;/i&gt;<br />
        &lt;i key="class"&gt;image2x&lt;/i&gt;<br />

        </p>

        <ul>
            <li><strong>when filling array by default taken such names keys:</strong>
                <ul>
                    <li>"href" - address of a page or file</li>
                    <li>"src" - image address</li>
                    <li>"attr" - tag attributes (example: data-src="logo.png")</li>
                    <li>"class" - class tag</li>
                    <li>"width" - width object(image, iframe ...)</li>
                    <li>"height" - height object(image, iframe ...)</li>
                    <li>"active" - use object (true or false)</li>
                </ul>
            </li>
            </ul>
        </dd>
    </dl>
    
    <dl id="useClass">
        <dt>How to use classes (styles) <a title="Copy this link" class="clickCopy" href="http://{$header.adminHost}/{$header.curController}/faq/#useClass">copy link</a></dt>
        <dd>
        <p>
        When using Bootstrap can use the following classes (styles) in the content:
        </p>
        <ul>
            <li><strong>Using grid system(12 columns)</strong>
                <ul>
                    <li>...</li>
                </ul>
            </li>
        </ul>
        <ul>
            <li><strong>Using styles for buttons</strong>
                <ul>
                    <li>...</li>
                </ul>
            </li>
        </ul>
        <ul>
            <li><strong>Using tables</strong>
                <ul>
                    <li>...</li>
                </ul>
            </li>
        </ul>
        
        <ul>
            <li><strong>Using span</strong>
                <ul>
                    <li>...</li>
                </ul>
            </li>
        </ul>
        
        <ul>
            <li><strong>Using images</strong>
                <ul>
                    <li>...</li>
                </ul>
            </li>
        </ul>
        <p>
        When using styles.css:
        </p>
        <ul>
            <li><strong>Using box(size)</strong>
                <ul>
                    <li>...</li>
                </ul>
            </li>
        </ul>
    
        <ul>
            <li><strong>Using icons font</strong>
                <ul>
                    <li>...</li>
                </ul>
            </li>
        </ul>
        <ul>
            <li><strong>Using backgroud-color, background-gradient, background-image, separated.</strong>
                <ul>
                    <li>...</li>
                </ul>
            </li>
        </ul>
        </dd>
    </dl>

</div>

{literal}
<script type= "text/javascript">
if (window.jQuery) {
$(document).ready(function() {
    
    $('#area-faq dt').bind('click', function(){
        
        var _dd = $(this).next('dd');
        
        if (_dd.is(':hidden'))
        {
            _dd.fadeIn("fast");
            $(this).addClass('active');
            
        }else{
            _dd.fadeOut("fast");
            $(this).removeClass('active');
        }
    });
      
	
    function copyToClipboard(text) {
        alert("Copy URL to clipboard (Ctrl+C): " + text);
    }
    
    $('.clickCopy').click(function() {
        
        copyToClipboard($(this).attr('href'));
        return false;
    });    
          
      
        
});
}
</script>
<style type="text/css">
dl{}
dt{cursor:pointer;background-color: #e5f3ff;padding:5px;font-size:16px;color:#303030;
	-webkit-transition: all 0.2s linear;
	-o-transition: all 0.2s linear;
	-moz-transition: all 0.2s linear;
	-ms-transition: all 0.2s linear;
	-kthtml-transition: all 0.2s linear;
	transition: all 0.2s linear;
}
dt.active,dt:hover{background-color: #bddbf4;color:#101010;}
dd{display:none;padding:10px;font-size:14px;line-height:18px;color:#101010}
dd ul{margin:10px 0px;padding:0;list-style: none outside none;}
dd ul li{line-height:21px;margin-bottom:5px;}
dd ul li ul{margin-left:35px;}
dd ul li ul li:before{content:"";}
span.note{
    display: inline-block;
    font-family: monospace;
}
span.info{
    color:#006633;
}
span.params{
    color:#000;
}
span.error{
    color:#fa0000;
}
a.clickCopy{font-family: monospace;font-size:13px;float:right;display:inline-block;text-decoration:none; border-bottom:1px dotted #abcae5; color:#abcae5;
	-webkit-transition: all 0.2s linear;
	-o-transition: all 0.2s linear;
	-moz-transition: all 0.2s linear;
	-ms-transition: all 0.2s linear;
	-kthtml-transition: all 0.2s linear;
	transition: all 0.2s linear;
}
a.clickCopy:hover{border-bottom:1px dotted #101010; color:#101010;}
</style>

{/literal}