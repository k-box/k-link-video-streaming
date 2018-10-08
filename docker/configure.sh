#!/bin/bash
set -e

APP_ENV=${APP_ENV:-production}
APP_DATABASE_FILE="${INSTALL_DIRECTORY}/storage/app/database.sqlite"
SETUP_WWWUSER=${SETUP_WWWUSER:-www-data}
TRUSTED_PROXY_IP=${TRUSTED_PROXY_IP:-192.0.2.1/32}

# TODO: write in storage/app the APP_KEY and grab it

function startup_config () {
    echo "Configuring K-Link Streaming service..."
    echo "- Writing php runtime configuration..."

    # Set post and upload size for php if customized for the specific deploy
    cat > /usr/local/etc/php/conf.d/php-runtime.ini <<-EOM &&
        memory_limit=${PHP_MEMORY_LIMIT}
        max_input_time=${PHP_MAX_INPUT_TIME}
        max_execution_time=${PHP_MAX_EXECUTION_TIME}
	EOM

    write_config &&
    init_empty_dir $INSTALL_DIRECTORY/storage && 
    echo "- Changing folder groups and permissions" &&
    chgrp -R $SETUP_WWWUSER $INSTALL_DIRECTORY/storage &&
    chgrp -R $SETUP_WWWUSER $INSTALL_DIRECTORY/bootstrap/cache &&
    chgrp -R $SETUP_WWWUSER $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/bin/ &&
    chmod +x $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/bin/tusd-linux &&
    chmod -R g+rw $INSTALL_DIRECTORY/bootstrap/cache &&
    chmod -R g+rw $INSTALL_DIRECTORY/storage &&
    normalize_line_endings &&
    install_or_update &&
    chgrp -R $SETUP_WWWUSER $INSTALL_DIRECTORY/storage/logs &&
    chgrp -R $SETUP_WWWUSER $INSTALL_DIRECTORY/bootstrap/cache &&
    chmod -R g+rw $INSTALL_DIRECTORY/bootstrap/cache &&
    chmod -R g+rw $INSTALL_DIRECTORY/storage/logs &&
	echo "configuration completed."
}

function write_config() {
    echo "- Writing env file..."

    if [[ -z "$APP_URL" ]]; then
        echo "ERROR. Required APP_URL environment variable is missing. Aborting the startup."
        exit 1
    fi

    if [[ -z "$KLINK_REGISTRY_URL" && "$APP_ENV" != 'local' ]]; then
        echo "ERROR. Required KLINK_REGISTRY_URL environment variable is missing. Aborting the startup."
        exit 1
    fi

	cat > ${INSTALL_DIRECTORY}/.env <<-EOM &&
		APP_ENV=${APP_ENV}
		APP_DEBUG=false
		APP_KEY=${APP_KEY}
		APP_URL=${APP_URL}
        DB_DATABASE=${APP_DATABASE_FILE}
		TRUSTED_PROXY_IP=${TRUSTED_PROXY_IP}
        TUSUPLOAD_USE_PROXY=true
        TUSUPLOAD_HOST=0.0.0.0
		KLINK_REGISTRY_URL=${KLINK_REGISTRY_URL}
	EOM

	echo "- ENV file written! $INSTALL_DIRECTORY/.env"
}

function install_or_update() {
    cd ${INSTALL_DIRECTORY} && echo "- Configuring database..."

    if [ ! -f "${APP_DATABASE_FILE}" ]; then
        touch "${APP_DATABASE_FILE}"
    fi

    php artisan videodeploy:link
    php artisan migrate --force

    chgrp -R $SETUP_WWWUSER $APP_DATABASE_FILE && \
    chmod -R g+rw $APP_DATABASE_FILE
}


function normalize_line_endings() {

    cp $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/pre-create $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/pre-create-original \
    && cp $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-receive $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-receive-original \
    && cp $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-finish $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-finish-original \
    && cp $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-terminate $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-terminate-original \

    tr -d '\r' < $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/pre-create-original > $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/pre-create
    tr -d '\r' < $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-receive-original > $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-receive
    tr -d '\r' < $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-finish-original > $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-finish
    tr -d '\r' < $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-terminate-original > $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/post-terminate

    chgrp -R $SETUP_WWWUSER $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/ \
    && chmod -R +x $INSTALL_DIRECTORY/vendor/oneofftech/laravel-tus-upload/hooks/linux/
}

function init_empty_dir() {
    local dir_to_init=$1

    echo "- Checking storage directory structure..."

    if [ ! -d "${dir_to_init}/app/public" ]; then
        mkdir -p "${dir_to_init}/app/public"
        echo "-- [app/public] created."
    fi
    if [ ! -d "${dir_to_init}/app/uploads" ]; then
        mkdir -p "${dir_to_init}/app/uploads"
        echo "-- [app/uploads] created."
    fi
    if [ ! -d "${dir_to_init}/app/videos" ]; then
        mkdir -p "${dir_to_init}/app/videos"
        echo "-- [app/videos] created."
    fi
    if [ ! -d "${dir_to_init}/framework/cache" ]; then
        mkdir -p "${dir_to_init}/framework/cache"
        echo "-- [framework/cache] created."
    fi
    if [ ! -d "${dir_to_init}/framework/sessions" ]; then
        mkdir -p "${dir_to_init}/framework/sessions"
        echo "-- [framework/sessions] created."
    fi
    if [ ! -d "${dir_to_init}/framework/testing" ]; then
        mkdir -p "${dir_to_init}/framework/testing"
        echo "-- [framework/testing] created."
    fi
    if [ ! -d "${dir_to_init}/framework/views" ]; then
        mkdir -p "${dir_to_init}/framework/views"
        echo "-- [framework/views] created."
    fi
    if [ ! -d "${dir_to_init}/logs" ]; then
        mkdir -p "${dir_to_init}/logs"
        echo "-- [logs] created."
    fi

    php artisan videostorage:link
}

startup_config >&2
