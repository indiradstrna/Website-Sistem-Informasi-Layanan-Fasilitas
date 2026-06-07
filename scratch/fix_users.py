with open('db_combined_final.sql', 'r', encoding='utf-8') as f:
    sql = f.read()

import re

# We want to find CREATE TABLE `users` ( ... ) ENGINE=InnoDB
# And add columns to it.
match = re.search(r'(CREATE TABLE `users` \(.+?)(\n\) ENGINE)', sql, re.DOTALL)
if match:
    new_sql = sql[:match.start(2)] + ",\n  `mac_address` text DEFAULT NULL,\n  `wfh_lat` decimal(11,8) DEFAULT NULL,\n  `wfh_lng` decimal(11,8) DEFAULT NULL" + sql[match.start(2):]
    with open('db_combined_final.sql', 'w', encoding='utf-8') as f:
        f.write(new_sql)
    print("Fixed users table successfully.")
else:
    print("Could not find users table pattern.")
