/**
 * dashboard/js/defaul.js
 * Model-View-Controller js File
 * 
 * @package MVC
 * @subpackage View
 * @author Carlos Vallory <carlvallory@gmail.com>
 **/

$(function(){
    $.get("dashboard/xhrGetListings", function(o) {
        
        for (var i = 0; i < o.length; i++)
        {
            $('#listInserts').append('<div>' + o[i].text + '<a class="del" rel="'+o[i].id+'" href="#">X</a></div>');
        }
        
        $(document).on('click', '.del', function() {
            delItem = $(this);
            var id = $(this).attr('rel');
            
            $.post('dashboard/xhrDeleteListing', {'id': id}, function(o) {
                
            }, "json");
            if ( delItem.parent().is( "div" ) ) {
                delItem.parent("div").remove();
                //console.log("div");
            } else {
                //console.log("no div");
            }
            return false;
        });
        
    },'json');
    
    
    $('#randomInsert').submit(function(){
        var url = $(this).attr('action');
        var data = $(this).serialize();
        
        $.post(url, data, function(o){
            $('#listInserts').append('<div>' + o.text + '<a class="del" rel="' + o.id + '" href="#">X</a></div>')
        },'json');
        
        
        return false;
    });
});
