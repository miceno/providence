#!/usr/bin/env bash

set -a

BASE_DIR=$(dirname $0)

# Set environment variables
CACHE_DIR=${CACHE_DIR:-$BASE_DIR/mysql_profile}
DB_NAME=${DB_NAME:-ca_test}
PROFILE=${1:-testing}
COLLECTIVEACCESS_HOME="$(dirname $(dirname "$0"))"
PATH="$PATH:$COLLECTIVEACCESS_HOME/support/bin"
PHP_BIN=${PHP_BIN:-php}

PROFILE_BACKUP="$CACHE_DIR/$PROFILE.sql"

# Install the testing profile
if test ! -e $PROFILE_BACKUP -o -n "$SKIP_CACHED_PROFILE"; then
  echo "Installing profile $PROFILE..."
  mkdir -p "${CACHE_DIR}"
  "$PHP_BIN" "$COLLECTIVEACCESS_HOME"/support/bin/caUtils install --hostname=localhost --setup="tests/setup-tests.php" \
    --skip-roles --profile-name="$PROFILE" --admin-email=support@collectiveaccess.org && (
        # Export database for later faster import
        echo "Exporting database to cache file: $CACHE_DIR/$PROFILE.sql"
        sudo mysqldump -uroot --hex-blob --complete-insert --extended-insert $DB_NAME >$PROFILE_BACKUP
  )
else
  echo "Skipping profile install"
  if test -e $PROFILE_BACKUP -a -z "$SKIP_CACHED_PROFILE"; then
    echo "Found cached database file $CACHE_DIR/$PROFILE.sql. Importing..."
    sudo mysql -uroot "$DB_NAME" <$PROFILE_BACKUP
  fi
fi
