#!/bin/bash

tests_db=$1
tests_suite=$2

# Prepares and restores DB
mysql -u root -proot -h db -e "CREATE DATABASE $tests_db"
DBCONN="-h db -u root -proot"
fCreateTable=""
fInsertData=""
for TABLE in `echo "SHOW TABLES" | mysql $DBCONN tests_db | tail -n +2`; do
        createTable=`echo "SHOW CREATE TABLE ${TABLE}"|mysql -B -r $DBCONN tests_db|tail -n +2|cut -f 2-`
        fCreateTable="${fCreateTable} ; ${createTable}"
        insertData="INSERT INTO ${tests_db}.${TABLE} SELECT * FROM tests_db.${TABLE}"
        fInsertData="${fInsertData} ; ${insertData}"
done;
echo "$fCreateTable ; $fInsertData" | mysql $DBCONN $tests_db

# Creating clone of Joomla site
mkdir -p tests/$tests_suite/joomla-cms
rsync -a tests/joomla-cms/ tests/$tests_suite/joomla-cms
sed -i "s/db = 'tests_db'/db = '$tests_db'/g" tests/$tests_suite/joomla-cms/configuration.php
sed -i "s,joomla-cms/,$tests_suite/joomla-cms/,g" tests/$tests_suite/joomla-cms/configuration.php
touch tests/.cache.setup.$tests_suite.tmp