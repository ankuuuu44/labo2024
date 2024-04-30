
document.addEventListener('DOMContentLoaded', function(){
    var questreemenulink = document.getElementById('questree-menu');    //getElementByIdはidタグの要素のドキュメント要素を取得するメソッド
    var questreemenu = document.querySelector('.ques_search'); // クエリセレクタを使用してques_searchクラスの要素を取得
    var stutreemenulink = document.getElementById('studenttree-menu');
    var stutreemenu = document.querySelector('.student_search');
    var studentAlllink = document.getElementById('studentall-menu');

    questreemenulink.addEventListener('click', function(event){
        event.preventDefault(); 
        questreemenu.classList.toggle('active');
        // element.classList.toggle('クラス名') を使用してクラスの表示/非表示を切り替える
        /**element.classlist.toggle('クラス名')
            elementにクラス名があればクラス名を除去し，なければ追加する
            押したらツリーメニューが表示され再度クリックされると非表示になる．
         */
    });
    stutreemenulink.addEventListener('click', function(event){
        event.preventDefault(); 
        stutreemenu.classList.toggle('active');
       
         
    });

    


    studentAlllink.addEventListener('click', function(event) {
        event.preventDefault();
        // displayUsers 関数を呼び出して学習者一覧を表示
        //displayUsers();
        var userlist = document.querySelector('.user-list');
        userlist.classList.toggle('active');
    });
});
