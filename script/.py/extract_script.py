import json

transcript_path = r'c:\Users\User\.gemini\antigravity-ide\brain\376b94f4-fd09-4d1f-bfd1-713c58b44a41\.system_generated\logs\transcript_full.jsonl'
output_path = r'c:\Users\User\manajemen_praktikum_\extracted_step.json'

found_view = False
with open(transcript_path, 'r', encoding='utf-8') as f, open(output_path, 'w', encoding='utf-8') as out:
    for line in f:
        try:
            data = json.loads(line)
        except:
            continue
            
        if data.get('created_at', '') == '2026-08-27T08:18:26Z' and data.get('type') == 'PLANNER_RESPONSE':
            found_view = True
        
        elif found_view and data.get('type') == 'TOOL_RESPONSE':
            out.write(line)
            break
