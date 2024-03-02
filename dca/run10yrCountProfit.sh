#!/bin/bash

start_year=2013
end_year=2023

for year in $(seq $start_year $end_year); do
    start_date="${year}0201"
    end_date="$(($year + 1))0201"
    output_file="${year}-$(($year + 1)).txt"

    # Execute the command
    echo "./doCountProfit.sh 15 10000 $start_date $end_date $(($year + 1))0301 > $output_file"
    ./doCountProfit.sh 15 10000 $start_date $end_date $(($year + 1))0301 > $output_file
done
