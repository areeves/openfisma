$(document).ready(function(){

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

 function edit( identity ){
        var obj = $("#"+identity);
        var modify = obj.find("span.sponsor").html();
        obj.find("span.sponsor").click(
            function(){
                type = obj.attr('type');
                contenter = obj.find("span.contenter");
                init_value = contenter.html();
                if( 'text' == type ){
                    size = obj.attr('size');
                    obj.html( '<input type="text" value="'+init_value+'" size="'+size+'" />' );
                }
                if( 'textarea' == type ){
                    rows = obj.attr('rows');
                    cols = obj.attr('cols');
                    obj.html( '<textarea rows="'+rows+'" cols="'+cols+'">'+init_value+'</textarea>' );
                }
                if( 'select' == type ){
                    option = obj.attr("option");
                    option_obj =  eval('(' + option + ')');
                    str = '<select>';
                    for ( x in option_obj ){
                        str += '<option value="'+x+'">'+option_obj[x]+'</option>';
                    }
                    str += '</select>';
                    obj.html( str );
                }
                obj.children(':first').focus().blur(
                    function(){
                        if( 'text' == type || 'textarea' == type ){
                            value = $(this).attr("value");
                            save( identity, value, modify);
                        }
                        if( 'select' == type ){
                            key = $(this).val();
                            value = option_obj[key];
                            save( identity, value, modify,key );
                        }
                    }
                );
            }
        )
    };
    function save( identity, value, modify, key ){
        obj = $("#"+identity);
        name = obj.attr('name');
        data = key ? key : value;
        $("input[name="+name+"]").val( data );
        Template = '<span class="sponsor">#{modify}</span>&nbsp;<span class="contenter">#{value}</span>';
        str = Template.replace(/#\{modify\}/, modify ).replace(/#\{value\}/, value);
        obj.html( str );
        edit( identity );
    }

    $(document).ready(function(){
        edit( 'system' );
        edit( 'recommendation' );
        edit( 'poam_type' );
        edit( 'description' );
        edit( 'resources' );
        edit( 'date_est' );
        edit( 'blscr' );
        edit( 'threat' );
        edit( 'source' );
        edit( 'justification' );
        edit( 'effectivness' );
        edit( 'cmeasure' );
        edit( 'cmeasure_justification' );
        edit( 'sso_approval' );
        edit( 'EV_SSO' );
        edit( 'EV_FSA' );
        edit( 'EV_IVV' );
    });


function upload_evidence(){
    $("#maskDiv").show();
    $("#editorDIV").show();
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
