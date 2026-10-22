FROM php:8.1-apache

RUN apt-get update && apt-get install -y git zip unzip && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install mysqli

RUN echo "upload_max_filesize = 50M" >> /usr/local/etc/php/conf.d/uploads.ini && \
    echo "post_max_size = 50M" >> /usr/local/etc/php/conf.d/uploads.ini

# Install Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN a2enmod rewrite

RUN a2dissite 000-default

RUN echo '<VirtualHost *:80>' > /etc/apache2/sites-available/team27.conf && \
    echo '    ServerName localhost' >> /etc/apache2/sites-available/team27.conf && \
    echo '    DocumentRoot /var/www/html' >> /etc/apache2/sites-available/team27.conf && \
    echo '    DirectoryIndex loginPage.php index.php index.html' >> /etc/apache2/sites-available/team27.conf && \
    echo '    <Directory /var/www/html>' >> /etc/apache2/sites-available/team27.conf && \
    echo '        Options Indexes FollowSymLinks' >> /etc/apache2/sites-available/team27.conf && \
    echo '        AllowOverride All' >> /etc/apache2/sites-available/team27.conf && \
    echo '        Require all granted' >> /etc/apache2/sites-available/team27.conf && \
    echo '    </Directory>' >> /etc/apache2/sites-available/team27.conf && \
    echo '</VirtualHost>' >> /etc/apache2/sites-available/team27.conf && \
    a2ensite team27

WORKDIR /var/www/html

EXPOSE 80

CMD ["apache2-foreground"]
