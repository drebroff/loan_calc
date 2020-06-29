FROM ubuntu:focal

MAINTAINER Phpistai

ARG DEBIAN_FRONTEND=noninteractive

RUN apt-get -y update && \
    apt-get -y install apache2 unzip php php-mbstring php-dom php-gd php-sqlite3 && \
    a2enmod rewrite && \
    sed -i 's,DocumentRoot /var/www/html,DocumentRoot /var/www/html/web,g' /etc/apache2/sites-available/000-default.conf && \
    printf "<Directory /var/www/html>\n\tAllowOverride All\n</Directory>" >> /etc/apache2/sites-available/000-default.conf && \
    php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" && \
    php composer-setup.php --install-dir=/usr/local/bin --filename=composer

WORKDIR /var/www/html

RUN rm -rf ./* && \
    composer --no-interaction create-project drupal/recommended-project . && \
    composer --no-interaction require drush/drush && \
    vendor/bin/drush site:install -y standard \
        --account-name=admin --account-pass=admin --site-name="Mortgage Loan Calculator" \
        --db-url=sqlite://sites/default/files/.ht.sqlite && \
    printf "\$settings['trusted_host_patterns'] = ['^localhost$'];\n" >> web/sites/default/settings.php && \
    chown -R www-data:www-data web/sites/default

COPY . /var/www/html/web/modules/custom/loan_calc

RUN vendor/bin/drush en -y loan_calc

CMD apachectl -D FOREGROUND

EXPOSE 80
