import json
import re

transcript_path = r'c:\Users\User\.gemini\antigravity-ide\brain\376b94f4-fd09-4d1f-bfd1-713c58b44a41\.system_generated\logs\transcript_full.jsonl'
output_path = r'c:\Users\User\manajemen_praktikum_\recovered_pendaftaran_koor.blade.php'

found_planner = False
content_captured = []

with open(transcript_path, 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if '"created_at":"2026-07-30T16:34:52Z"' in line and 'PLANNER_RESPONSE' in line:
        found_planner = True
    elif found_planner and 'TOOL_RESPONSE' in line:
        try:
            data = json.loads(line)
            content = data.get('content', '')
            if 'The following code has been modified' in content:
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
                            content_captured.append(parts[1])
                        else:
                            if cl.strip().isdigit() and cl.endswith(':'):
                                content_captured.append('')
                            else:
                                content_captured.append(cl)
                
                with open(output_path, 'w', encoding='utf-8') as out:
                    out.write('\n'.join(content_captured))
                print(f'Extracted successfully! Length: {len(content_captured)} lines.')
                break
        except Exception as e:
            print(e)
            pass
