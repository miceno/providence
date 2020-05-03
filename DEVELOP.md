
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

Setup
 
    vagrant up

For Virtualbox, add add-ons plugin

    vagrant plugin install ssh 
    vagrant plugin install vagrant-vbguest 
    vagrant plugin install vagrant-scp 

Testing
=======

From the command line:

    $ php $COLLECTIVEACCESS_HOME/vendor/phpunit/phpunit/phpunit \
    --configuration $COLLECTIVEACCESS_HOME/tests/phpunit.xml \
    TimeExpressionParserTest \
    $COLLECTIVEACCESS_HOME/tests/lib/Parsers/TimeExpressionParserTest.php
    

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