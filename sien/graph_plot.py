import sys
sys.path.append('c:\\users\\tomoya\\anaconda3\\lib\\site-packages')
import numpy as np
import matplotlib.pyplot as plt
import json


# JSON ファイルを読み込む
file_path = 'json/file.json'
with open(file_path, 'r') as file:
    data = json.load(file)

# JSONデータを解析して正解率のリストを作成
accuracy_data = [user['accuracy'] for user in data.values()]

# 正解率データからヒストグラムを作成
plt.hist(accuracy_data, bins=10, range=(0, 100), alpha=0.75, color='blue', edgecolor='black')

# タイトルと軸ラベルの設定
plt.title('Accuracy Histogram')
plt.xlabel('Accuracy (%)')
plt.ylabel('Frequency')

# グリッドの追加
plt.grid(True)

plt.savefig('images/accuracy_histogram.png')