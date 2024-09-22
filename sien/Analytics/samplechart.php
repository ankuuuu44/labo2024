<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histogram using Chart.js</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <canvas id="myHistogram"　max-="100"></canvas>
    <script>
        // ランダムなデータセットを生成 (0から100までの範囲)
        const data = Array.from({length: 100}, () => Math.floor(Math.random() * 101));

        // データをヒストグラム用にバケットに分類
        const bins = 10; // バケット数
        const histogramData = new Array(bins).fill(0);
        data.forEach(value => {
            const index = Math.floor(value / (100 / bins));
            histogramData[index] += 1;
        });

        // バケットのラベルを生成
        const labels = Array.from({length: bins}, (_, i) => `${i * (100 / bins)}-${(i + 1) * (100 / bins) - 1}%`);

        // Chart.jsを使用してヒストグラムを描画
        const ctx = document.getElementById('myHistogram').getContext('2d');
        const myHistogram = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: '人数',
                    data: histogramData,
                    backgroundColor: 'rgba(75, 192, 192, 0.5)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: '正解率 (%)'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'データ数'
                        },
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>