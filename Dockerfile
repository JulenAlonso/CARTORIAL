FROM php:8.2-apache

# Instalar extensiones necesarias (Laravel usa pdo_mysql)
RUN docker-php-ext-install pdo_mysql

# Instalar xdebug para mejorar var_dump()
# RUN pecl install xdebug && docker-php-ext-enable xdebug

# Configurar xdebug para mostrar var_dump() con formato
# RUN echo "xdebug.mode=develop" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.var_display_max_depth=5" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.var_display_max_children=256" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini && \
#    echo "xdebug.var_display_max_data=1024" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

# Habilitar mod_rewrite
RUN a2enmod rewrite

# 🔧 Cambiar el DocumentRoot de Apache a /var/www/html/public
RUN sed -ri 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/000-default.conf

# 🔧 Configuración específica para Laravel
RUN printf "<Directory /var/www/html/public/>\n\
    Options Indexes FollowSymLinks\n\
    AllowOverride All\n\
    Require all granted\n\
</Directory>\n" \
> /etc/apache2/conf-available/laravel.conf \
    && a2enconf laravel

# Ajustar el nivel de reporte de errores de PHP
RUN echo "error_reporting = E_ALL & ~E_NOTICE" > /usr/local/etc/php/conf.d/error-level.ini && \
    echo "display_errors = On" >> /usr/local/etc/php/conf.d/error-level.ini

# Directorio de trabajo por defecto
WORKDIR /var/www/html

# Copiar entrypoint personalizado
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]
