import subprocess
import os

MYSQL_BIN = r"C:\xampp\mysql\bin\mysql.exe"
MYSQLDUMP_BIN = r"C:\xampp\mysql\bin\mysqldump.exe"
SILATAS_SQL = r"C:\xampp\htdocs\silatas\database\if0_40693730_silatas.sql"
KDR_SQL = r"C:\xampp\htdocs\silatas\database\db_kdr.sql"

def run_sql(query):
    process = subprocess.Popen([MYSQL_BIN, "-u", "root"], stdin=subprocess.PIPE, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    out, err = process.communicate(query.encode('utf-8'))
    if process.returncode != 0:
        print(f"Error executing SQL: {err.decode('utf-8')}")
        exit(1)

def run_sql_file(db, filepath):
    with open(filepath, 'r', encoding='utf-8') as f:
        content = "SET FOREIGN_KEY_CHECKS=0;\n" + f.read() + "\nSET FOREIGN_KEY_CHECKS=1;"
    process = subprocess.Popen([MYSQL_BIN, "-u", "root", db], stdin=subprocess.PIPE, stdout=subprocess.PIPE, stderr=subprocess.PIPE)
    out, err = process.communicate(content.encode('utf-8'))
    if process.returncode != 0:
        print(f"Error importing {filepath} into {db}: {err.decode('utf-8')}")
        exit(1)

print("Dropping temporary databases...")
run_sql("DROP DATABASE IF EXISTS tmp_kdr; DROP DATABASE IF EXISTS db_gabungan;")

print("Creating databases...")
run_sql("CREATE DATABASE tmp_kdr; CREATE DATABASE db_gabungan;")

print("Importing SQL files...")
run_sql_file("db_gabungan", SILATAS_SQL)
run_sql_file("tmp_kdr", KDR_SQL)

print("Merging databases...")
merge_sql = """
USE db_gabungan;
SET FOREIGN_KEY_CHECKS=0;

-- Modify users table to accommodate db_kdr columns
ALTER TABLE users
MODIFY COLUMN role ENUM('user', 'supervisor', 'admin', 'super admin') NOT NULL DEFAULT 'user',
ADD COLUMN daily_workload INT DEFAULT 8,
ADD COLUMN work_radius_meters INT DEFAULT 100,
ADD COLUMN mac_address VARCHAR(255) DEFAULT NULL,
ADD COLUMN wfh_lat VARCHAR(50) DEFAULT NULL,
ADD COLUMN wfh_lng VARCHAR(50) DEFAULT NULL;

-- Merge users from db_kdr into db_gabungan.users
-- Update existing users with same employee_id
UPDATE db_gabungan.users u
JOIN tmp_kdr.users k ON u.employee_id = k.employee_id
SET 
  u.daily_workload = k.daily_workload,
  u.work_radius_meters = k.work_radius_meters,
  u.mac_address = k.mac_address,
  u.wfh_lat = k.wfh_lat,
  u.wfh_lng = k.wfh_lng,
  u.role = IF(k.role = 'super admin', 'super admin', u.role);

-- Insert new users from db_kdr that don't exist in db_gabungan.users
INSERT INTO db_gabungan.users (employee_id, password, role, daily_workload, work_radius_meters, created_at, mac_address, wfh_lat, wfh_lng)
SELECT k.employee_id, k.password, 
       IF(k.role = 'admin', 'admin', IF(k.role = 'super admin', 'super admin', 'user')),
       k.daily_workload, k.work_radius_meters, k.created_at, k.mac_address, k.wfh_lat, k.wfh_lng
FROM tmp_kdr.users k
WHERE NOT EXISTS (SELECT 1 FROM db_gabungan.users u WHERE u.employee_id = k.employee_id);

-- Remap user_id in tmp_kdr tables
-- When employee_id is same, kdr.user_id should map to gabungan.user_id
CREATE TABLE id_mapping AS
SELECT k.id AS old_id, u.id AS new_id
FROM tmp_kdr.users k
JOIN db_gabungan.users u ON k.employee_id = u.employee_id;

-- Update foreign keys in tmp_kdr
UPDATE tmp_kdr.attendance a JOIN id_mapping m ON a.user_id = m.old_id SET a.user_id = m.new_id;
UPDATE tmp_kdr.task_assignments t JOIN id_mapping m ON t.user_id = m.old_id SET t.user_id = m.new_id;
UPDATE tmp_kdr.work_sessions w JOIN id_mapping m ON w.user_id = m.old_id SET w.user_id = m.new_id;

-- Copy remaining tables from db_kdr
CREATE TABLE db_gabungan.assessments LIKE tmp_kdr.assessments; INSERT INTO db_gabungan.assessments SELECT * FROM tmp_kdr.assessments;
CREATE TABLE db_gabungan.attendance LIKE tmp_kdr.attendance; INSERT INTO db_gabungan.attendance SELECT * FROM tmp_kdr.attendance;
CREATE TABLE db_gabungan.employee_targets LIKE tmp_kdr.employee_targets; INSERT INTO db_gabungan.employee_targets SELECT * FROM tmp_kdr.employee_targets;
CREATE TABLE db_gabungan.evidence LIKE tmp_kdr.evidence; INSERT INTO db_gabungan.evidence SELECT * FROM tmp_kdr.evidence;
CREATE TABLE db_gabungan.gps_logs LIKE tmp_kdr.gps_logs; INSERT INTO db_gabungan.gps_logs SELECT * FROM tmp_kdr.gps_logs;
CREATE TABLE db_gabungan.login_logs LIKE tmp_kdr.login_logs; INSERT INTO db_gabungan.login_logs SELECT * FROM tmp_kdr.login_logs;
CREATE TABLE db_gabungan.qr_tokens LIKE tmp_kdr.qr_tokens; INSERT INTO db_gabungan.qr_tokens SELECT * FROM tmp_kdr.qr_tokens;
CREATE TABLE db_gabungan.task_assignments LIKE tmp_kdr.task_assignments; INSERT INTO db_gabungan.task_assignments SELECT * FROM tmp_kdr.task_assignments;
CREATE TABLE db_gabungan.work_sessions LIKE tmp_kdr.work_sessions; INSERT INTO db_gabungan.work_sessions SELECT * FROM tmp_kdr.work_sessions;

DROP TABLE id_mapping;
SET FOREIGN_KEY_CHECKS=1;
"""
run_sql(merge_sql)

print("Exporting db_gabungan.sql...")
export_cmd = f'"{MYSQLDUMP_BIN}" -u root db_gabungan > "C:\\xampp\\htdocs\\silatas\\database\\db_gabungan.sql"'
os.system(export_cmd)

print("Merge complete!")
