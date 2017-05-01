#!/bin/bash

./vendor/bin/phpunit --testsuite=Squanch_Unit_Tests

INSTANCES=(squid predis inmemory fallback fallback-advanced lrucache);

for i in ${INSTANCES[@]}; do
	export CACHE_PLUGIN_TYPE=$i;
	echo ;
	echo "==========================================";
	echo "Testing with $i implementer";
	echo "==========================================";
	./vendor/bin/phpunit --testsuite=Squanch_Sanity_Tests
done