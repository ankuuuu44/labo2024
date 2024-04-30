function fetchData(url) {
    return fetch(url)
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            return data;
        })
        .catch(error => {
            console.error('There has been a problem with your fetch operation:', error);
        });
}

document.addEventListener('DOMContentLoaded', function() {
    //document.addEventListenerについて
    //ターゲットに特定のイベントが配信されるたびに呼び出される関数を設定する
    //HTMLが完全に読み込まれ，すべてのDOM要素が利用可能になった時に実行されるイベントハンドラーの設定
    //この関数の中に書かれたものは，ページが正しく読み込まれてからスクリプトが実行されることが保証される．
    var userLinks = document.querySelectorAll('.user-link');
    var quesLinks = document.querySelectorAll('.ques-link');
    var historyContainer = document.getElementById('history-container');

    //ここからグラフ描画の変数
    var canvasTF = document.getElementById('canvasTF').getContext('2d');
    var canvasBar = document.getElementById('canvasBar').getContext('2d');
    var myDoughnutChart;    // チャートの変数を定義
    var myBarChart;         //積み上げ縦棒の変数定義

    userLinks.forEach(function(link) {
        link.addEventListener('click', function(event) {
            event.preventDefault();
            var userId = link.getAttribute('data-users-id');
            fetchData('get_user_history.php?user_id=' + userId)
                .then(function(data) {
                    //historyContainer.innerHTML = JSON.stringify(data);
                    
                    // 既存のチャートを破棄
                    if (myDoughnutChart) {
                        myDoughnutChart.destroy();
                    }

                    if (myBarChart){
                        myBarChart.destroy();
                    }

                    var chartData = {
                        labels: ['正解', '不正解'],
                        datasets: [{
                            data: [data.countT, data.countF],
                            backgroundColor: ['green', 'red']
                        }]
                    };

                    var labels = ['第一回解答'];

                    var correctData = [data.countT];
                    var incorrectData = [data.countF];

                    // 新しいチャートを作成
                    myDoughnutChart = new Chart(canvasTF, {
                        type: 'pie',
                        data: chartData
                    });

                    myBarChart = new Chart(canvasBar, {
                        type: 'bar',
                        data: {
                            labels: labels,
                            datasets: [
                                {
                                    label: '正解',
                                    data: correctData,
                                    backgroundColor: 'green'
                                },
                                {
                                    label: '不正解',
                                    data: incorrectData,
                                    backgroundColor: 'red'
                                }
                            ]
                        },
                        options: {
                            scales: {
                                x: {
                                    stacked: true
                                },
                                y: {
                                    stacked: true
                                }
                            }
                        }
                    });


                })
                .catch(function(error) {
                    console.error('データの取得に失敗しました: ', error);
                });
        });
    });
});
