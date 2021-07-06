FROM composer as build
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install

FROM php as run
WORKDIR /app
COPY --from=build /app/vendor ./vendor
COPY . .
WORKDIR /mnt
ENTRYPOINT ["/app/vendor/bin/psalm", "--plugin=/app/Plugin.php"]
