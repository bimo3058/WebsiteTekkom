import json
import sys
import re

transcript_path = r'c:\Users\User\.gemini\antigravity-ide\brain\376b94f4-fd09-4d1f-bfd1-713c58b44a41\.system_generated\logs\transcript_full.jsonl'
output_path = 'script/recovered_pendaftaran_koor.blade.php'

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
                match = re.search(r'Showing lines \d+ to \d+\n(.*?)(?=\nThe above content does NOT show|\Z)', content, re.DOTALL)
                if match:
                    code = match.group(1)
                    # Remove line numbers (e.g. "123: ")
                    code = re.sub(r'^\d+:\s?', '', code, flags=re.MULTILINE)
                    if len(code) > max_length:
                        max_length = len(code)
                        best_content = code
        
        # Check tool calls for replace_file_content / multi_replace / write_to_file
        if data.get('type') == 'PLANNER_RESPONSE':
            tool_calls = data.get('tool_calls', [])
            for call in tool_calls:
                args = call.get('args', {})
                target_file = str(args.get('TargetFile', args.get('AbsolutePath', '')))
                if 'pendaftaran-koor.blade.php' in target_file:
                    if call.get('name') == 'write_to_file':
                        code = args.get('CodeContent', '')
                        if len(code) > max_length:
                            max_length = len(code)
                            best_content = code

if best_content:
    with open(output_path, 'w', encoding='utf-8') as out:
        out.write(best_content)
    print(f"Recovered {len(best_content)} chars.")
else:
    print("No content found.")
