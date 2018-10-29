FROM php:7.1.21-apache

# Soluciona el error de permisos para DockerToolbox
RUN usermod -u 501 www-data
RUN usermod -G dialout www-data

RUN apt-get update -y && apt-get install -my gnupg2

# Soluciona error al compilar imagen en dockertoolbox: make: *** [php_zip.lo] Error 1
RUN apt-get install -y apt-file && apt-file update
RUN apt-file search -x "/pcre.h$"
RUN apt-get install -y libpcre3-dev

RUN apt-get install -y zlib1g-dev \
   && docker-php-ext-install bcmath \
   && docker-php-ext-install zip \
   && docker-php-ext-install pdo_mysql \
   && docker-php-ext-install opcache \
   && pecl install apcu-5.1.5 \
   && docker-php-ext-enable apcu

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

RUN apt-get install -y libicu-dev # Previene error ICU en libreria intl
#RUN pecl install intl
RUN docker-php-ext-install intl

RUN apt-get install -y vim
RUN apt-get install -y git

# install nodejs, npm, bower
RUN curl -sL https://deb.nodesource.com/setup_6.x | bash -
RUN apt-get install -y nodejs
RUN npm install uglify-js uglifycss less bower -g

# Para compilar e instalar extensiones nativas desde npm se necesitará también instalar las herramientas de compilación
RUN apt-get install -y build-essential

RUN php -r "readfile('https://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin --filename=composer
RUN php -r "readfile('https://phar.phpunit.de/phpunit-old.phar');" > /usr/local/bin/phpunit && chmod +x /usr/local/bin/phpunit

# Set Apache environment variables (can be changed on docker run with -e)
ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_DOCUMENTROOT /var/www

# Enable mod_rewrite feature for Apache
RUN a2enmod rewrite

# Add config for virtualhost, disable default and enable new one.
RUN a2dissite 000-default
ADD ./config/project.conf /etc/apache2/sites-available/
RUN a2ensite project

COPY ./config/crontab /etc/