#!/bin/bash

# Check if the required number of arguments are provided
if [ "$#" -ne 5 ]; then
    echo "Usage: $0 <period-day> <invest$> <begin-date> <end-date> <buyback-date>"
    exit 1
fi

# Assign arguments to variables
arg1=$1
arg2=$2
arg3=$3
arg4=$4
arg5=$5

# Run php commands with the provided arguments

php dca.php acdd04  "$arg1" "$arg2" "$arg3" "$arg4" "$arg5"
php dca.php acps10  "$arg1" "$arg2" "$arg3" "$arg4" "$arg5"
php dca.php acps02  "$arg1" "$arg2" "$arg3" "$arg4" "$arg5"
php dca.php ackh19  "$arg1" "$arg2" "$arg3" "$arg4" "$arg5"
php dca.php acic01  "$arg1" "$arg2" "$arg3" "$arg4" "$arg5"
php dca.php 0050    "$arg1" "$arg2" "$arg3" "$arg4" "$arg5"
php dca.php VTI     "$arg1" "$arg2" "$arg3" "$arg4" "$arg5"
php dca.php SPY     "$arg1" "$arg2" "$arg3" "$arg4" "$arg5"
php dca.php QQQ     "$arg1" "$arg2" "$arg3" "$arg4" "$arg5"
php dca.php TQQQ    "$arg1" "$arg2" "$arg3" "$arg4" "$arg5"

