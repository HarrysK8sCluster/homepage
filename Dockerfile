# nginx als Webserver
FROM nginx:alpine

# Website in nginx html dir kopieren
COPY . /usr/share/nginx/html

# optional: Default nginx config entfernen (nicht zwingend)
# RUN rm /etc/nginx/conf.d/default.conf

EXPOSE 80