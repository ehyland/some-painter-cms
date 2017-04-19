FROM alpine:3.5

RUN apk update && \
    apk upgrade && \
    apk add \
        php5 \
        php5-gd \
        php5-openssl \
        php5-curl \
        php5-pdo \
        php5-pdo_mysql \
        php5-mcrypt \
        php5-common \
        php5-dom \
        php5-json \
        php5-zlib \
        php5-iconv \
        php5-ctype \
        php5-phar \
        php5-xml \
        php5-opcache \
        php5-apache2 \
        apache2 \
        libcap

# allow binding to 80
RUN setcap cap_net_bind_service=+ep /usr/sbin/httpd

# install composer
RUN php5 -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php5 -r "if (hash_file('SHA384', 'composer-setup.php') === '669656bab3166a7aff8a7506b8cb2d1c292f042046c5a994c43155c0be6190fa0355160742ab2e1c88d40d5be660b410') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" && \
    php5 composer-setup.php --install-dir=/usr/local/bin --filename=composer && \
    php5 -r "unlink('composer-setup.php');"

# create apache pid dir
RUN mkdir -p /run/apache2

# copy config and app
COPY docker/etc/apache2/ /etc/apache2/
COPY docker/etc/php5/ /etc/php5/
COPY . /var/www/cms

# fix permissions
RUN chown -R -L apache:apache /var/www

WORKDIR /var/www/cms
USER apache

# build
RUN composer install

VOLUME /var/www/cms/assets
EXPOSE 80

CMD ["/usr/sbin/httpd", "-D", "FOREGROUND"]
