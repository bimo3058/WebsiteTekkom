import json
import sys
import re

transcript_path = r'c:\Users\User\.gemini\antigravity-ide\brain\376b94f4-fd09-4d1f-bfd1-713c58b44a41\.system_generated\logs\transcript_full.jsonl'
output_path = r'c:\Users\User\manajemen_praktikum_\recovered_pendaftaran_koor.blade.php'

best_content = None
max_length = 0

with open(transcript_path, 'r', encoding='utf-8') as f:
    for line in f:
        try:
            data = json.loads(line)
        except:
            continue
        
        # Check tool responses for view_file
        if data.get('type') == 'TOOL_RESPONSE' and 'view_file' in str(data):
            content = data.get('content', '')
            if 'pendaftaran-koor.blade.php' in content and 'File Path:' in content:
                # Extract file contents