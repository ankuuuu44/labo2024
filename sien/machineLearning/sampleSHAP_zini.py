import sys
sys.path.append('c:\\users\\tomoya\\anaconda3\\lib\\site-packages')
import numpy as np
import pandas as pd
import matplotlib.pyplot as plt
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
import shap

# ダミーデータの生成
np.random.seed(0)
X = pd.DataFrame({
    'Feature_A': np.random.rand(100),
    'Feature_B': np.random.rand(100),
    'Feature_C': np.random.rand(100) * 0.1,
    'Feature_D': np.random.rand(100) * 0.1
})
y = (X['Feature_A'] + X['Feature_B'] + np.random.rand(100) * 0.1 > 1).astype(int)

# トレーニングとテストデータの分割
X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.3, random_state=0)

# ランダムフォレストモデルの訓練
model = RandomForestClassifier(n_estimators=100, random_state=0)
model.fit(X_train, y_train)

# ジニ係数に基づく特徴量の重要度
gini_importance = model.feature_importances_

# SHAP値の計算
explainer = shap.TreeExplainer(model)
shap_values = explainer.shap_values(X_test)

# プロットの準備
feature_names = X.columns
shap.summary_plot(shap_values[1], X_test, plot_type="bar", show=False)
plt.title('SHAP Feature Importance')
plt.show()

# ジニ係数の重要度のプロット
plt.figure(figsize=(8, 6))
plt.barh(feature_names, gini_importance)
plt.xlabel('ジニ係数')
plt.title('特徴量重要度')
plt.show()
