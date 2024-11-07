#!/bin/bash

start_year=2005
end_year=2024

# Monthly invest in year (N) and (N+1), buy back in (N+2) July 1st

for year in $(seq $start_year $end_year); do
    start_date="${year}0101"
    end_date="$(($year + 0))1230"
    output_file="${year}-$(($year + 0)).txt"

    # Execute the command
    echo "./doCountProfit.sh 10 10000 $start_date $end_date $(($year + 2))0701 > $output_file"
    ./doCountProfit.sh 10 10000 $start_date $end_date $(($year + 2))0701 > $output_file
done
