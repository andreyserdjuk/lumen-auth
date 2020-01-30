FROM php:7.4-cli-alpine

RUN apk --update add --virtual build-dependencies build-base openssl-dev autoconf \
  && pecl install mongodb \
  && docker-php-ext-enable mongodb \
  && apk del build-dependencies build-base openssl-dev autoconf \
  && rm -rf /var/cache/apk/*

# Time Zone
RUN echo "date.timezone=${PHP_TIMEZONE:-UTC}" > $PHP_INI_DIR/conf.d/date_timezone.ini

RUN apk update \
  && apk upgrade \
  && apk add bash \
  && curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin --filename=composer
