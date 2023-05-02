ARG OJS_VERSION

FROM pkpofficial/ojs:${OJS_VERSION}

RUN set -xe \
	&& apk add --no-cache php7-xdebug

COPY xdebug.ini /etc/php7/conf.d/50_xdebug.ini