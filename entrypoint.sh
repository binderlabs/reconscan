#!/bin/bash

# if mariadb error encounters then run blow commented command
# cd /var/lib/mysql
# ls
# rm -r *
# mysql_install_db --user=mysql --basedir=/usr --datadir=/var/lib/mysql
# service mariadb start
service mariadb start
service nginx start
service php8.1-fpm start
chmod -R ug+rwx /home/reconscan/storage /home/reconscan/bootstrap/cache
chgrp -R www-data /home/reconscan/storage /home/reconscan/bootstrap/cache
php /home/reconscan/artisan key:generate
php /home/reconscan/artisan cache:clear
php /home/reconscan/artisan migrate:fresh
tail -f 