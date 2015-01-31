# Viechbook

                                                                            `...`
                                                                          `sNNMNNmy+.
            ./oyhhy+.                                                     +MMMMMMMMMNy-
         .+dNNMMMMMMd`                                                    +MMMMMMMMMMMNy.
       .omNMMMMMMMMMN-                                                    .dMMMMMMMMMMMMd-
      /mNMMMMMMMMMMMMy`                                                 `/dNMMMMMMMMMMMMMd`
     :NMMMMMMMMMMMMMMNd-                 .-:+oossssoo+/:.`            .omNMMMMMMMMMMMMMMMM:
    `dMMMMMMMMMMMMMMMMMNs-`         `:+ydNNNNMMMMMMMMNNNNmho:.     `:yNMMMMMMMMMMMMMMMMMMN-
    `dMMMMMMMMMMMMMMMMMMMNdo-`   `/ymNNMMMMMMMMMMMMMMMMMMMMMNmy/.`/hNMMMMMMMMMMMMNsshddhs:
     /dNNNNNhmMMMMMMMMMMMMMMNh/:smNMMMMMMMMMMMMMMMMMMMMMMMMMMMMNmmNMMMMMMMMMMMMMm/` ````
      `.--..`.yNMMMMMMMMMMMMMMNNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNy.
              `:hNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMN+`
                `+mMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMd:
                  .sNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNdhhhdmNMMMMMMMMMMMMNy.
                    -yNMMMMMMMMMMMMNNMMMMMMMMMMMMMMMd+-`````./yNMMMMMMMMMm/`
                     `/dMMMMMMMMMMMdhmMMmyymMMMMMMMh.         `+NMMMMMMMMs
                       .NMMMMMMMMMMMmhhs:+dNMMMMMMN/            yMMMMMMMMm`
                       -NMMMMMMMMMMMms:/yNMMMMMMMMN+            sMMMMMMMMM:
                       /MMMMMMMMMMNy:+dNmhdNMMMMMMMm/`        `:mMMMMMMMMM+
                       +MMMMMMMMMMNmmNMMMNmNMMMMMMMMNh/-.```-/yNMMMMMMMMMMo
                       /MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNmddddmNMMMMMMMMMMMM+
                       -MMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMM:
                       `mMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMm`
                        /NMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMN+
                        `yMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMh`
                         `hMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMd.
                          .yNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMh-
                           `omMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNs`
                             -yNMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNh-
                              `-ymMMMMMMMMMMMMMMMMMMMMMMMMMMMMMNNy:`
                                 .+hNNMMMMMMMMMMMMMMMMMMMMMMMNho-
                                    -+mMMMMMMMMMMMMMMMMMMMNy/-`
                                      /NMMMMMMMMMMMMMMMMMMo
                                      `yMMMMMMMMMMMMMMMMMN.
                                       .NMMMMMMMMMMMMMMMMd
                                        oMMMMMMMMMMMMMMMMs
                                        `dMMMMMMMMMMMMMMN:
                                       ``oMMMMMMMMMMMMMMN.
                                   `:shdmNMMMMMMMMMMMMMMNhy+:.`
                                  .dNMMMMMMMNNmmmmmNNNMMMMMMMNh/
                                  `hNMMMMMMMhsyhhhyyymMMMMMMMMMd
                                    -+ydNNNMMMNMMMMMMMMMMMNNNms-
                                        `.:/+osyyyyyyyyss+/:.

[![Build Status](https://api.travis-ci.org/cakephp/app.png)](https://travis-ci.org/cakephp/app)
[![License](https://poser.pugx.org/cakephp/app/license.svg)](https://packagist.org/packages/cakephp/app)

A platform for the soundviecher community with [CakePHP](http://cakephp.org) 3.0.

This is an unstable repository and should be treated as an alpha.

## Installation

# Requirements: Ubuntu 12.04 Server, SSH-Root Access

# Kill Apache2 if existent
# -------------------

sudo apt-get remove apache2*

# Install Packages:
# -------------------

sudo apt-get install mysql-server mysql-client php5 php5-fpm php5-mysql nginx git php5-dev libpcre3-dev gcc make php5-dev libpcre3-dev


# Compile Phalcon:
# -------------------

git clone --depth=1 git://github.com/phalcon/cphalcon.git
cd cphalcon/build
sudo ./install

# Add a file called 30-phalcon.ini in /etc/php5/conf.d/ with this content:
# extension=phalcon.so

echo 'extension=phalcon.so' | sudo tee /etc/php5/fpm/conf.d/30-phalcon.ini > /dev/null
sudo service php5-fpm restart


# Configure Nginx:
# -------------------

	server {
		 server_name viechbook.dev;
		 listen   80;

		 # root directive should be global
		 root   /home/cite/viechbook/webroot;
		 index  index.php;

		 location / {
		     try_files $uri $uri/ /index.php?$args;
		 }

		 location ~ \.php$ {
		     try_files $uri =404;
		     include /etc/nginx/fastcgi_params;
		     fastcgi_pass    unix:/var/run/php5-fpm.sock;
		     fastcgi_index   index.php;
		     fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
		 }
	}

# be sure that listen ist set to this 'listen = /var/run/php5-fpm.sock' in '/etc/php5/fpm/pool.d/www.conf' 



Install zero-mq:
-------------------
http://php.net/manual/de/zmq.setup.php


1. Download [Composer](http://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `php composer.phar install`.

If Composer is installed globally, run
```bash
composer create-project --prefer-dist -s dev cakephp/app [app_name]
```

You should now be able to visit the path to where you installed the app and see
the setup traffic lights.

## Ratchet-server (theViech) setup:

To add a function to the initialization procedure:
Write a script that performs the desired function.
Test the script to make certain it behaves as expected. Be sure any environment variables used in the script are defined at startup.
Name the file so that it begins with the uppercase letter ``P'', ``S'', ``I'', or ``K'' followed by a two-digit number indicating the order in which it should be executed relative to the other files in the directory, and ends with a name that describes the script's function. For example, S80lp handles print service startup. It will be executed after any script that begins with S79, and before any that begins with S81. You must follow this naming convention to ensure that your script is executed at the proper time.
Note that a set of scripts whose names start with P77, P78, and P79 will be executed concurrently. S80lp will not start until they have all exited.

Copy the script into the /etc/rc2.d directory so that it is executed by rc2 when the system enters (or leaves) multiuser mode.



## Configuration

Read and edit `config/app.php` and setup the 'Datasources' and any other
configuration relevant for your application.
