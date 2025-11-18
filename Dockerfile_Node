FROM gitlab.brainchurts.com:5050/k8s/baseimages/nginx-php-fpm-memcache:latest
USER root

RUN apk --no-cache add composer php-pdo php-pdo_mysql php-simplexml php83-pecl-memcached 

RUN mkdir -p /web && \
    chown -R www:www /web && \
    chmod -R 755 /web

#Handel DevOps configuration
COPY config/devops/nginx.conf /etc/nginx/nginx.conf

USER www

ARG CI_JOB_TOKEN
ENV CI_JOB_TOKEN=$CI_JOB_TOKEN

#RUN composer config -- gitlab-token.git.lan.vsmedia.net gitlab-ci-token $CI_JOB_TOKEN; \
#    awk -v pass="$CI_JOB_TOKEN" '{gsub(/CHANGEME/, pass); print}' auth.json > tmp.auth && mv tmp.auth /web/auth.json;

COPY --chown=www composer.json /web/
COPY --chown=www public/ /web/public/
COPY --chown=www php/ /web/php/
COPY --chown=www templates/ /web/templates/
COPY --chown=www app/ /web/app/
COPY --chown=www bin/ /web/bin/

#temporary fix for dotenv
COPY --chown=www .env /web/.env

#RUN composer config gitlab-domains git.lan.vsmedia.net; \
RUN composer update --working-dir=/web/

RUN mkdir -p /web/var/cache/latte
RUN chmod -R 777 /web/var

EXPOSE 8080

# Let supervisord start nginx & php-fpm
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]