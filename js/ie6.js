
$(document).ready(function() {

	DD_belatedPNG.fix('.png');

 $("#page_langs").bind('mouseover', function() {

        $('#langsdiv').css('display','block');

    });	

 $("#page_langs").bind('mouseout', function() {

        $('#langsdiv').css('display','none');

    });
})
