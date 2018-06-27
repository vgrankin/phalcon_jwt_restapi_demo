#!/usr/bin/env bash

# trace ERR through pipes
set -o pipefail

# trace ERR through 'time command' and other functions
set -o errtrace

# set -u : exit the script if you try to use an uninitialised variable
set -o nounset

# set -e : exit the script if any statement returns a non-true return value
set -o errexit

APPLICATION_USER=${APPLICATION_USER:-}
APPLICATION_GROUP=${APPLICATION_GROUP:-}
APPLICATION_CACHE=${APPLICATION_CACHE:-}
APPLICATION_LOGS=${APPLICATION_LOGS:-}

if [ x"$APPLICATION_USER" != x ]; then
	owner="$APPLICATION_USER"
	if [ x"$APPLICATION_GROUP" != x ]; then
		owner="${owner}:$APPLICATION_GROUP"
	fi

	if [ x"$APPLICATION_CACHE" != x ] && [ -d "$APPLICATION_CACHE" ]; then
		chown -R $owner ${APPLICATION_CACHE}
	fi

	if [ x"$APPLICATION_LOGS" != x ] && [ -d "$APPLICATION_LOGS" ]; then
		chown -R $owner ${APPLICATION_LOGS}
	fi
fi
