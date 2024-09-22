import pandas as pd

# CSVファイルの読み込み
file_path = 'c:/Users/tomoya/Downloads/sampledata.csv'
df = pd.read_csv(file_path)

# データの並べ替え
df_sorted = df.sort_values(by=['UID', 'level'])

# 文法項目を展開
df_sorted['grammar'] = df_sorted['grammar'].str.split('#')
df_sorted = df_sorted.explode('grammar')

# 文法項目のクリーニング
df_sorted['grammar'] = df_sorted['grammar'].str.strip()
df_sorted = df_sorted[df_sorted['grammar'] != '']

# レベルごとの正解率を計算
accuracy = df_sorted.groupby(['UID', 'level', 'grammar'])['TF'].mean().reset_index()
accuracy.rename(columns={'TF': 'accuracy'}, inplace=True)

# Understandが2と4のみを抽出して割合を計算
def calculate_hesitate_ratio(group):
    total_count = group.shape[0]
    hesitate_count = group[group['Understand'].isin([2, 4])].shape[0]
    if total_count > 0:
        ratio = hesitate_count / total_count
    else:
        ratio = None
    return ratio

# 各レベルおよび文法項目ごとにhesitateratioを計算
hesitateratio = df_sorted.groupby(['UID', 'level', 'grammar']).apply(calculate_hesitate_ratio).reset_index(name='hesitateratio')

# accuracyとhesitateratioをマージ
result = pd.merge(accuracy, hesitateratio, on=['UID', 'level', 'grammar'])

# 結果を表示
print(result)

# 結果をCSVファイルに保存
output_file_path = 'output/sampledata_hesitate.csv'
result.to_csv(output_file_path, index=False)
