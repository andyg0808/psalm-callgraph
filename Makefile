.PHONY: show
show: demo_callers.dot
	test -s $<
	dot -Tx11 $<

.PHONY: build
build: Plugin.php composer.json composer.lock
	docker build -t callgraph .

demo_callers.csv: build
	touch $@
	docker run --rm -v$(PWD)/phpunit:/mnt -v$(PWD)/$@:/mnt/callers.csv callgraph

%.dot: %.csv
	./csv2dot $< > $@
