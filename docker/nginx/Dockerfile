ARG NGINX_VERSION=1.22

FROM nginx:1.22 as base

FROM base AS development

FROM base AS deploy

COPY ./docker/nginx/templates /etc/nginx/templates/
COPY --chown=www-data:www-data ./ /var/www/html/
