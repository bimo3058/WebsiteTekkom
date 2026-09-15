import json

transcript_path = r'c:\Users\User\.gemini\antigravity-ide\brain\376b94f4-fd09-4d1f-bfd1-713c58b44a41\.system_generated\logs\transcript_full.jsonl'

with open(transcript_path, 'r', encoding='utf-8') as f:
    for line in f:
        try:
            data = json.loads(line)
        except:
            continue
        
        # Start looking from today's first prompt
        if data.get('created_at', '') < '2026-08-27T08:18:00Z':
            continue

        if data.get('type') == 'PLANNER_RESPONSE':
            for call in data.get('tool_calls', []):
                args = call.get('args', {})
                name = call.get('name', '')
                
                # Check if it modifies the file
                if name in ['replace_file_content', 'multi_replace_file_content', 'write_to_file']:
                    target = str(args)
                    if 'pendaftaran-koor.blade.php' in target:
                        print(f"Time: {data.get('created_at')} | Tool: {name}")
                        if name == 'replace_file_content':
                            print('Replaced Target:\n', args.get('TargetContent', '')[:300])
                        elif name == 'multi_replace_file_content':
                            for chunk in args.get('ReplacementChunks', []):
                                print('Replaced Chunk Target:\n', chunk.get('TargetContent', '')[:300])
                        elif name == 'write_to_file':
                            print('Wrote entire file!')
                        print('-'*40)
