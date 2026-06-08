import re

def get_insert_statement(file_path, table_name):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    # Match from INSERT INTO `table_name` up to the semicolon
    pattern = r"INSERT INTO `(?:" + table_name + r")`[^\;]+\;"
    match = re.search(pattern, content)
    if match:
        return match.group(0)
    return None

def replace_insert_statement(file_path, table_name, new_insert):
    with open(file_path, 'r', encoding='utf-8') as f:
        content = f.read()
    
    pattern = r"INSERT INTO `(?:" + table_name + r")`[^\;]+\;"
    if re.search(pattern, content):
        if new_insert:
            content = re.sub(pattern, new_insert.replace('\\', '\\\\'), content)
            with open(file_path, 'w', encoding='utf-8') as f:
                f.write(content)
            print(f"Replaced {table_name}")
        else:
            print(f"Warning: No new insert provided for {table_name}")
    else:
        # If it doesn't exist, we append it? No, it should exist
        print(f"Insert for {table_name} not found in target file")

tables = ['vehicle_requests', 'room_requests', 'repair_requests', 'zoom_requests', 'item_loan_requests', 'repair_budgets']

source_file = '../database/if0_40693730_silatas.sql'
target_file = '../database/biotrop.sql'

for table in tables:
    new_insert = get_insert_statement(source_file, table)
    replace_insert_statement(target_file, table, new_insert)

print("Done")
