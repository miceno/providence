
#

BASEDIR=$(dirname $0)

rm -rf $BASEDIR/app/tmp/collectiveaccessCache

# cat >app/tmp/.htaccess <<EOF
# Order allow,deny
# Deny from all
# EOF


mysql -ppassword -u ca_test ca_test < $BASEDIR/tests/mysql_profile/testing.sql
