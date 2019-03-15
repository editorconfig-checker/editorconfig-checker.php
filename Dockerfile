FROM composer:1.8

RUN chmod -R 777 /tmp/cache

COPY . /usr/src/myapp
WORKDIR /usr/src/myapp
