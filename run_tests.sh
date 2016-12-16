#!/usr/bin/env bash
DIR_THIS="$(dirname "$(readlink -f "$0")")"
${DIR_THIS}/bin/phpunit