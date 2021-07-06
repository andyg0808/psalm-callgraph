.PHONY: show
show: callers.dot
	dot -Tx11 $<

build: Plugin.php composer.json composer.lock
	docker build -t callgraph .

callers.csv: build
	touch $@
	docker run --rm -v$(PWD)/$@:/app/callers.csv callgraph

%.dot: %.csv
	./csv2dot $< > $@
