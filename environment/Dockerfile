ARG OJS_VERSION

FROM pkpofficial/ojs:${OJS_VERSION}

ARG PHP_VERSION

RUN set -xe \
	&& apk add --no-cache ${PHP_VERSION}-xdebug \
	&& apk add --no-cache rsync \
	&& apk add --no-cache tar

COPY xdebug.ini /etc/${PHP_VERSION}/conf.d/50_xdebug.ini

COPY ojs-entry-point /usr/local/bin/ojs-entry-point
COPY ojs-run-scheduled /usr/local/bin/ojs-run-scheduled
RUN chmod +x /usr/local/bin/ojs-entry-point

CMD ["ojs-entry-point"]
