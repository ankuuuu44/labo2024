import sys
sys.path.append('c:\\users\\tomoya\\anaconda3\\lib\\site-packages')
import matplotlib.pyplot as plt
import json
import numpy as np
import seaborn as sns
import japanize_matplotlib # 追加
from matplotlib.colors import LinearSegmentedColormap

# JSONファイルのパス
json_file1 = 'json/file_all_acu.json'
json_file2 = 'json/file_all_hesi.json'

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

# 0を削除
valid_indices = keys != 0
keys = keys[valid_indices]
values1 = values1[valid_indices]
values2 = values2[valid_indices]

# キーをラベルに変換
labels = [key_label_map[key] for key in keys]

# データの正規化 (0~1)
values1_normalized = (values1 - values1.min()) / (values1.max() - values1.min())
values2_normalized = (values2 - values2.min()) / (values2.max() - values2.min())

# ヒートマップデータの作成
heatmap_data1 = np.expand_dims(values1_normalized, axis=0)
heatmap_data2 = np.expand_dims(values2_normalized, axis=0)

# ヒートマップデータの作成
heatmap_data1 = np.expand_dims(values1_normalized, axis=0)
heatmap_data2 = np.expand_dims(values2_normalized, axis=0)

# カスタムカラーマップの作成
colors_accuracy = ["red", "white"]
colors_hesitate = ["white", "red"]
n_bins = 100  # カラーマップの分解能
custom_cmap_accuracy = LinearSegmentedColormap.from_list("custom_cmap_accuracy", colors_accuracy, N=n_bins)
custom_cmap_hesitate = LinearSegmentedColormap.from_list("custom_cmap_hesitate", colors_hesitate, N=n_bins)

# ヒートマップの描画
fig, axes = plt.subplots(2, 1, figsize=(12, 8), gridspec_kw={'height_ratios': [1, 1]})

# 上のJSONデータのヒートマップ
sns.heatmap(
    heatmap_data1,
    annot=True,
    fmt=".2f",
    cmap=custom_cmap_accuracy,
    center=0.5,
    xticklabels=labels,
    yticklabels=["上のJSON"],
    ax=axes[0],
    cbar_kws={'label': 'Accuracy', 'orientation': 'horizontal'}
)
axes[0].set_title("上のJSONのAccuracy")

# 下のJSONデータのヒートマップ
sns.heatmap(
    heatmap_data2,
    annot=True,
    fmt=".2f",
    cmap=custom_cmap_hesitate,
    center=0.5,
    xticklabels=labels,
    yticklabels=["下のJSON"],
    ax=axes[1],
    cbar_kws={'label': 'Hesitation', 'orientation': 'horizontal'}
)
axes[1].set_title("下のJSONのHesitation")

# 全体のタイトルとレイアウトの調整
fig.suptitle("Grammar Item Accuracy and Hesitation Comparison", fontsize=16)
fig.tight_layout(rect=[0, 0.03, 1, 0.95])

# グラフの表示
plt.show()