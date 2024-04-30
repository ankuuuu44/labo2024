jQuery.noConflict();
jQuery(document).ready(function(){
    //フィルターのモーダルウィンドウ
    jQuery("#filterbutton").on("click",function(){
        //モーダルウィンドウ表示
        jQuery("#modal-bg").show();
    });

    jQuery(".closeButton").on("click",function(){
        //モーダルウィンドウ非表示
        jQuery("#modal-bg").hide();
        jQuery("#modal-visual").hide();
        jQuery("#modal-sum").hide();
    });

    jQuery("#modal-bg").click(function(e){
        //クリックされた要素がモーダルウィンドウの本体でなければモーダルウィンドウを非表示に
        if(!jQuery(e.target).closest("#modal-main").length){
            jQuery("#modal-bg").hide();
        }
    });


    //ビジュアライゼーションのモーダルウィンドウ
    jQuery("#visualbutton").on("click",function(){
        //モーダルウィンドウ表示
        jQuery("#modal-visual").show();
    });

    jQuery("#modal-visual").click(function(e){
        //クリックされた要素がモーダルウィンドウの本体でなければモーダルウィンドウを非表示に
        if(!jQuery(e.target).closest("#visual-main").length){
            jQuery("#modal-visual").hide();
        }
    });

    jQuery("#bargraph").on("click",function(){
        //ここに棒グラフを表示するプログラムを記述
        //新しくgraph.phpファイルを開いてそこに描画する
        window.open("graph.php","graph");
    })

    //集約のモーダルウィンドウ
    jQuery("#sumbutton").on("click",function(){
        //モーダルウィンドウ表示
        jQuery("#modal-sum").show();
        jQuery(".hide").hide();
    });

    jQuery("#modal-sum").click(function(e){
        //クリックされた要素がモーダルウィンドウの本体でなければモーダルウィンドウを非表示に
        if(!jQuery(e.target).closest("#sum-main").length){
            jQuery("#modal-sum").hide();
        }
    });

});

//sum.phpのボタン
jQuery(window).on("load",function(){
    jQuery('#sumbuttoncons').on("click",function(){
        console.log("aaa");
        window.open("sum.php","sum");
        //なんか要素の取得が上手くいかない．
        /*
        var val = $('[name=keycolumn]').val();
        console.log(val);
        */
    });
});
//ここまで
//
jQuery(document).ready(function(){
    console.log("#Timerangeの関数内です．");
    jQuery("#TimeRangeid").change(function(){
        if(jQuery(this).val() === "range"){
            jQuery("#Timesearch_minmax").show();
            jQuery("#Timesearchid").hide();
        }else{
            jQuery("#Timesearch_minmax").hide();
            jQuery("#Timesearchid").show();
        }
    });
});