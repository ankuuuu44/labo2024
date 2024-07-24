import sys
sys.path.append('c:\\users\\tomoya\\anaconda3\\lib\\site-packages')
import json
import matplotlib.pyplot as plt
import japanize_matplotlib # 追加
import numpy as np


# JSONファイルのパス
json_file1 = 'json/file_all_acu.json'
json_file2 = 'json/file_all_acu_stu.json'

# JSONファイルの読み込み
with open(json_file1, 'r') as f:
    data1 = json.load(f)

with open(json_file2, 'r') as f:
    data2 = json.load(f)

# キーと値を取得
keys = list(data1.keys())
values1 = list(data1.values())
values2 = list(data2.values())

# キーを整数としてソート
keys = [int(k) for k in keys]
sorted_indices = np.argsort(keys)
keys = np.array(keys)[sorted_indices]
values1 = np.array(values1)[sorted_indices]
values2 = np.array(values2)[sorted_indices]

key_label_map = {
    -1: "その他",
    1: "仮定法，命令法",
    2: "It,There",
    3: "無生物主語",
    4: "接続詞",
    5: "倒置",
    6: "関係詞",
    7: "間接話法",
    8: "前置詞",
    9: "分詞",
    10: "動名詞",
    11: "不定詞",
    12: "受動態",
    13: "助動詞",
    14: "比較",
    15: "否定",
    16: "後置修飾",
    17: "完了形，時制",
    18: "句動詞",
    19: "挿入",
    20: "使役",
    21: "補語/二重目的語",
    22: "不明",
}

# 0は削除する
valid_indices = keys != 0
keys = keys[valid_indices]
values1 = values1[valid_indices]
values2 = values2[valid_indices]

# キーをラベルに変換
labels = [key_label_map[key] for key in keys]

# グラフの描画
bar_width = 0.4
indices = np.arange(len(keys))

fig, ax = plt.subplots(figsize=(10, 8))

# 平均の棒グラフ
bar1 = ax.barh(indices - bar_width/2, values1, bar_width, label='平均', color = "blue")

# 対象学習者の棒グラフ
bar2 = ax.barh(indices + bar_width/2, values2, bar_width, label='対象学習者', color="red")


# 軸ラベルの設定
ax.set_xlabel('Percentage')
ax.set_ylabel('Grammar Items')
ax.set_yticks(indices)
ax.set_yticklabels(labels)
ax.set_title('Comparison of Grammar Item Accuracy')
ax.legend()

# グリッドの追加
ax.grid(axis='x', linestyle='--', alpha=0.7)



# グラフの表示
plt.tight_layout()
plt.savefig('images/comparison_grammar_accuracy.jpg', format='jpg')
#plt.show()
