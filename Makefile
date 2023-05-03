# default build target
all::

all:: dev
.PHONY: dev down up release docker new_version clean

OJS_VERSION ?= 3_3_0-14
PHP_VERSION ?= php7
PPR_OJS_IMAGE := ppr_ojs:$(OJS_VERSION)
DETACHED_MODE := $(if $(DETACHED),-d,)

PWD := $(shell pwd)

ENV := env PPR_OJS_IMAGE=$(PPR_OJS_IMAGE)

dev: down up

down:
	cd environment && $(ENV) docker-compose down -v || :

up:
	cd environment && $(ENV) docker-compose up --build $(DETACHED_MODE)

release: new_version
	$(eval RELEASE_VERSION = $(shell cat VERSION))
	$(eval RELEASE_DATE = $(shell date +%Y-%m-%d))
	mkdir -p releases
	sed 's/\(<release>\).*\(<\/release>\)/\1$(RELEASE_VERSION)\2/' pprOjsPlugin/version.xml | sed 's/\(<date>\).*\(<\/date>\)/\1$(RELEASE_DATE)\2/' > pprOjsPlugin/version.xml.new
	mv pprOjsPlugin/version.xml.new pprOjsPlugin/version.xml
	tar -czvf releases/ppr-ojs-plugin-$(RELEASE_VERSION).tar.gz pprOjsPlugin

new_version:
	./create_version.sh

clean:
	rm -rf environment/data/db
	rm -rf environment/data/ojs

docker:
	docker build --build-arg OJS_VERSION=$(OJS_VERSION) --build-arg PHP_VERSION=$(PHP_VERSION) -t $(PPR_OJS_IMAGE) -f environment/Dockerfile ./environment

