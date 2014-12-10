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

## Ratchet-server (theViech) setup:

Creating a daemon in Linux is as simple as creating a BASH/SH script in /etc/init.d. BASH is NOT part of this short tutorial so I won't go into the details of the script itself.

To create a daemon create a script in /etc/init.d. To simplify the creation you can copy the skeleton script in /etc/init.d/skeleton. Your script need to handle the following daemon commands:
start
stop
restart/reload
force-reload
status

cp /etc/init.d/skeleton /etc/init.d/daemonName
sudo chmod 775 /etc/init.d/daemonName

To enable the daemon to start at boot:
update-rc.d daemonName defaults 97 03

http://manpages.ubuntu.com/manpages/hardy/man8/update-rc.d.8.html


To disable the daemon from starting at boot:

update-rc.d -f daemonName remove

## Installation

1. Download [Composer](http://getcomposer.org/doc/00-intro.md) or update `composer self-update`.
2. Run `php composer.phar create-project --prefer-dist -s dev cakephp/app [app_name]`.

If Composer is installed globally, run
```bash
composer create-project --prefer-dist -s dev cakephp/app [app_name]
```

You should now be able to visit the path to where you installed the app and see
the setup traffic lights.

## Configuration

Read and edit `config/app.php` and setup the 'Datasources' and any other
configuration relevant for your application.
