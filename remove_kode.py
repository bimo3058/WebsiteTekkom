import os

path = r'c:\Users\User\manajemen_praktikum_\Modules\EOffice\resources\views\manajemen-praktikum'
search1 = 'if(isset(->kode) && ->kode)  .= \x22 [{->kode}]\x22;'
search2 = 'if(->kode)  .= \x22 [{->kode}]\x22;'

count = 0
for root, dirs, files in os.walk(path):
    for file in files:
        if file.endswith('.blade.php'):
            filepath = os.path.join(root, file)
            with open(filepath, 'r', encoding='utf-8') as f:
                content = f.read()
            
            new_content = content
            if search1 in new_content:
                new_content = new_content.replace(search1, '')
            if search2 in new_content:
                new_content = new_content.replace(search2, '')
            
            if new_content != content:
                with open(filepath, 'w', encoding='utf-8') as f:
                    f.write(new_content)
                count += 1
                print(f'Updated {filepath}')
print(f'Total updated: {count}')
