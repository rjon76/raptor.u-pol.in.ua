if (window.jQuery) {
$(document).ready(function () {

    
(function ($) {  
    
    var adminPanel = $('#adminPanel');
    var adminBlock = $('.adminBlock');
    var adminBtnClose = adminPanel.find('.admin-btn-close');
    var adminPanelInner = adminPanel.find('.inner');
     
    adminBtnClose.bind('click',function(){
        
        if (adminPanelInner.is(':hidden')){
            adminPanelInner.fadeIn("fast");
            adminBlock.fadeIn("fast");
            $(this).addClass('active');
        }
        else{
            adminPanelInner.fadeOut("fast");
            adminBlock.fadeOut("fast");
            $(this).removeClass('active');
        }
        
        return false;
    });
    
    var adminEditNote = $('.adminEditNote');
    var adminEditBtnClose = adminEditNote.find('.admin-btn-close');
    //var adminEditBtnCloseNote = adminEditNote.find('.admin-btn-closeNote');
    
    var iteration = 100;
    adminEditNote.each(function(){
        $(this).css({'z-index':iteration++});
        
        var _parent = $(this).parent();
        
        if (_parent.length>0){
            var parentTagName = _parent.get(0).tagName.toLowerCase();
        }
        
        var _next = $(this).next();
        
        if (_next.length>0){
            nextTagName = _next.get(0).tagName.toLowerCase();
        }
        
        switch(parentTagName){
            
            case "li":
                break;
            case "table":
                _parent.after($(this));
                break;
            case "ul":
                _parent.next('li').append($(this));
                break;
        }
        
        switch(nextTagName){
            
            case "li":
                _next.append($(this));
                break;
            
            case "div":
            
                break;   

        }
        
    });
 
    adminEditBtnClose.bind('click',function(){
        var _link = $(this);
        
        var adminEditNoteInner = $(this).parent().find('.inner');
        if (adminEditNoteInner.is(':hidden')){
            adminEditNoteInner.fadeIn("fast");
            _link.addClass('active');
        }
        else{
            adminEditNoteInner.fadeOut("fast");
            _link.removeClass('active');
        }
        
        return false;
    });

    var toggleLink = adminPanel.find('.toggle');    
    toggleLink.bind('click',
        function(){

            var _type = $(this).attr('data-type');
            
            if (_type !== undefined && _type != ""){
                
                
                switch(_type){
                    
                    case "console":
                    
                        toggleLink.next('.count').html('('+adminEditNote.length+')');
                            
                            var _link = $(this);
                            
                            $('.adminEditNote').each(function(){
                                if ($(this).is(':hidden'))
                                {
                                    $(this).fadeIn("fast", function(){
                                        _link.find('span').addClass('visible').html('hide');
                                    });
                                }else{
                                    $(this).fadeOut("fast", function(){
                                        _link.find('span').removeClass('visible').html('show');
                                    });
                                }
                    
                            });

                        break;
                        
                    default:
                    
                        if (adminBlock.find('.inner.'+_type).is(':hidden')){
                            adminBlock.show().find('.inner.'+_type).show();
                            $(this).find('span').addClass('visible').html('hide');
                        }
                        else{ 
                            adminBlock.show().find('.inner.'+_type).hide();
                            $(this).find('span').removeClass('visible').html('show');
                        }
                        
                            break;
 
                }

            }
            
            return false;
            
        });
        

})(jQuery);    
         
});
}
