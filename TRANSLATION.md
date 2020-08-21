

HOWTO Translate
===============

There are some general details about translation at CollectiveAccess Web site

https://docs.collectiveaccess.org/wiki/Creating_a_Translation

Here you will find details on how to run the commands to update PO files on your local
development environment.

Requirements
===

Use any of the following tools:

* GNU gettext 
* POEdit

GNU gettext
===

Run the following command from the source code root folder:

    msgmerge 

It will update your `PO` file.

It will parse PHP files (*.php). If you would like to include also other extension files,
you will need to use: 

    xgettext --language=Python --add-comments=TRANSLATORS --force-po -o %o --from-code=%c -k%k %F
    
where:
* `%o` is the output file.
* `%c` is the charset in case it is not the default one.
* `%k` is a keyword.
* `%F` is for your files.

Editing translation
====

Use your preferred file editor, or try it also with POEdit.

POEdit will also allow you updating the PO file from the source code. Just configure it
to point to the source code root folder.

Update translations
=====

When updating translations from source code, take into account:

1. include also `conf` folders
2. configuration files should be parsed using Python language (not PHP, since they 
don't include PHP tags).
2. exclude `Zend` folder

Translation Cache
====

CollectiveAccess uses Zend Translate class and it also caches translations.

If you are testing your translation code, remember to clear the translation files at 

```bash
$COLLECTIVEACCESS_HOME/app/tmp/ca_translation---*.*
```