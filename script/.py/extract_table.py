import sys

with open('script/fix_table2.py', 'r', encoding='utf-8') as f:
    text = f.read()

part = text.split('new_table = """')[1]
new_table = part.split('"""')[0]

with open('script/data/extracted_table.txt', 'w', encoding='utf-8') as f:
    f.write(new_table)
print("Extract ok")
