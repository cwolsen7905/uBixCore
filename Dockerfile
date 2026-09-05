FROM gitlab.brainchurts.com:5050/k8s/baseimages/nginx-php85-fpm-memcache:dev

USER root

RUN apk add --no-cache git && \
    mkdir -p /web && \
    chown -R www:www /web && \
    chmod -R 0755 /web

#Handel DevOps configuration
COPY config/devops/nginx.conf /etc/nginx/nginx.conf

USER www


COPY --chown=www composer.json composer.lock /web/
COPY --chown=www public/ /web/public/
COPY --chown=www php/ /web/php/
COPY --chown=www templates/ /web/templates/
COPY --chown=www app/ /web/app/
COPY --chown=www bin/ /web/bin/

# Reproducible: install exactly what composer.lock pins (no dev tools in live pods;
# Dockerfile_Test adds them). `composer update` here would let an image change
# with no commit behind it.
RUN composer install --working-dir=/web/ --no-dev --no-interaction --prefer-dist --no-progress

RUN mkdir -p /web/var/cache/latte
RUN chmod -R 777 /web/var

EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
# Rebuild trigger: pull nginx-php85-fpm-memcache:dev with the php-fpm85 supervisord fix
# (baseimages@b99503a). No-op to force a fresh ubixcore:dev image after the base fix. 2026-08-28
