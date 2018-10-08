FROM docker.klink.asia/images/video-processing-cli:0.4.1

FROM php:7.1-fpm

## Default environment variables
ENV INSTALL_DIRECTORY /var/www/vss
ENV PHP_MAX_EXECUTION_TIME 240
ENV PHP_MAX_INPUT_TIME 120
ENV PHP_MEMORY_LIMIT 288M

## Install libraries, envsubst, tini, supervisor and php modules
RUN apt-get update -yqq && \
    apt-get install -yqq \ 
        locales \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libmcrypt-dev \
        libpng-dev \
        libbz2-dev \
        gettext \
        supervisor \
        cron \
    && docker-php-ext-install -j$(nproc) iconv mcrypt \
    && docker-php-ext-configure gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ \
    && docker-php-ext-install -j$(nproc) gd \
    && docker-php-ext-install bz2 zip exif pdo_mysql \
    && apt-get clean \
    && rm -r /var/lib/apt/lists/*

## Forces the locale to UTF-8
RUN locale-gen "en_US.UTF-8" \
    && DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
 	&& locale-gen "C.UTF-8" \
 	&& DEBIAN_FRONTEND=noninteractive dpkg-reconfigure locales \
 	&& /usr/sbin/update-locale LANG="C.UTF-8"

## NGINX installation
### The installation procedure is heavily inspired from https://github.com/nginxinc/docker-nginx
RUN set -e; \
	NGINX_GPGKEY=573BFD6B3D8FBC641079A6ABABF5BD827BD9BF62; \
	NGINX_VERSION=1.14.0-1~stretch; \
	found=''; \
	apt-get update; \
	apt-get install -y gnupg; \
	for server in \
		ha.pool.sks-keyservers.net \
		hkp://keyserver.ubuntu.com:80 \
		hkp://p80.pool.sks-keyservers.net:80 \
		pgp.mit.edu \
	; do \
		echo "Fetching GPG key $NGINX_GPGKEY from $server"; \
		apt-key adv --keyserver "$server" --keyserver-options timeout=10 --recv-keys "$NGINX_GPGKEY" && found=yes && break; \
	done; \
	test -z "$found" && echo >&2 "error: failed to fetch GPG key $NGINX_GPGKEY" && exit 1; \
    echo "deb http://nginx.org/packages/debian/ stretch nginx" >> /etc/apt/sources.list \
	&& apt-get update \
	&& apt-get install --no-install-recommends --no-install-suggests -y \
						ca-certificates \
						nginx=${NGINX_VERSION} \
						# nginx-module-njs=${NJS_VERSION} \
						# gettext-base \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

## Configure cron to run Laravel scheduler
RUN echo '* * * * * php /var/www/dms/artisan schedule:run >> /dev/null 2>&1' | crontab -

## Copy NGINX default configuration
COPY docker/nginx-default.conf /etc/nginx/conf.d/default.conf

## Copy additional PHP configuration files
COPY docker/php/php-*.ini /usr/local/etc/php/conf.d/

## Override the php-fpm additional configuration added by the base php-fpm image
COPY docker/php/zz-docker.conf /usr/local/etc/php-fpm.d/

## Copy supervisor configuration
COPY docker/supervisor/supervisor.conf /etc/supervisor/conf.d/

## Copying custom startup scripts
COPY docker/configure.sh /usr/local/bin/configure.sh
COPY docker/start.sh /usr/local/bin/start.sh

RUN chmod +x /usr/local/bin/configure.sh && \
    chmod +x /usr/local/bin/start.sh

## Copy the application code
COPY . $INSTALL_DIRECTORY 

COPY --from=0 /video-processing-cli/ "${INSTALL_DIRECTORY}/bin/"

ENV STORAGE_PATH "${INSTALL_DIRECTORY}/storage"

WORKDIR $INSTALL_DIRECTORY

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/start.sh"]

