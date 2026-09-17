import json

transcript_path = r'c:\Users\User\.gemini\antigravity-ide\brain\376b94f4-fd09-4d1f-bfd1-713c58b44a41\.system_generated\logs\transcript_full.jsonl'
output_path = 'script/data/extracted_view.txt'

with open(transcript_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

found_planner = False
for i, line in enumerate(lines):
    if '"created_at":"2026-08-27T08:18:26Z"' in line and 'PLANNER_RESPONSE' in line:
        found_planner = True
    elif found_planner and 'TOOL_RESPONSE' in line:
        try:
            data = json.loads(line)
            content = data.get('content', '')
            if 'The following code has been modified' in content:
                # We found the tool response!
                clean_lines = []
                capture = False
                for cl in content.split('\n'):
                    if 'The following code has been modified' in cl:
                        capture = True
                        continue
                    if capture:
                        if 'The above content does NOT show' in cl:
                            break
                        parts = cl.split(': ', 1)
                        if len(parts) == 2 and parts[0].isdigit():
                            clean_lines.append(parts[1])
                        else:
                            # if it's an empty line that just matched as digit:
                            if cl.strip().isdigit() and cl.endswith(':'):
                                clean_lines.append('')
                            else:
                                clean_lines.append(cl)
                
                with open(output_path, 'w', encoding='utf-8') as out:
                    out.write('\n'.join(clean_lines))
                print(f'Extracted successfully! Length: {len(clean_lines)} lines.')
                break
        except Exception as e:
            print(e)
            pass