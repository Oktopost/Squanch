#!/bin/bash

INSTANCES=(dummy squid migration);

for i in ${INSTANCES[@]}; do
	export instance=$i;
	echo ;
	echo "==========================================";
	echo "Testing with $i implementer";
	echo "==========================================";
	./vendor/bin/phpunit tests
done
