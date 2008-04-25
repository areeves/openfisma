$(document).ready(function(){

    $("select[name='system']").change(function(){
        searchAsset();
    });

    asset_detail();

    $("input#search_asset").click(function(){
        searchAsset();
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
        $("div#asset_info").load(url);
    });
}
