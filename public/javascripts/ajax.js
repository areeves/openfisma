String.prototype.trim = function() {
        return this.replace(/^\s+|\s+$/g,"");
}

$(document).ready(function(){

   $('input.date').datepicker({dateFormat:'yymmdd',showOn: 'both', buttonImageOnly: true,
        buttonImage: '/images/calendar.gif', buttonText: 'Calendar'});
        

    $("select[name='system']").change(function(){
        searchAsset();
    });

    asset_detail();
    
    $("input#search_asset").click(function(){
        searchAsset();
    });

    $("input#search_product").click(function(){
        searchProduct();
    });

    getProdId();

    $("input#all_finding").click(function(){
        $('input[@type=checkbox]').attr('checked','checked');
    });

    $("input#none_finding").click(function(){
        $('input[@type=checkbox]').removeAttr('checked');
    });

    $("span.editable").click(function(){
        var name = $(this).attr('name');
        var type = $(this).attr('type');
        var url = $(this).attr('href');
        var class = $(this).attr('class');
        class = class.replace(/editable/i, '');
        var cur_val = $(this).text();
        var cur_span = $(this);
        if(type == 'text'){
            cur_span.replaceWith( '<input name='+name+' class="'+class+'" type="text" value="'+cur_val.trim()+'" />');
        }else if( type == 'textarea' ){
            var row = $(this).attr('rows');
            var col = $(this).attr('cols');
            cur_span.replaceWith( '<textarea rows="'+row+'" cols="'+col+'" name="'+name+'">'+
                    cur_val.trim()+ '</textarea>');
        }else{
            $.get(url,{value:cur_val.trim()},
            function(data){
                if(type == 'select'){
                    cur_span.replaceWith('<select name="'+name+'">'+data+'</select>');
                }
            });
        }
        $('input.date').datepicker({dateFormat:'yymmdd',showOn: 'both', buttonImageOnly: true,
                buttonImage: '/images/calendar.gif', buttonText: 'Calendar'});
    });

});

function searchAsset( ){
    var trigger = $("select[name='system']");
    var sys = trigger.children("option:selected").attr('value');
    var param =  '';
    if( null != sys){
        param +=  '/sid/' + sys;
    }
    $("input.assets").each(function(){
        if( $(this).attr('value') ){
            param += '/' + $(this).attr('name') + '/' + $(this).attr('value');
        }
    });
    var url = trigger.attr("url") + param ;
    $("select[name='asset_list']").parent().load(url,null,function(){
        asset_detail();
    });
}

function asset_detail() {
    $("select[name='asset_list']").change(function(){
        var url = '/zfentry.php/asset/detail/id/'+ $(this).children("option:selected").attr('value');
        $("div#asset_info").load(url,null);
    });
}


function upload_evidence(){
    //$("#up_evidence").blur();
    var dw = $(document).width();
    var dh = $(document).height();
    $('<div id="full"></div>')
                .width(dw).height(dh)
                .css({backgroundColor:"#000000", marginTop:-1*dh, opacity:0, zIndex:10})
                .appendTo("body").fadeTo(1, 0.4);
    var content = $("#editorDIV").html();
    $('<div title="Upload Evidence"></div>').append(content).
        dialog({position:'top', width: 540, height: 200, resizable: true,modal:true,
            close:function(){
                $('#full').remove();
            }
        });
    return false;
}

function comment(formname){
    var dw = $(document).width();
    var dh = $(document).height();
    $('<div id="full"></div>')
                .width(dw).height(dh)
                .css({backgroundColor:"#000000", marginTop:-1*dh, opacity:0, zIndex:10})
                .appendTo("body").fadeTo(1, 0.4);
    var content = $("#comment_dialog").html();
    $('<div title="Upload Evidence"></div>').append(content).
        dialog({position:'top', width: 540, height: 240, resizable: true,modal:true,
            close:function(){
                $('#full').remove();
            },
            buttons:{
                'Cancel':function(){
                    $(this).dialog("close");
                },
                'Continue':function(){
                    var form1 = formname;
                    var topic = $("input[name=topic]",this).val();
                    var reason = $("textarea[name=reason]",this).val();
                    form1.elements['topic'].value = topic;
                    form1.elements['reject'].value = reason;
                    form1.elements['decision'].value = 'DENY';
                    form1.submit();
                }
            }
        });
}

function getProdId(){
    var trigger= $("select[name='prod_list']");
    trigger.change(function(){
        var prod_id= trigger.children("option:selected").attr('value');
        $("input[name='prod_id']").val(prod_id);
    });
}

function searchProduct(){
    var trigger = $("input#search_product");
    var url = trigger.attr('url');
    url += '/view/list';
    $("input.product").each(function(){
        if($(this).attr('value')){
            url += '/' + $(this).attr('name') + '/' + $(this).attr('value');
        }
    });
    $("select[name='prod_list']").parent().load(url,null,function(){
        getProdId();
    });
}

function message( msg ,model){
    $("#msgbar").html(msg).css('font-weight','bold');
    if( model == 'warning')  $("#msgbar").css('color','red');
    else $("#msgbar").css('color','green');
}
