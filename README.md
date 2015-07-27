CurrenciesCharts
================

INSTALL:

Instalując system w wersji produkcyjnej należy ustawić zmienną środowiska na prod następującą komendą:
export SYMFONY_ENV=prod

Następnie po pobraniu repozytorium należy zainstalować zależności za pomocą composer'a:
composer install --no-dev --optimize-autoloader

Kolejnie należy wywołać następujące komendy w celu zainstalowania assetów:
php app/console fos:js-routing:dump
php app/console assets:install --symlink
php app/console assetic:dump --env=prod --no-debug


Do wyczyszczenia cache'a należy użyć komendy:
php app/console cache:clear --env=prod --no-debug

Z powodu błedu w działaniu tej komendy następnie należy przywrócić uprawnienia na katalogu app/cache/prod:
chmod -R 0777 app/cache/prod/
oraz logów:
chmod -R 0777 logs


Bazę danych tworzy się poleceniem:
php app/console doctrine:database:create


Więcej na stronie: How to Deploy a Symfony Application
