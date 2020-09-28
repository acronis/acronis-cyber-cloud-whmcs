#
# @Copyright © 2003-2019 Acronis International GmbH. This source code is distributed under MIT software license.
#

FROM centos:7

ARG PHP_VERSION=72

VOLUME /mnt/target
WORKDIR /mnt/target

# TOOLS
RUN yum -y install zip epel-release

# PHP
RUN yum -y install http://rpms.remirepo.net/enterprise/remi-release-7.rpm
RUN yum-config-manager --enable remi-php${PHP_VERSION}
RUN yum -y install \
  php \
  php-pdo \
  php-pdo_mysql \
  php-gd \
  php-imap \
  php-intl \
  php-mbstring \
  php-soap \
  php-xml \
  php-xmlrpc \
  php-pecl-zip \
  php-pecl-apc \
  php-pecl-apcu

RUN sed -i 's/;date.timezone =/date.timezone = UTC/' /etc/php.ini

# COMPOSER
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

RUN yum clean all
