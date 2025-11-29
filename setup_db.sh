#!/bin/bash
sudo mysql -e "CREATE DATABASE IF NOT EXISTS simpleak_db_akunting;"
sudo mysql -e "CREATE USER IF NOT EXISTS 'simpleak_user_simple'@'localhost' IDENTIFIED BY '#5@8@12Yaa';"
sudo mysql -e "GRANT ALL PRIVILEGES ON simpleak_db_akunting.* TO 'simpleak_user_simple'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
echo "Database setup complete."

