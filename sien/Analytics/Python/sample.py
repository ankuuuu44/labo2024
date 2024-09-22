import re
from difflib import SequenceMatcher

def highlight_differences(correct_sentence, user_sentence):
    # 単語ごとにスペース、ピリオド、カンマで区切る
    def split_sentence(sentence):
        return re.findall(r'\b\w+\b|[.,]', sentence)
    
    correct_words = split_sentence(correct_sentence)
    user_words = split_sentence(user_sentence)
    
    # SequenceMatcherで一致部分を検出
    matcher = SequenceMatcher(None, correct_words, user_words)
    highlighted_sentence = []
    correct_index = 0
    user_index = 0

    for tag, i1, i2, j1, j2 in matcher.get_opcodes():
        if tag == 'replace' or tag == 'delete':
            for word in correct_words[i1:i2]:
                highlighted_sentence.append(f'<span style="color:red">{word}</span>')
        if tag == 'replace' or tag == 'insert':
            for word in user_words[j1:j2]:
                highlighted_sentence.append(f'<span style="color:red">{word}</span>')
        if tag == 'equal':
            for word in correct_words[i1:i2]:
                highlighted_sentence.append(word)
    
    return ' '.join(highlighted_sentence)

# テストケース
correct_sentence1 = "I like a dog."
user_sentence1 = "like a dog I"
result1 = highlight_differences(correct_sentence1, user_sentence1)
print(result1)

correct_sentence2 = "neither road will lead you to the destination."
user_sentence2 = "You will lead to the destination neither road."
result2 = highlight_differences(correct_sentence2, user_sentence2)
print(result2)