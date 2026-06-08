import re
from collections import defaultdict

with open('c:\\xampp\\htdocs\\silatas\\database\\biotrop.sql', 'r', encoding='utf-8') as f:
    content = f.read()

# Find where ALTER TABLE section starts
start_idx = content.find('ALTER TABLE')
if start_idx == -1:
    print("No ALTER TABLE found")
    exit()

pre_alters = content[:start_idx]
alters_part = content[start_idx:]

# Extract all ALTER TABLE blocks
# They are separated by double newlines and end with a semicolon
blocks = re.split(r'\n\n+', alters_part)

table_alters = defaultdict(list)
table_modifies = defaultdict(list)

for block in blocks:
    if not block.strip():
        continue
    if block.strip() == 'COMMIT;':
        continue
    if block.startswith('/*!'):
        continue
        
    match = re.match(r'ALTER TABLE `([^`]+)`\s+(.*);', block.strip(), re.DOTALL)
    if not match:
        continue
        
    table_name = match.group(1)
    body = match.group(2)
    
    # Split body into parts
    parts = [p.strip() for p in body.split(',\n')]
    
    for part in parts:
        if part.startswith('MODIFY'):
            table_modifies[table_name].append(part)
        else:
            table_alters[table_name].append(part)

# Deduplicate
final_alters = []

for table in sorted(set(list(table_alters.keys()) + list(table_modifies.keys()))):
    # Process ADD PRIMARY KEY, ADD KEY, ADD UNIQUE KEY, ADD CONSTRAINT
    pk_added = False
    unique_keys = {}
    keys = {}
    constraints = {}
    
    for part in table_alters[table]:
        if part.startswith('ADD PRIMARY KEY'):
            if not pk_added:
                pk_added = True
                final_alters.append(f"ALTER TABLE `{table}`\n  {part};")
        elif part.startswith('ADD UNIQUE KEY'):
            match = re.search(r'ADD UNIQUE KEY `([^`]+)`', part)
            if match:
                kname = match.group(1)
                if kname not in unique_keys:
                    unique_keys[kname] = part
        elif part.startswith('ADD KEY'):
            match = re.search(r'ADD KEY `([^`]+)`', part)
            if match:
                kname = match.group(1)
                if kname not in keys:
                    keys[kname] = part
        elif part.startswith('ADD CONSTRAINT'):
            match = re.search(r'ADD CONSTRAINT `([^`]+)`', part)
            if match:
                kname = match.group(1)
                if kname not in constraints:
                    constraints[kname] = part

    # Group ADD KEYs and ADD UNIQUE KEYs together if possible, or just emit them
    table_adds = []
    if unique_keys:
        table_adds.extend(unique_keys.values())
    if keys:
        table_adds.extend(keys.values())
        
    if table_adds:
        final_alters.append(f"ALTER TABLE `{table}`\n  " + ",\n  ".join(table_adds) + ";")
        
    if constraints:
        for c in constraints.values():
            final_alters.append(f"ALTER TABLE `{table}`\n  {c};")
            
    # Process MODIFY
    if table_modifies[table]:
        # Find the one with highest AUTO_INCREMENT
        max_ai = 0
        best_mod = table_modifies[table][0]
        for mod in table_modifies[table]:
            m = re.search(r'AUTO_INCREMENT=(\d+)', mod)
            if m:
                val = int(m.group(1))
                if val > max_ai:
                    max_ai = val
                    best_mod = mod
        final_alters.append(f"ALTER TABLE `{table}`\n  {best_mod};")

with open('c:\\xampp\\htdocs\\silatas\\database\\biotrop.sql', 'w', encoding='utf-8') as f:
    f.write(pre_alters)
    f.write("\n".join(final_alters))
    f.write("\n\nCOMMIT;\n")
    # append the end comments
    f.write("/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;\n")
    f.write("/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;\n")
    f.write("/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;\n")

print("Deduplicated ALTER TABLE statements.")
