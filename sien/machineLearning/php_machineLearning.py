import sys
sys.path.append('c:\\users\\tomoya\\anaconda3\\lib\\site-packages')
import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score
from sklearn.model_selection import cross_val_score,KFold,cross_validate
from sklearn.metrics import precision_score, recall_score, f1_score, classification_report
import numpy as np
import json


class Classify:
    def __init__(self,df):
        self.df = df
        self.Understand1_count = 0
        self.Understand2_count = 0
        self.Understand3_count = 0
        self.Understand4_count = 0
        self.classifydf = pd.DataFrame()
        self.f1scores = []
        self.Featureimportances = []
        self.result = {}

    def binary(self):
        self.classify_df=self.df[self.df['Understand'].isin([2,4])]    #2がかなり迷った4がほとんど迷わなかった
        return self.classify_df

    def countUnderstand(self,df):
        self.Understand1_count = df[df['Understand'] == 1].shape[0]
        self.Understand2_count = df[df['Understand'] == 2].shape[0]
        self.Understand3_count = df[df['Understand'] == 3].shape[0]
        self.Understand4_count = df[df['Understand'] == 4].shape[0]

    def makingclassifydf(self):
        if self.Understand2_count >= self.Understand4_count:
            samplerow = self.Understand4_count
        else:
            samplerow = self.Understand2_count

        sampledf_2 =self.classify_df[self.classify_df['Understand'] == 2].sample(n=samplerow,random_state = 0)
        sampledf_4 =self.classify_df[self.classify_df['Understand'] == 4].sample(n=samplerow,random_state = 0)

        self.classifydf = pd.concat([sampledf_2,sampledf_4])
        #print(self.classifydf)
    
    def RandomForestClassify(self):
        count = 0
        tmpdf = self.classifydf
        tmpdf = tmpdf.drop(["UID","WID","Understand"],axis = 1)
        features = tmpdf.columns
        featuresdict = {}
        objective = ['Understand']
        #
        for i in features:
            featuresdict[i] = 0

        kf = KFold(n_splits=10, shuffle=True, random_state=0)
        X_data = self.classifydf[features]
        Y_data = self.classifydf[objective]


        for train_index,test_index in kf.split(X_data):
            train_x = X_data.iloc[train_index]
            train_y = Y_data.iloc[train_index].to_numpy().ravel()
            test_x = X_data.iloc[test_index]
            test_y = Y_data.iloc[test_index].to_numpy().ravel() 

            model = RandomForestClassifier(n_estimators=100, random_state=0)
            model.fit(train_x, train_y)
            pred = model.predict(test_x)
            Feature_importances = model.feature_importances_
            indices = np.argsort(Feature_importances)[::-1]


            f1 = f1_score(test_y, pred, average='macro')
            print('{:.2f}'.format(f1*100))
            for i in range(len(features)):
                print(str(i+1) + " "+ str(features[indices[i]]) + " " + '{:.2f}'.format(Feature_importances[indices[i]]))
                featuresdict[features[indices[i]]] += Feature_importances[indices[i]]

            self.f1scores.append(f1)
            count += 1
        f1score = np.mean(self.f1scores)*100
        print(f1score)
        for i in features:
            featuresdict[i] = featuresdict[i]/count

        jsonfinename = './featurejson/featuredict.json'
        with open(jsonfinename, 'w') as f:
            json.dump(featuresdict,f)
        

def main():
    inputfilename = 'pydata/test.csv'
    df = pd.read_csv(inputfilename)
    datamarge=Classify(df)
    return_df = datamarge.binary()
    datamarge.countUnderstand(return_df)

    """
    print(datamarge.Understand1_count)
    print(datamarge.Understand2_count)
    print(datamarge.Understand3_count)
    print(datamarge.Understand4_count)
    """

    datamarge.makingclassifydf()        #ここで迷い無しとありが1:1のデータセットができている．
    datamarge.RandomForestClassify()
    

    """
    df1 = df.drop(["UID","WID"],axis = 1)
    print(df1.columns)
    """
    


if __name__ == '__main__':
    main()