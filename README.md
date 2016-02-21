# MSpace

[![Build Status](https://api.travis-ci.org/cakephp/app.png)](https://travis-ci.org/cakephp/app)
[![License](https://poser.pugx.org/cakephp/app/license.svg)](https://packagist.org/packages/cakephp/app)

A platform for the soundviecher community with [CakePHP](http://cakephp.org) 3.0.

This is an unstable repository and should be treated as an alpha.

## Installation

# Requirements: Ubuntu 12.04 Server, SSH-Root Access


# strang ovh stuff

add line "ifconfig lo 127.0.0.1 netmask 255.0.0.0 up" to /etc/rc.local


# Kill Apache2 if existent
# -------------------

sudo apt-get remove apache2*

# Install Packages:
# -------------------

# preparation for php 5.5 instead of 5.3
sudo apt-get update

sudo apt-get install python-software-properties
sudo add-apt-repository ppa:ondrej/php5
sudo apt-get update

sudo apt-get install mysql-server mysql-client php5 php5-fpm php5-mysql nginx git php5-dev libpcre3-dev gcc make libpcre3-dev php-pear pkg-config libtool build-essential autoconf automake uuid-dev 


# Compile Phalcon:
# -------------------

	git clone --depth=1 git://github.com/phalcon/cphalcon.git
	cd cphalcon/build
	sudo ./install

# Add a file called 30-phalcon.ini in /etc/php5/conf.d/ with this content:
# extension=phalcon.so

echo 'extension=phalcon.so' | sudo tee /etc/php5/fpm/conf.d/30-phalcon.ini > /dev/null
sudo service php5-fpm restart


# Install zero-mq:
# -------------------

# http://php.net/manual/de/zmq.setup.php

cd
wget http://download.zeromq.org/zeromq-4.0.5.tar.gz
tar -xvf zeromq-4.0.5.tar.gz 
cd zeromq-4.0.5
./configure	
make
sudo make install

sudo sed -i 'extension=zmq.so' /etc/php5/fpm/php.ini
sudo sed -i 'extension=zmq.so' /etc/php5/cli/php.ini
sudo service php5-fpm restart



# Configure Nginx:
# -------------------

server {
    listen   80;
    server_name MSpace.stage;

    index index.php index.html index.htm;
    set $root_path '/home/cite/MSpace/webroot/';
    root $root_path;

    try_files $uri $uri/ @rewrite;

    location @rewrite {
        rewrite ^/(.*)$ /index.php?_url=/$1;
    }

    location ~ \.php {
        fastcgi_pass unix:/var/run/php5-fpm.sock;
        fastcgi_index /index.php;

        include /etc/nginx/fastcgi_params;

        fastcgi_split_path_info       ^(.+\.php)(/.+)$;
        fastcgi_param PATH_INFO       $fastcgi_path_info;
        fastcgi_param PATH_TRANSLATED $document_root$fastcgi_path_info;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }

    location ~* ^/(css|img|js|flv|swf|download)/(.+)$ {
        root $root_path;
    }

    location ~ /\.ht {
        deny all;
    }
}

# Configure Fpm:
# -------------------

# set following value

# listen = /var/run/php5-fpm.sock

# listen.owner = www-data
# listen.group = www-data
# listen.mode = 0660

sudo service php5-fpm restart

# Clone code
# -------------------

git clone https://github.com/Atarax/MSpace.git
cd MSpace/

# Run composer
# -------------------

./composer.phar self-update	
./composer.phar update
./composer.phar install

# Application Configuration
# -------------------

adjust values in app/config.ini and in app/environment.ini
enable short tags!!

# Ratchet-server (theViech) setup:
# -------------------

sudo ./installTheViechService.sh

# Ratchet-server (theViech) setup:
# -------------------

# seems to be necessary for ovh.net vm configuration (ubuntu 12.02)

sudo update-rc.d nginx defaults
sudo update-rc.d mysql defaults
sudo update-rc.d php5-fpm defaults	


