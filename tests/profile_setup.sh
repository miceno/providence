#!/usr/bin/env bash

# Set environment variables
export CACHE_DIR=${CACHE_DIR:-mysql_profile}
export DB_NAME=${DB_NAME:-ca_test}
export PROFILE=${1:-testing}
export COLLECTIVEACCESS_HOME="$(dirname $(dirname "$0"))"
export PATH="$PATH:$COLLECTIVEACCESS_HOME/support/bin"

# Install the testing profile
if test ! -e "$CACHE_DIR/$PROFILE.sql";
then
  "$COLLECTIVEACCESS_HOME"/support/bin/caUtils install --hostname=localhost --setup="tests/setup-tests.php" \
    --skip-roles --profile-name="$PROFILE" --admin-email=support@collectiveaccess.org
else
  echo "Skipping profile install, using cached database on $CACHE_DIR/$PROFILE.sql"
fi

# Export database for later faster import
echo "Caching database to $CACHE_DIR/$PROFILE.sql"
sudo mysqldump -uroot $DB_NAME > "$CACHE_DIR/$PROFILE.sql"
