#!/usr/bin/env bash

BASEDIR=$(dirname $0)
DUMP_FILE=${1:-$BASEDIR/tests/mysql_profile/testing.sql}

# Clean cache files
rm -rf $BASEDIR/app/tmp/collectiveaccessCache

mysql -ppassword -uca_test ca_test < "$DUMP_FILE"
