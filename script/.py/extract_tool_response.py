import json
import re

with open(r'c:\Users\User\.gemini\antigravity-ide\brain\376b94f4-fd09-4d1f-bfd1-713c58b44a41\.system_generated\logs\transcript_full.jsonl', 'r', encoding='utf-8') as f:
    lines = f.readlines()

for i, line in enumerate(lines):
    if '"created_at":"2026-08-27T08:18:26Z"' in line and 'PLANNER_RESPONSE' in line:
        for j in range(i+1, min(i+10, len(lines))):
            next_line = lines[j]
            if '"type":"TOOL_RESPONSE"' in next_line:
                data = json.loads(next_line)
                content = data.get('content', '')
                if 'Total Lines:' in content:
                    with open(r'c:\Users\User\manajemen_praktikum_\found_content.txt', 'w', encoding='utf-8') as out:
                        out.write(content)
                    print('Found and extracted!')
                    break
        break
