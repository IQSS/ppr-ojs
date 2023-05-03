ARG OJS_VERSION

FROM pkpofficial/ojs:${OJS_VERSION}

ARG PHP_VERSION

RUN set -xe \
	&& apk add --no-cache ${PHP_VERSION}-xdebug

COPY xdebug.ini /etc/${PHP_VERSION}/conf.d/50_xdebug.ini