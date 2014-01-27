SPDB
====

Projekt z SPDB, semestr 13Z, EiTI. Temat 2 - Wizualizacja danych udostępnionych w konkursie Predict which 311 issues are most important to citizens. Prowadzący dr inż. Grzegorz Protaziuk

Wymagania serwera aplikacji:
- Apache 2
- mod_rewrite (rewrite engine dla Apache2)
- PHP 5.4.*
- PHP5-OCI8 (moduł do połączeń z bazą danych Oracle)
- Composer (https://getcomposer.org - menedżer zależności bibliotek dla PHP)

Wymagania serwera baz danych:
- Oracle Database 11g (lub wyższe),
- Oracle Spatial (np. w wersji Enterprise)


Instalacja aplikacji:
- Należy pobrać i rozpakować źródła do katalogu np. /var/www/spdb (`$HOME`)
- W katalogu `$HOME` należy uruchomić komendę `composer install` która zainstaluje potrzebne biblioteki
- Utworzyć Virtual Hosta w Apache2 skierowanego do folderu `$HOME/public`
- Utworzyć indywidualny klucz Google Maps API v3 (https://code.google.com/apis/console)
- Podmienić wygenerowany klucz w pliku `$HOME/module/Application/view/application/index/index.twig` zamiast ciągu znaków `###API_KEY###`
- Uzupełnić dane do połączenia z bazą danych w pliku konfiguracyjnym: `$HOME/config/autoload/global.php`

Instalacja bazy danych:
- Rozpakować i zaimportować spakowane (*.tar.gz) pliki SQL z `$HOME/data/oracle`
