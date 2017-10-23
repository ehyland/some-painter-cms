FROM registry.gitlab.com/ehyland/dockerfiles/silverstripe:nginx-1.12.1-php-7.1.9-alpine

RUN apk add --no-cache bash

COPY . /site

WORKDIR /site

RUN composer install

CMD ["/site/scripts/docker-entry.sh"]
