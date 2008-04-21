$(document).ready(function(){

    $("select[name='system']").change(function(){
        var url = $(this).attr("url") + '/sid/' + $(this).children("option:selected").attr('value');
        $("select[name='asset_list']").parent().load(url,null,function(){
            asset_detail();
        });
    });

    asset_detail();

});


function asset_detail() {
    $("select[name='asset_list']").change(function(){
        var url = '/zfentry.php/asset/detail/id/'+ $(this).children("option:selected").attr('value');
        $("div#asset_info").load(url);
    });
}
