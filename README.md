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

Install zero-mq:
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
