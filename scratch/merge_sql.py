import re

def parse_dump(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    tables = {}
    alters = []
    buffer = []
    
    for line in content.splitlines(True):
        if not buffer and (line.startswith('--') or line.startswith('/*')):
            continue
        if not line.strip() and not buffer:
            continue
            
        buffer.append(line)
        
        if line.strip().endswith(';'):
            stmt = ''.join(buffer)
            if stmt.startswith('CREATE TABLE'):
                match = re.search(r'CREATE TABLE `([^`]+)`', stmt)
                if match:
                    tbl = match.group(1)
                    if tbl not in tables:
                        tables[tbl] = {'schema': stmt, 'inserts': []}
                    else:
                        tables[tbl]['schema'] = stmt
            elif stmt.startswith('INSERT INTO'):
                match = re.search(r'INSERT INTO `([^`]+)`', stmt)
                if match:
                    tbl = match.group(1)
                    if tbl not in tables:
                        tables[tbl] = {'schema': '', 'inserts': []}
                    tables[tbl]['inserts'].append(stmt)
            elif stmt.startswith('ALTER TABLE'):
                alters.append(stmt)
            
            buffer = []
            
    return tables, alters

silatas_tables, silatas_alters = parse_dump('../database/silatas.sql')
db_kdr_tables, db_kdr_alters = parse_dump('../database/db_kdr.sql')
if0_silatas_tables, if0_silatas_alters = parse_dump('../database/if0_40693730_silatas.sql')

all_tables = set(list(silatas_tables.keys()) + list(db_kdr_tables.keys()) + list(if0_silatas_tables.keys()))

with open('../database/db_combined_final.sql', 'w', encoding='utf-8') as out:
    out.write("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n")
    out.write("START TRANSACTION;\n")
    out.write("SET time_zone = '+00:00';\n\n")

    out.write("CREATE DATABASE IF NOT EXISTS `db_gabungan`;\n")
    out.write("USE `db_gabungan`;\n\n")

    for t in sorted(all_tables):
        schema = silatas_tables[t]['schema'] if t in silatas_tables and silatas_tables[t]['schema'] else (db_kdr_tables[t]['schema'] if t in db_kdr_tables else (if0_silatas_tables[t]['schema'] if t in if0_silatas_tables else ''))
        if schema:
            if t == 'employees':
                schema = schema.replace('`nik`', '`nip_nik`')
            out.write(schema + "\n\n")

    for t in sorted(all_tables):
        inserts = []
        if t in ['attendance', 'evidence', 'assessments', 'work_sessions', 'employee_targets', 'login_logs', 'qr_tokens', 'performance_assessments', 'monthly_assessments']:
            inserts = db_kdr_tables.get(t, {}).get('inserts', [])
        elif t in ['repair_requests', 'vehicle_requests', 'room_requests']:
            inserts = if0_silatas_tables.get(t, {}).get('inserts', [])
        elif t in ['zoom_requests', 'item_loan_requests', 'repair_budgets']:
            inserts = silatas_tables.get(t, {}).get('inserts', [])
        elif t in ['employees', 'users']:
            inserts = silatas_tables.get(t, {}).get('inserts', [])
            if not inserts:
                inserts = db_kdr_tables.get(t, {}).get('inserts', [])
        else:
            if t in silatas_tables and silatas_tables[t]['inserts']:
                inserts = silatas_tables[t]['inserts']
            elif t in db_kdr_tables and db_kdr_tables[t]['inserts']:
                inserts = db_kdr_tables[t]['inserts']
            elif t in if0_silatas_tables and if0_silatas_tables[t]['inserts']:
                inserts = if0_silatas_tables[t]['inserts']
        
        for ins in inserts:
            if t == 'employees':
                ins = ins.replace('`nik`', '`nip_nik`')
            out.write(ins + "\n\n")
            
    # Combine all unique ALTER TABLE statements
    all_alters = []
    seen = set()
    for alt in (if0_silatas_alters + silatas_alters + db_kdr_alters):
        if alt not in seen:
            seen.add(alt)
            all_alters.append(alt)
            
    for alt in all_alters:
        out.write(alt + "\n\n")

    out.write("COMMIT;\n")

print("Done generating db_combined_final.sql. Sizes:")
print("silatas tables:", len(silatas_tables))
print("db_kdr tables:", len(db_kdr_tables))
print("if0_silatas tables:", len(if0_silatas_tables))
