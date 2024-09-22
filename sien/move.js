let chartInstance = null;
let chartInstance1 = null;
document.addEventListener('DOMContentLoaded', function() {
    fetchStudentslist();
})

function fetchStudentslist() {
    fetch('../fetch_students_list.php')
        .then(response => response.json())
        .then(data => {
            const students = data.students;
            const selectElement = document.getElementById('learner-list');
            const selectElementQues = document.getElementById('ques-list');
            students.forEach(student => {
                const option = document.createElement('option');
                option.value = student.UID;
                option.textContent = student.UID;
                selectElement.appendChild(option);
            });
            // 学習者が選択されたときのイベントリスナーを追加
            selectElement.addEventListener('change', function() {
                if (selectElement.value) {
                    console.log("学習者が選択されました。");
                    const selectedUID = selectElement.value;
                    console.log(selectedUID);
                    fetchQueslist(selectedUID);
                    displayLearnerDetails(selectedUID); // 選択された学習者を表示
                    //これに加え，学習者が解答した問題数をカウントするものと，正答率を表示するものを追加

                }
            });
            //問題が選択されたときのイベントリスナーを追加
            selectElementQues.addEventListener('change', function() {
                if (selectElementQues.value) {
                    console.log("問題が選択されました。");
                    const selectElement1 = document.getElementById('learner-list');
                    const selectedUID = selectElement.value;
                    const selectedWID = selectElementQues.value;
                    console.log(selectedUID);
                    console.log(selectedWID);
                    displayQuesDetails(selectedWID);
                    fetchQuesinfo(selectedWID, selectedUID);
                }
            });
        })
        .catch(error => console.error(error));
}

function fetchQueslist(selectedUID) {
    fetch('../fetch_ques_list.php?uid=' + selectedUID)
    .then(response => response.json())
    .then(data => {
        const answers = data.answers;
        const ques_count = data.datacount;
        const accuracy = data.accuracy;
        const grammarinfo = data.grammarinfo;
        // Timeデータを抽出
        const timeData = answers.map(answer => answer.Time);
        console.log(timeData);
        //箱ひげ図を作成
        TimeChartPlot(timeData,answers);


        const answerListElement = document.getElementById('ques-list');
        answerListElement.innerHTML = ''; // 既存の内容をクリア
        answers.forEach(answer => {
            const option = document.createElement('option');
            option.value = answer.WID;
            option.textContent = answer.WID;
            if(answer.TF == '1'){
                option.textContent = option.textContent + ' : ' + '〇';
            }else{
                option.textContent = option.textContent + ' : ' + '×';
            }
            option.textContent = option.textContent + ' : ' + answer.Sentence
            const grammarItems = Object.values(answer.grammarJapanese).join(', ');
            option.textContent = option.textContent + ' : ' + grammarItems
            answerListElement.appendChild(option);
        });
        displayQuescount(ques_count);
        displayAccuracy(accuracy);
        missGrammarElement(grammarinfo);
    })
    .catch(error => console.error(error));
}
function displayLearnerDetails(selectedUID) {
    const studentname = document.getElementById('student-name');
    studentname.innerHTML = `選択された学習者は: <span class="text-highlight">${selectedUID}</span> です。`;
}
function displayQuescount(ques_count){
    const student_ques_count = document.getElementById('student-ques-count');
    student_ques_count.textContent = `■問題数: ${ques_count} 件`
}
function displayAccuracy(accuracy){
    const student_accuracy = document.getElementById('student-accuracy');
    student_accuracy.textContent = `■正解率: ${accuracy} %`
}
function missGrammarElement(grammarinfo){
    const miss_grammar = document.querySelector('#miss-grammar-table tbody');
    miss_grammar.innerHTML = ''; // 既存の内容をクリア
    // オブジェクトを配列に変換
    const grammarArray = Object.keys(grammarinfo).map(key => {
        return { grammar: key, ...grammarinfo[key] };
    });
    //console.log(grammarArray);
    // grammaraccuracyで昇順に並べ替え
    grammarArray.sort((a, b) => a.grammaraccuracy - b.grammaraccuracy);
    //水平棒グラフ作成のために配列を関数に送る
    createhorizonBarChart(grammarArray);

    // 上位5件を取り出す
    const top5 = grammarArray.slice(0, 5);
    // 行を追加
    top5.forEach((info, index) => {
        const row = document.createElement('tr');
        row.dataset.grammar = info.grammar; // データ属性として文法項目を保存

        const cellRank = document.createElement('td');
        cellRank.textContent = index + 1;
        row.appendChild(cellRank);

        const cellGrammar = document.createElement('td');
        cellGrammar.textContent = info.grammarjapanese;
        row.appendChild(cellGrammar);

        const cellAccuracy = document.createElement('td');
        cellAccuracy.textContent = `${info.grammaraccuracy}%`;
        row.appendChild(cellAccuracy);

        row.addEventListener('click', function() {
            updateSelectQues(this.dataset.grammar); // クリック時に文法項目を引数として関数を実行
            //console.log(this.dataset.grammar);
        });

        miss_grammar.appendChild(row);
    });
}

function updateSelectQues(selectedGrammar) {
    const selectElement = document.getElementById('learner-list');
    const selectedUID = selectElement.value;
    
    const select = document.getElementById('ques-list');
    select.innerHTML = ''; // 既存の内容をクリア

    
    fetch('../fetch_ques_list.php?uid=' + selectedUID + '&grammar=' + selectedGrammar)
    .then(response => response.json())
    .then(data => {
        const answers = data.answers;
        const ques_count = data.datacount;
        const accuracy = data.accuracy;
        const grammarinfo = data.grammarinfo;
        const grammarnumber = data.grammarnumber;
        console.log(grammarnumber);

        const answerListElement = document.getElementById('ques-list');
        answerListElement.innerHTML = ''; // 既存の内容をクリア
        answers.forEach(answer => {
            const option = document.createElement('option');
            option.value = answer.WID;
            option.textContent = answer.WID
            if(answer.TF == '1'){
                option.textContent = option.textContent + ' : ' + '〇';
            }else{
                option.textContent = option.textContent + ' : ' + '×';
            }
            option.textContent = option.textContent + ' : ' + answer.Sentence
            const grammarItems = Object.values(answer.grammarJapanese).join(', ');
            option.textContent = option.textContent + ' : ' + grammarItems
            answerListElement.appendChild(option);
        });
        displayQuescount(ques_count);
        displayAccuracy(accuracy);
        missGrammarElement(grammarinfo);
    })
    .catch(error => console.error(error));
    
}

function fetchQuesinfo(selectedWID, selectedUID) {
    fetch('../fetch_ques_info.php?wid=' + selectedWID + '&uid=' + selectedUID)
    .then(response => response.json())
    .then(data => {
        const quesinfo = data.quesinfo;
        console.log(quesinfo);
        //const questionname = document.getElementById('ques-name');
        displayQuesSentence(quesinfo);
        displayQuesgrammar(quesinfo[0].grammarJapanese);
        displayQueslevel(quesinfo[0].level);
        //displayQuesEndsentence(quesinfo[0].EndSentence);
        displayQuesTF(quesinfo[0].TF);
        displayQueswordnum(quesinfo[0].wordnum);
        console.log(quesinfo[0].hesitate2);
        displayQueshesitateWord(quesinfo[0].hesitate2);
        displayQuesTime(quesinfo[0].Time);
        relativeSentence(quesinfo[0].Sentence, quesinfo[0].EndSentence);
    })
    .catch(error => console.error(error));
}
function createhorizonBarChart(grammarArray) {
    const ctx1 = document.getElementById('stu-accuracy-grammar').getContext('2d');
    const labels = grammarArray.map(item => item.grammarjapanese);
    const data = grammarArray.map(item => item.grammaraccuracy);
    //console.log("labels: " + labels);
    //console.log("data: " + data);
    // 既存のチャートがある場合は破棄する
    if (chartInstance) {
        chartInstance.destroy();
    }
    chartInstance = new Chart(ctx1, {
        type:'bar',  //水平棒グラフ
        data:{
            labels: labels,
            datasets: [{
                label: '文法項目別正解率',
                data: data,
                backgroundColor: 'rgba(75, 192, 192, 0.2)', // 同じ色
                borderColor: 'rgba(75, 192, 192, 1)', // 同じ色
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            scales: {
                x:{
                    beginAtZero: true
                }
            }
        }
    });
}
function TimeChartPlot(timeData,answers) {
    const ctx2 = document.getElementById('stu-time-chart').getContext('2d');

    // 既存のチャートを破棄
    if (chartInstance1) {
        chartInstance1.destroy();
    }

    // 1. timeDataを区間（ビン）に分割する
    const bins = 5; // ヒストグラムのビンの数
    const minTime = Math.min(...timeData);
    const maxTime = Math.max(...timeData);
    const binWidth = (maxTime - minTime) / bins;
    const histogramData = new Array(bins).fill(0);

    timeData.forEach(value => {
        const binIndex = Math.floor((value - minTime) / binWidth);
        if (binIndex >= bins) {
            histogramData[bins - 1]++; // 最大値のビンに含める
        } else {
            histogramData[binIndex]++;
        }
    });

    // 2. X軸ラベル（ビンの境界）を作成
    const labels = [];
    for (let i = 0; i < bins; i++) {
        const start = minTime + i * binWidth;
        const end = start + binWidth;
        labels.push(`${Math.round(start)} - ${Math.round(end)}`);
    }

    // 3. ヒストグラムを描画
    chartInstance1 = new Chart(ctx2, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Time Data Distribution',
                data: histogramData,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'データ数'
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: '総解答時間'
                    }
                }
            },
            plugins: {
                title: {
                    display: true,
                    text: '総解答時間分布'
                }
            },
            onClick: (event, elements) => {
                if (elements.length > 0) {
                    const index = elements[0].index;
                    const start = minTime + index * binWidth;
                    const end = start + binWidth;
                    filterQuestionsByTimeRange(answers, start, end);
                }
            }
        }
    });
}
function filterQuestionsByTimeRange(answers, start, end) {
    const filtered = answers.filter(answer => answer.Time >= start && answer.Time < end);
    const dropdown = document.getElementById('filtered-questions');
    console.log("aaa");
    console.log(filtered.length);
    dropdown.innerHTML = "<option value=''>問題を選択してください</option>";

    if (filtered.length === 0) {
        const noOption = document.createElement('option');
        noOption.textContent = "No questions found in this time range";
        noOption.value = "";
        dropdown.appendChild(noOption);
    } else {
        filtered.forEach(answer => {
            const option = document.createElement('option');
            const grammar = Object.values(answer.grammarJapanese).join(', '); // 文法項目をカンマ区切りで取得
            const tfDisplay = answer.TF === 1 ? '〇' : '×';  // 正誤表示
            option.textContent = `WID: ${answer.WID} :${tfDisplay} : "${answer.Sentence}" : ${grammar}`;
            option.value = answer.WID;  // 必要に応じて適切なvalueを設定
            dropdown.appendChild(option);
        });
    }
}
function displayQuesDetails(selectedWID) {
    const questionname = document.getElementById('ques-name');
    questionname.innerHTML = `選択された問題は: <span class="text-highlight">${selectedWID}</span> です。`;
}
function displayQuesSentence(quesinfo){
    const question_sentence = document.getElementById('ques-sentence');
    //console.log(quesinfo);
    question_sentence.innerHTML = `■日本語文: ${quesinfo[0].Japanese}<br>■問題文: ${quesinfo[0].Sentence}`;
}

function displayQuesgrammar(grammarJapanese){
    const question_grammar = document.getElementById('ques-grammar');
    console.log(typeof(grammarJapanese));
    // オブジェクトの値を取得してカンマ区切りの文字列に変換
    const grammarItems = Object.values(grammarJapanese).join(', ');

    // カンマ区切りの文字列を表示
    question_grammar.textContent = `■文法項目: ${grammarItems}`;
}
function relativeSentence(Sentence, EndSentence){
    const sentenceWords = Sentence.split(' ');
    const endSentenceWords = EndSentence.split(' ');

    //比較結果を格納する配列
    const highlightSenteneceWords = [];
    const highlightEndSentenceWords = [];
    //長さ取得
    const maxlength = Math.max(sentenceWords.length, endSentenceWords.length);
    for(let i = 0; i < maxlength; i++){
        if(sentenceWords[i] !== endSentenceWords[i]){
            highlightEndSentenceWords.push(`<span class="text-highlight">${endSentenceWords[i] || ''}</span>`);
        }else{
            highlightEndSentenceWords.push(endSentenceWords[i] || '');
        }
    }

    document.getElementById('ques-endsentence').innerHTML = `■最終解答文: ${highlightEndSentenceWords.join(' ')}`;
}
function displayQueslevel(level){
    const question_level = document.getElementById('ques-level');
    if(level == 1){
        level = "初級";
    }else if(level == 2){
        level = "中級";
    }else if(level == 3){
        level = "上級";
    }
    question_level.textContent = `■難易度: ${level}`;
}
function displayQueswordnum(wordnum){
    const question_wordnum = document.getElementById('ques-wordnum');
    question_wordnum.textContent = `■単語数: ${wordnum}`;
}
function displayQuesEndsentence(EndSentence){
    const question_endsentence = document.getElementById('ques-endsentence');
    question_endsentence.textContent = `■最終解答文: ${EndSentence}`;
}
function displayQuesTF(TF){
    const question_TF = document.getElementById('ques-TF');
    if(TF == 1){
        TF = "○";
    }else if(TF == 0){
        TF = "×";
    }
    question_TF.textContent = `■正解: ${TF}`;
    
}
function displayQuesTime(time){
    //時間表示
    console.log(time);
}

function displayQueshesitateWord(hesitate2Word){
    console.log(hesitate2Word);
    const question_hesitateWord = document.getElementById('ques-hesitateword');
    if(hesitate2Word == ""){
        question_hesitateWord.textContent = `■迷いの可能性のある単語: なし`;
    }else{
        question_hesitateWord.textContent = `■迷いの可能性のある単語: ${hesitate2Word}`;
    }
}



