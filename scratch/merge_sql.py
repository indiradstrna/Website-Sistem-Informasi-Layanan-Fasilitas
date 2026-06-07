import re

def parse_dump(file_path):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()

    # Split by double newlines since mysqldump separates statements by \n\n usually
    # or we can do a simple character-by-character parser
    
    tables = {}
    current_table = None
    statement_type = None
    buffer = []
    
    in_string = False
    escape_next = False
    
    for line in content.splitlines(True):
        if not buffer and (line.startswith('--') or line.startswith('/*')):
            continue
        if not line.strip() and not buffer:
            continue
            
        buffer.append(line)
        
        # We need to know if the statement has ended.
        # A statement ends with ';' not inside a string.
        # Let's just do a naive check: if the line ends with ';' and we are not doing anything crazy.
        if line.strip().endswith(';'):
            # check if it's a complete statement roughly
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
            
            buffer = []
            
    return tables

silatas = parse_dump('silatas.sql')
db_kdr = parse_dump('db_kdr.sql')

all_tables = set(list(silatas.keys()) + list(db_kdr.keys()))

with open('db_combined_final.sql', 'w', encoding='utf-8') as out:
    out.write("SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n")
    out.write("START TRANSACTION;\n")
    out.write("SET time_zone = '+00:00';\n\n")

    out.write("CREATE DATABASE IF NOT EXISTS `db_gabungan`;\n")
    out.write("USE `db_gabungan`;\n\n")

    for t in sorted(all_tables):
        schema = silatas[t]['schema'] if t in silatas and silatas[t]['schema'] else (db_kdr[t]['schema'] if t in db_kdr else '')
        if schema:
            out.write(schema + "\n\n")

    for t in sorted(all_tables):
        inserts = []
        if t in ['attendance', 'evidence', 'assessments', 'work_sessions', 'employee_targets', 'login_logs', 'qr_tokens', 'performance_assessments', 'monthly_assessments']:
            inserts = db_kdr.get(t, {}).get('inserts', [])
        elif t in ['repair_requests', 'zoom_requests', 'item_loan_requests', 'vehicle_requests', 'room_requests', 'repair_budgets']:
            inserts = silatas.get(t, {}).get('inserts', [])
        elif t in ['employees', 'users']:
            inserts = silatas.get(t, {}).get('inserts', [])
            if not inserts:
                inserts = db_kdr.get(t, {}).get('inserts', [])
        else:
            if t in silatas and silatas[t]['inserts']:
                inserts = silatas[t]['inserts']
            elif t in db_kdr and db_kdr[t]['inserts']:
                inserts = db_kdr[t]['inserts']
        
        for ins in inserts:
            out.write(ins + "\n\n")

    out.write("COMMIT;\n")

print("Done generating db_combined_final.sql. Sizes:")
print("silatas tables:", len(silatas))
print("db_kdr tables:", len(db_kdr))
