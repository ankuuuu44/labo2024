import pandas as pd
from sklearn.ensemble import RandomForestClassifier
from sklearn.model_selection import train_test_split
from sklearn.metrics import accuracy_score
from sklearn.model_selection import cross_val_score,KFold,cross_validate
from sklearn.metrics import precision_score, recall_score, f1_score, classification_report
import numpy as np
import json
import mysql.connector


class CSVaction:
    def __init__(self):
        self.output_path = 'pydata/hesitate2_4.csv'
    def write_csv(self,df):
        if df is not None:
            sortdf = df.sort_values(['UID', 'WID'])
            sortdf.to_csv(self.output_path, mode='w')
        else:
            print("Data frame is empty. Please read a CSV file first.")
class Classify:
    def __init__(self,df):
        self.df = df
        self.Understand1_count = 0
        self.Understand2_count = 0
        self.Understand3_count = 0
        self.Understand4_count = 0
        self.mean_accuracy = 0
        self.precision_y = 0
        self.recall_y = 0
        self.f1score_y = 0
        self.precision_n = 0
        self.recall_n = 0
        self.f1score_n = 0
        self.classifydf = pd.DataFrame()
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

    def countUIDdata(self):
        counts = self.classifydf['UID'].value_counts()
        # カウント結果を表示
        for item, count in counts.items():
            print(f"{item}: {count}")
    
    def divideMainStu(self,mainstu):
        self.mainStudf = self.classifydf.query('UID == @mainstu')
        self.subStudf = self.classifydf.query('UID != @mainstu')
        self.mainStudf = self.mainStudf.sort_values(['UID', 'WID'])
        self.subStudf = self.subStudf.sort_values(['UID', 'WID'])
        self.mainStudf_rows = self.mainStudf.shape[0]
        self.subStudf_rows = self.subStudf.shape[0]
        #print(self.mainStudf)
        #print(self.subStudf)
        #print(self.mainStudf_rows)

        k = 2                       #mainStuを分割したい数
        if self.mainStudf_rows % k == 0:
            N = (self.mainStudf_rows / k)
        else:
            N = (self.mainStudf_rows // k) + 1
        N = int(N)
        print(N)

        self.splited_df = [self.mainStudf[i:i+N] for i in range(0, len(self.mainStudf), N)]
        self.splited_df0 = self.splited_df[0]
        self.splited_df1 = self.splited_df[1]

    def RandomForestClassify_Stu(self,testdf,learningdf):
        count = 0
        tmptestdf = testdf         
        tmplearningdf = learningdf
        tmptestdf = tmptestdf.drop(["UID","WID","Understand"],axis = 1)
        self.features = tmptestdf.columns.tolist()
        self.featuresdict = {}
        objective = ['Understand']
        #
        for i in self.features:
            self.featuresdict[i] = 0

        #学習データと検証データの分割
        X_data_train_after = learningdf[self.features]
        Y_data_train_after = learningdf[objective]
        Y_data_train_after = Y_data_train_after.to_numpy().ravel()
        X_data_test_after = testdf[self.features]
        Y_data_test_after = testdf[objective]
        Y_data_test_after = Y_data_test_after.to_numpy().ravel()


        #学習
        accuracies = []
        precisions_y = []   #迷い有り適合率
        recalls_y = []      #迷い有り再現率
        f1s_y = []          #迷い有りF1スコア
        precisions_n = []   #迷い無し適合率
        recalls_n = []      #迷い無し再現率
        f1s_n = []          #迷い無しF1スコア

        #迷い推定
        model = RandomForestClassifier(n_estimators=100, random_state=0)
        model.fit(X_data_train_after, Y_data_train_after)
        pred = model.predict(X_data_test_after)
        Feature_importances = model.feature_importances_
        indices = np.argsort(Feature_importances)[::-1]

        self.accuracy = accuracy_score(Y_data_test_after, pred)
        self.precision_y = precision_score(Y_data_test_after, pred, pos_label=2)
        self.recall_y = recall_score(Y_data_test_after, pred, pos_label=2)
        self.f1_y = f1_score(Y_data_test_after, pred, pos_label=2)
        self.precision_n = precision_score(Y_data_test_after, pred, pos_label=4)
        self.recall_n = recall_score(Y_data_test_after, pred, pos_label=4)
        self.f1_n = f1_score(Y_data_test_after, pred, pos_label=4)




        print(f'Accuracy: {self.accuracy}, Precision_y: {self.precision_y}, Recall_y: {self.recall_y}, F1_y: {self.f1_y}, Precision_n: {self.precision_n}, Recall_n: {self.recall_n}, F1_n: {self.f1_n}')

        '''
        print(f'count:{count+1}')
        print(f'Accuracy: {accuracy}')
        print(f'Precision_y: {precision_y}')
        print(f'Recall_y: {recall_y}')
        print(f'F1_y: {f1_y}')
        print(f'Precision_n: {precision_n}')
        print(f'Recall_n: {recall_n}')
        print(f'F1_n: {f1_n}')
        '''
        for i in range(len(self.features)):
            #print(str(i+1) + " "+ str(self.features[indices[i]]) + " " + '{:.2f}'.format(Feature_importances[indices[i]]))
            self.featuresdict[self.features[indices[i]]] += Feature_importances[indices[i]]

        #print(self.featuresdict)
        self.featuresdict_sorted = sorted(self.featuresdict.items(), key=lambda x:x[1])
        #print(self.featuresdict_sorted)
        #self.featuresdict['f1score'] = f1score




    
    def RandomForestClassify(self):
        count = 0
        tmpttestdf = self.classifydf
        tmpttestdf = tmpttestdf.drop(["UID","WID","Understand"],axis = 1)
        self.features = tmpttestdf.columns.tolist()
        self.featuresdict = {}
        objective = ['Understand']
        #
        for i in self.features:
            self.featuresdict[i] = 0

        kf = KFold(n_splits=10, shuffle=True, random_state=0)
        X_data = self.classifydf[self.features]
        Y_data = self.classifydf[objective]
        accuracies = []
        precisions_y = []   #迷い有り適合率
        recalls_y = []      #迷い有り再現率
        f1s_y = []          #迷い有りF1スコア
        precisions_n = []   #迷い無し適合率
        recalls_n = []      #迷い無し再現率
        f1s_n = []          #迷い無しF1スコア


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

            accuracy = accuracy_score(test_y, pred)
            if 2 in test_y:
                precision_y = precision_score(test_y, pred, pos_label=2)
                recall_y = recall_score(test_y, pred, pos_label=2)
                f1_y = f1_score(test_y, pred, pos_label=2)
            else:
                 print(f"Fold Class 2 not found in y_test, skipping precision, recall, f1 for class 2")

            if 4 in test_y:
                precision_n = precision_score(test_y, pred, pos_label=4)
                recall_n = recall_score(test_y, pred, pos_label=4)
                f1_n = f1_score(test_y, pred, pos_label=4)
            else:
                print(f"Fold : Class 4 not found in y_test, skipping precision, recall, f1 for class 4")


            accuracies.append(accuracy)
            precisions_y.append(precision_y)
            recalls_y.append(recall_y)
            f1s_y.append(f1_y)
            precisions_n.append(precision_n)
            recalls_n.append(recall_n)
            f1s_n.append(f1_n)
            print(f'Fold {count+1}: Accuracy: {accuracies}, Precision_y: {precisions_y}, Recall_y: {recall_y}, F1_y: {f1_y}, Precision_n: {precision_n}, Recall_n: {recall_n}, F1_n: {f1_n}')

            '''
            print(f'count:{count+1}')
            print(f'Accuracy: {accuracy}')
            print(f'Precision_y: {precision_y}')
            print(f'Recall_y: {recall_y}')
            print(f'F1_y: {f1_y}')
            print(f'Precision_n: {precision_n}')
            print(f'Recall_n: {recall_n}')
            print(f'F1_n: {f1_n}')
            '''
            for i in range(len(self.features)):
                #print(str(i+1) + " "+ str(self.features[indices[i]]) + " " + '{:.2f}'.format(Feature_importances[indices[i]]))
                self.featuresdict[self.features[indices[i]]] += Feature_importances[indices[i]]

            count += 1

        for i in self.features:
            self.featuresdict[i] = self.featuresdict[i]/count
        #self.featuresdict['f1score'] = f1score

        jsonfinename = './featurejson/featuredict.json'
        with open(jsonfinename, 'w') as f:
            json.dump(self.featuresdict,f)
        self.mean_accuracy = np.mean(accuracies)
        self.precision_y = np.mean(precisions_y)
        self.recall_y = np.mean(recalls_y)
        self.f1score_y = np.mean(f1s_y)
        self.precision_n = np.mean(precisions_n)
        self.recall_n = np.mean(recalls_n)
        self.f1score_n = np.mean(f1s_n)


        print("Mean Accuracy:", self.mean_accuracy)
        print("Mean Precision_y:", self.precision_y)
        print("Mean Recall_y:", self.recall_y)
        print("Mean F1score_y:", self.f1score_y)
        print("Mean Precision_n:", self.precision_n)
        print("Mean Recall_n:", self.recall_n)
        print("Mean F1score_n:", self.f1score_n)

        '''
        print("---------------------------------------")
        print("Mean Accuracy:", self.mean_accuracy)
        print("Mean Precision_y:", self.precision_y)
        print("Mean Recall_y:", self.recall_y)
        print("Mean F1score_y:", self.f1score_y)
        print("Mean Precision_n:", self.precision_n)
        print("Mean Recall_n:", self.recall_n)
        print("Mean F1score_n:", self.f1score_n)
        '''


class DBaction:
    def __init__(self,host,user,password,dbname):
        self.host = host
        self.user = user
        self.password = password
        self.dbname = dbname
        self.conn = None
        self.cursor = None

    def connectDB(self):
        try:
            self.conn = mysql.connector.connect(
                host = self.host,
                user = self.user,
                password = self.password,
                database = self.dbname
            )
            self.cursor = self.conn.cursor()
            print("MySQL connection established")
        except mysql.connector.Error as e:
            print(f"Error connecting to MySQL database: {e}")

    def execute_query(self, query, params=None):
        try:
            self.cursor.execute(query, params)
            self.conn.commit()
            print("Query executed successfully")
        except mysql.connector.Error as e:
            print(f"Error executing query: {e}")

    def insertDB(self,model_name,featurename,gini_results,accuracy,precision_y,precision_n,recall_y,recall_n,f1score_y,f1score_n):
        query = """
        INSERT INTO ml_results (model_name,featurename,gini_results,acc_result,pre_result_y,pre_result_n,rec_result_y,rec_result_n,f1_score_y,f1_score_n)
        VALUES (%s, %s, %s, %s, %s, %s, %s, %s, %s, %s)
        """
        gini_results_json = json.dumps(gini_results)
        params = (model_name,featurename,gini_results_json,accuracy,precision_y,precision_n,recall_y,recall_n,f1score_y,f1score_n)
        self.execute_query(query, params)
        

def main():
    #データセット作成
    inputfilename = 'pydata/test.csv'
    df = pd.read_csv(inputfilename)
    datamarge=Classify(df)
    csvaction = CSVaction()
    #データを分割
    return_df = datamarge.binary()      #迷い有りと無しのみ二分割
    datamarge.countUnderstand(return_df)#迷い有りと無しの数をカウント

    datamarge.makingclassifydf()        #ここで迷い無しとありが1:1のデータセットができている．
    csvaction.write_csv(datamarge.classifydf)
    datamarge.countUIDdata()

    mainUID = 90910038
    datamarge.divideMainStu(mainUID)    #ここで学生のみのデータセットを作成
    results_for_csv = []                #csvに書き込むためのリスト

    for i in range (2):
        if(i == 0):
            print("---------------------------------------")
            print("テストデータ0")
            #対象学習者を含まない分類器
            testdf = datamarge.splited_df0
            learningdf = datamarge.subStudf
            print("対象学習者を含まない分類器")
            print(f'教師データ数:{learningdf.shape[0]}')
            datamarge.RandomForestClassify_Stu(testdf,learningdf)
            results_for_csv.append([mainUID,0,learningdf.shape[0],0,datamarge.accuracy,datamarge.precision_y,datamarge.recall_y,datamarge.f1_y,datamarge.precision_n,datamarge.recall_n,datamarge.f1_n,datamarge.featuresdict_sorted])


            learningdf_twice = pd.concat([datamarge.subStudf,datamarge.subStudf])
            print("2倍の学習者を含む分類器")
            print(f'教師データ数:{learningdf_twice.shape[0]}')
            datamarge.RandomForestClassify_Stu(testdf,learningdf_twice)
            results_for_csv.append([mainUID,0,learningdf_twice.shape[0],0,datamarge.accuracy,datamarge.precision_y,datamarge.recall_y,datamarge.f1_y,datamarge.precision_n,datamarge.recall_n,datamarge.f1_n,datamarge.featuresdict_sorted])

            #対象学習者を含む分類器
            count = datamarge.subStudf_rows
            whilecount = 0
            count_mainstudata = 0
            print("対象学習者を含む分類器")
            while(count <= 3*(datamarge.subStudf_rows)):
                if (whilecount == 0):
                    learningdf_in_mainstu = pd.concat([datamarge.subStudf,datamarge.splited_df1])
                else:
                    learningdf_in_mainstu = pd.concat([learningdf_in_mainstu,datamarge.splited_df1])
                datamarge.RandomForestClassify_Stu(testdf,learningdf_in_mainstu)

                count = learningdf_in_mainstu.shape[0]
                count_mainstudata += datamarge.splited_df1.shape[0]
                whilecount = whilecount + 1
                results_for_csv.append([mainUID,whilecount,count,count_mainstudata,datamarge.accuracy,datamarge.precision_y,datamarge.recall_y,datamarge.f1_y,datamarge.precision_n,datamarge.recall_n,datamarge.f1_n,datamarge.featuresdict_sorted])
                print(f'教師データ数:{count},whilecount:{whilecount}')

        elif(i == 1):
            print("-------------------------------------------")
            print("テストデータ1")
            #対象学習者を含まない分類器
            testdf = datamarge.splited_df1
            learningdf = datamarge.subStudf
            print("対象学習者を含まない分類器")
            print(f'教師データ数:{learningdf.shape[0]}')
            datamarge.RandomForestClassify_Stu(testdf,learningdf)
            results_for_csv.append([mainUID,0,learningdf.shape[0],0,datamarge.accuracy,datamarge.precision_y,datamarge.recall_y,datamarge.f1_y,datamarge.precision_n,datamarge.recall_n,datamarge.f1_n,datamarge.featuresdict_sorted])

            learningdf_twice = pd.concat([datamarge.subStudf,datamarge.subStudf])
            print("2倍の学習者を含む分類器")

            print(f'教師データ数:{learningdf_twice.shape[0]}')
            datamarge.RandomForestClassify_Stu(testdf,learningdf_twice)
            results_for_csv.append([mainUID,0,learningdf_twice.shape[0],0,datamarge.accuracy,datamarge.precision_y,datamarge.recall_y,datamarge.f1_y,datamarge.precision_n,datamarge.recall_n,datamarge.f1_n,datamarge.featuresdict_sorted])

            #対象学習者を含む分類器
            count = datamarge.subStudf_rows
            whilecount = 0
            count_mainstudata = 0
            print("対象学習者を含む分類器")
            while(count <= 3*(datamarge.subStudf_rows)):
                if (whilecount == 0):
                    learningdf_in_mainstu = pd.concat([datamarge.subStudf,datamarge.splited_df0])
                else:
                    learningdf_in_mainstu = pd.concat([learningdf_in_mainstu,datamarge.splited_df0])
                datamarge.RandomForestClassify_Stu(testdf,learningdf_in_mainstu)

                count = learningdf_in_mainstu.shape[0]
                count_mainstudata += datamarge.splited_df0.shape[0]
                whilecount = whilecount + 1
                print(f'教師データ数:{count},whilecount:{whilecount}')
                results_for_csv.append([mainUID,whilecount,count,count_mainstudata,datamarge.accuracy,datamarge.precision_y,datamarge.recall_y,datamarge.f1_y,datamarge.precision_n,datamarge.recall_n,datamarge.f1_n,datamarge.featuresdict_sorted])
    #print(results_for_csv)

    for i, record in enumerate(results_for_csv):
        # リストの最後の要素がリストでない場合はスキップ
        if not isinstance(record[-1], list):
            continue
        features_list = record[-1]
        # features_listがタプルのリストであることを確認
        if all(isinstance(item, tuple) and len(item) == 2 for item in features_list):
            features_str = '; '.join([f"{name}: {importance}" for name, importance in features_list])
        else:
            raise ValueError(f"Record index {i}: features_listの形式が期待される形式ではありません。内容: {features_list}")
        record[-1] = features_str
    columns = ["UID","whilecount","教師データ数","指定学生のデータ数","accuracy","precision_y","recall_y","f1_y","precision_n","recall_n","f1_n","featuresdict_sorted"]
    df = pd.DataFrame(results_for_csv, columns=columns)
    # CSVファイルに書き出し
    df.to_csv('outputcsv\output.csv', mode="a",index=False, encoding="shift-jis")

            #learningdf_cheating = pd.concat([datamarge.subStudf,datamarge.mainStudf])   #正解データの中にtestデータが入っている
            #datamarge.RandomForestClassify_Stu(testdf,learningdf_cheating)              #これを実行したら100%になる．✖デバッグ用
    #mainstuの学生のデータをk分割する．   



    #datamarge.RandomForestClassify()
    


    #データベース接続
    '''
    db = DBaction(host="127.0.0.1",user="root",password="8181saisaI",dbname="2019su1")
    db.connectDB()
    #データベースinsertするデータ
    model_name = "RandomForest" #今後別のアルゴリズムを使用するなら可変にする
    featurename = json.dumps(datamarge.features)  # リストをJSON文字列に変換    #特徴量の任意設定可能にする
    gini_results = datamarge.featuresdict  #ジニ係数をこの形式で記述する
    accuracy = datamarge.mean_accuracy
    precision_y = datamarge.precision_y
    recall_y = datamarge.recall_y
    f1_score_y = datamarge.f1score_y
    precision_n = datamarge.precision_n
    recall_n = datamarge.recall_n
    f1_score_n = datamarge.f1score_n

    db.insertDB(model_name,featurename,gini_results,accuracy,precision_y,precision_n,recall_y,recall_n,f1_score_y,f1_score_n)
    '''
    


    


if __name__ == '__main__':
    main()