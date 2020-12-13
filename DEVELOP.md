
Composer
========

Install composer.

Install dependencies:

    php composer.phar install 

Update dependencies 
=============

Dependencies are locked, but not up to date.

To update dependencies:

    php composer.phar update 

Then you will get a lot of updates on your `vendor` folder, and on the `composer.lock` file.


Vagrant
=======

For Virtualbox, first add add-ons plugin

    vagrant plugin install ssh 
    vagrant plugin install vagrant-vbguest 
    vagrant plugin install vagrant-scp 

Then bring up the VM:
 
    vagrant up

Run a custom vagrant configuration:

    VAGRANT_VAGRANTFILE=Vagrantfile.focal \
    VAGRANT_DOTFILE_PATH=.vagrant_focal \
    vagrant up

Testing
=======

From the command line:

    $ php $COLLECTIVEACCESS_HOME/vendor/phpunit/phpunit/phpunit \
    --configuration $COLLECTIVEACCESS_HOME/tests/phpunit.xml \
    TimeExpressionParserTest \
    $COLLECTIVEACCESS_HOME/tests/lib/Parsers/TimeExpressionParserTest.php
    
You can create a dump of the testing environment for faster test initialization. 

1. Make sure you have allowed install overwrite temporarily on `tests/setup-tests.php` to run it:
    ```php
    if (!defined('__CA_ALLOW_INSTALLER_TO_OVERWRITE_EXISTING_INSTALLS__')) {
    	define('__CA_ALLOW_INSTALLER_TO_OVERWRITE_EXISTING_INSTALLS__', true);
    }
    ```
1. Run:

    ```shell script
        php7.2 support/bin/caUtils install \
            --setup=tests/setup-tests.php \
            --admin-email=info@example.com \
            --profile-name=testing \
            --overwrite
    ```
1. Dump:
    ```shell script
    mysqldump -uca_test \
       -ppassword \
       --hex-blob \
       --complete-insert \
       --extended-insert \
       ca_test > tests/mysql_profile/testing.sql
    ``` 


PHPUnit < 7
===========

Running tests using the built-in run templates for PHPUnit on PHPStorm 2019 is not possible, 
since PHPStorm supposes PHPUnit
 honors option `cache-result-file`, and it breaks with errors if PHPUnit will not support it. 
 This is an example of running 
 tests with PHPUnit 5.7 on PHPStorm 2019:
 
 
     PHPUnit 5.7.0 by Sebastian Bergmann and contributors.
     
     unrecognized option --cache-result-file
     
     Process finished with exit code 1
   
   

ca_ES date locale
=================

Se ha creado un fichero de unittest para probar todas las expresiones de fecha en catalan.

De esta forma se puede modificar el fichero TimeExpressionParser/ca_ES.lang y realizar unas pruebas de regresión.

Se pueden extraer todas las formas de fecha aceptadas procesando con `sed` el fichero de unittest.

Se trata de buscar las expresiones

    $vb_res = $o_tep->parse('començaments del XIXè segle'); // Comment
    $this->assertEquals($o_tep->getText(), "principis del XIXè segle"); // Comment
    
El script `support/scripts/extract_date_expression.sh.txt` permite extraerlas, y genera
un informe ordenado, marcando las formas canónicas de visualización (con tres asteriscos `*`).

Database setup
==============

Importing a database
====================

1. drop existing database
   ```shell
   echo "drop database collectiveaccess;" | mysql -u root -p
   ```
1. create database
   ```shell
   echo "create database if not exists collectiveaccess;" | mysql -u root -p
   ```
1. import database
   ```shell
   mysql -u root -p collectiveaccess < DATABASE_BACKUP.sql 
   ```

Migrations
==========