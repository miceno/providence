#!/usr/bin/env bash

# Installs a CollectiveAccess profile.
# It also applies schema updates and dumps the database to allow faster
# install in future runs.
#
# Usage
#    profile_setup.sh PROFILE
#
# Params:
#  PROFILE: name of the profile to install

# Variables:
#
# * USE_CACHED_PROFILE: in case it is defined, it will use a dump of the database, and will
#   not install the profile.
#

export BASE_DIR=$(dirname $0)

# Set environment variables
export CACHE_DIR=${CACHE_DIR:-$BASE_DIR/mysql_profile}
export DB_NAME=${DB_NAME:-ca_test}
export PROFILE=${1:-testing}
export COLLECTIVEACCESS_HOME="$(dirname $(dirname "$0"))"
export PATH="$PATH:$COLLECTIVEACCESS_HOME/support/bin"
export PHP_BIN=${PHP_BIN:-php}

# Install the testing profile
if test -z "$USE_CACHED_PROFILE"; then
  echo "Installing profile $PROFILE..."
  "$PHP_BIN" "$COLLECTIVEACCESS_HOME"/support/bin/caUtils install --hostname=localhost \
    --setup="tests/setup-tests.php" \
    --skip-roles --profile-name="$PROFILE" --admin-email=support@collectiveaccess.org
else
  echo "Skipping profile install"
  if test -e "$CACHE_DIR/$PROFILE.sql"; then
    echo "Found cached database file $CACHE_DIR/$PROFILE.sql. Importing..."
    sudo mysql -uroot "$DB_NAME" <"$CACHE_DIR/$PROFILE.sql"
  fi
fi

# Update database schema.
"$PHP_BIN" "$COLLECTIVEACCESS_HOME"/support/bin/caUtils update-database-schema \
  --hostname=localhost --setup="tests/setup-tests.php" \
  --yes
# Export database for later faster import
echo "Exporting database to cache file: $CACHE_DIR/$PROFILE.sql"
sudo mysqldump -uroot --hex-blob --complete-insert --extended-insert $DB_NAME >"$CACHE_DIR/$PROFILE.sql"
