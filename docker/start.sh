#!/bin/bash
set -e
DIR=$(pwd)
COMMAND=${1}

echo "K-Link Streaming Service start in progress..."

/usr/local/bin/configure.sh && exec /usr/bin/supervisord