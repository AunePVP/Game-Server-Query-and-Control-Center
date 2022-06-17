#!/bin/bash
# Project: Game-Server-Query-and-Control-Center
# Author: Leo Muehlhaeusler
# Contributors: https://github.com/AunePVP/Game-Server-Query-and-Control-Center/graphs/contributors
# Documentation: https://github.com/AunePVP/Game-Server-Query-and-Control-Center/wiki
# Website: https://tracker.iguaserver.de

if whiptail --yesno "Apt Update?" 10 35; then
  sudo apt-get update -y
fi
#Check if git and mysql are installed
if ! command -v git &> /dev/null
then
    sudo apt-get install git -y
fi
if ! command -v mysql &> /dev/null
then
    echo "Please install mysql"
    exit 0
fi
user=$(whoami)
wsuname=$(whiptail --inputbox --title "Installation" "Please enter your webserver username (most likely www-data)" 10 60 3>&1 1>&2 2>&3)
exitstatus=$?
[[ "$exitstatus" = 1 ]] && exit 0;
# Download Source code with git
git clone https://github.com/AunePVP/Game-Server-Query-and-Control-Center
# Set file permissions
namedir=Game-Server-Query-and-Control-Center/
user=$(whoami)
find $namedir -type d -exec chmod 770 {} \;
find $namedir -type f -exec chmod 640 {} \;
find ${namedir}html/type/arkse/ -type f -exec chmod 660 {} \;
sudo chown -R ${user}:${wsuname} $namedir
sudo mv -f ${namedir}{.,}* .
sudo rm -r $namedir

mysqlucheck="$(sudo mysql -sse "SELECT EXISTS(SELECT 1 FROM mysql.user WHERE user = '$user')")"
dbname=$(whiptail --inputbox --title "Create Database" "Please enter a name for your database" 10 100 3>&1 1>&2 2>&3)
exitstatus=$?
[[ "$exitstatus" = 1 ]] && exit 0;
mysqlpasswd=$(whiptail --passwordbox --title "Create Database" "Set a new password" 10 100 3>&1 1>&2 2>&3)
exitstatus=$?
[[ "$exitstatus" = 1 ]] && exit 0;
sudo mysql -e "CREATE DATABASE ${dbname} /*\!40100 DEFAULT CHARACTER SET utf8 */;"
if ! [ "$mysqlucheck" = 1 ]; then
  sudo mysql -e "CREATE USER ${user}@localhost IDENTIFIED BY '${mysqlpasswd}';"
fi
sudo mysql -e "GRANT ALL PRIVILEGES ON ${dbname}.* TO '${user}'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
sudo mysql -e "USE ${dbname}; CREATE TABLE users (id INT auto_increment PRIMARY KEY AUTO_INCREMENT, username VARCHAR(100) NOT NULL, password VARCHAR(100) NOT NULL, server JSON NOT NULL);"

echo "<?php" > html/config.php
echo "\$DB_SERVER = 'localhost';" >> html/config.php
echo "\$DB_USERNAME = '${user}';" >> html/config.php
echo "\$DB_NAME = '${dbname}';" >> html/config.php
sudo chown ${wsuname}:${user} html/config.php
sudo chmod 640 html/config.php
sudo apt install php-mbstring -y
clear
printf "\n"
base64 -d <<<"H4sIAAAAAAAAA8WWza3EIAyE71sF0hbAPbUg0UiU2t/yYxiwJ8DpcVgl/rA9GMPm62DENJwxGIjx
lZyEKuDzBYsP6TfoqQz462LkNFQDqIjGZ+Bn5+QsFALQRDMwkO2cnIQaQRdFUzBQ7Zzsh5pAE5XM
PhgeDGT78zByEsoEoiyzn1G5ZRCjAtXBcGEeixwGqOJ4KjFMoDl4i1gePAcBRRpNFPv6B1BrE6MX
kl6EmNpoDr5KGVkkTJPQJaXHXBDh/j3e6TH/4OvQQJjTyLEAIg8LUotWkvkxS69V1pOeQF5+nbqo
ewxF3wJuOZJ2WI+vay1K5hIFtVEx5Rk3EJusk7lmYQnm/W8Tc0VhmlI5Nmaq7D1tYJ2RORIlRkJR
YJRzVgRzyTkXS1I6CO0jlGPVCTkqdnfOqbE5KJCcl62ociTbmvp9rRplcjHKEV5F8VDvstpfm2oK
twJ6c9717BaqfwLQBDxz2RyzS+0W29AEH0pq75egnEMn1/FSz8s9pgB+VQa2bOtmacL6RbzjsQ2G
D3A62G3inufU4xTsCfyf8fkD1YkCfk4NAAA=" | gunzip
printf "\n"
sleep 2 & pid=$!
frames="< >"
while kill -0 $pid > /dev/null 2>&1;
do
    for frame in $frames;
    do
        printf "\r$frame Loading..."
        sleep 0.5
    done
done
printf "\n"
echo "Game Server Query installed!"
echo "Go to your browser, open the webpage (https://<ip/domain/localhost>/index.php) and start the setup"