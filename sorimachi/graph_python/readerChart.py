import sys
sys.path.append('c:\\users\\tomoya\\anaconda3\\lib\\site-packages')
import numpy as np
import matplotlib.pyplot as plt
import json


# JSON ファイルを読み込む
file_path1 = 'json/file_all_acu.json'
with open(file_path1, 'r') as file1:
    data1_all = json.load(file1)
# JSON ファイルを読み込む
file_path1_1 = 'json/file_all_acu_stu.json'
with open(file_path1_1, 'r') as file1_1:
    data1_stu = json.load(file1_1)
# JSON ファイルを読み込む
file_path2 = 'json/file_all_hesi.json'
with open(file_path2, 'r') as file2:
    data2 = json.load(file2)
# JSON ファイルを読み込む
file_path2_1 = 'json/file_all_hesi_stu.json'
with open(file_path2_1, 'r') as file2_1:
    data2_1 = json.load(file2_1)

# データの準備
labels = list(data1_all.keys())
values1_all = list(data1_all.values())
values1_stu = list(data1_stu.values())
values2_all = list(data2.values())
values2_stu = list(data2_1.values())

# レーダーチャートを描画し保存する関数
def plot_and_save_radar_chart(labels, data_set1, data_set2, filename, title1, title2, color1, color2):
    angles = np.linspace(0, 2 * np.pi, len(labels), endpoint=False).tolist()  # 角度を設定
    data_set1 += data_set1[:1]  # データリストの末尾に最初の値を追加して閉じる
    data_set2 += data_set2[:1]  # 同上
    angles += angles[:1]  # 角度のリストも閉じる

    fig, ax = plt.subplots(figsize=(6, 6), subplot_kw=dict(polar=True))
    ax.fill(angles, data_set1, color=color1, alpha=0.25)  # データセット1の塗りつぶし
    ax.plot(angles, data_set1, color=color1, label=title1)  # データセット1のライン
    ax.fill(angles, data_set2, color=color2, alpha=0.25)  # データセット2の塗りつぶし
    ax.plot(angles, data_set2, color=color2, label=title2)  # データセット2のライン

    ax.set_xticks(angles[:-1])  # X軸の設定
    ax.set_xticklabels(labels)  # X軸ラベル
    plt.title('Comparison of ' + title1 + ' and ' + title2)  # タイトル設定
    plt.legend(loc='upper right', bbox_to_anchor=(1.1, 1.05))  # 凡例の設定
    #plt.show()
    plt.savefig(filename)  # ファイルに保存
    plt.close()  # グラフを閉じる

# グラフを描画して保存
plot_and_save_radar_chart(labels, values1_all, values1_stu, 'images/comparison_data1.png', 'ALL_ave', 'Student', 'red', 'blue')


# 別のグラフを描画して保存
plot_and_save_radar_chart(labels, values2_all, values2_stu, 'images/comparison_data2.png', 'ALL_ave', 'Student', 'green', 'purple')