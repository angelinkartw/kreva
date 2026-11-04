import json

durations = []
with open('stress-test-result2.json', 'r') as f:
    for line in f:
        line = line.strip()
        if not line: continue
        try:
            d = json.loads(line)
            if d.get('metric') == 'http_req_duration' and d.get('type') == 'Point':
                durations.append(d['data']['value'])
        except:
            pass

if durations:
    avg = sum(durations) / len(durations)
    print(f"Avg: {avg:.2f}ms")
    print(f"Max: {max(durations):.2f}ms")
    print(f"Min: {min(durations):.2f}ms")
    print(f"Count: {len(durations)}")
else:
    print("No duration data found.")
