# default build target
all::

all:: dev
.PHONY: dev down up release docker new_version

OJS_VERSION := 3_3_0-14
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
	sed 's/\(<release>\).*\(<\/release>\)/\1$(RELEASE_VERSION)\2/' ppr-ojs-plugin/version.xml | sed 's/\(<date>\).*\(<\/date>\)/\1$(RELEASE_DATE)\2/' > ppr-ojs-plugin/version.xml.new
	mv ppr-ojs-plugin/version.xml.new ppr-ojs-plugin/version.xml
	tar -czvf releases/ppr-ojs-plugin-$(RELEASE_VERSION).tar.gz ppr-ojs-plugin

new_version:
	./create_version.sh

docker:
	docker build --build-arg OJS_VERSION=$(OJS_VERSION) -t $(PPR_OJS_IMAGE) -f environment/Dockerfile ./environment
	sed -i 's/{OJS_RELEASE_VERSION}/$(NEW_VERSION)/g' ppr-ojs-plugin/version.xml
	sed -i 's/{OJS_RELEASE_DATE}/$(NEW_VERSION)/g' ppr-ojs-plugin/version.xml
