#!/usr/bin/env bash

BASEDIR=$(dirname $0)

# Clean cache files
rm -rf $BASEDIR/app/tmp/collectiveaccessCache

mysql -ppassword -uca_test ca_test < $BASEDIR/tests/mysql_profile/testing.sql
