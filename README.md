# Webová aplikace Rephoto
https://github.com/Tomikrys/Rephoto
## Postup zprovoznění
Nainstalovat XAMPP
Nainstalovat Composer

Přidat složku PHP v instalačním adresáři XAMPP do systémové proměnné prostředí PATH
    
    C:\xampp\php

Nainstalovat Javu 8
Přidat instalační adresář Javy do systémové proměnné prostředí JAVA_HOME

    JAVA_HOME - C:\Program Files\Java\jre1.8.0_311

Ve složce s projektem spustit následující příkazy:
    
    composer install
    php init
    // V soboru src/common/config/main-local.php změnit přístupy k DB
    php yii migrate
    php yii elasticsearch/init

Spustit Elastic Search:

    run-elastic.bat
    // run bin/elastic.bat in https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-6.2.4.zip

    
je potřeba mít nainstalovaný Python 2.7, PHP 7.1, MariaDB 10.2, Elasticsearch 6.2 a případná rozšíření požadovaná při spuštění: 

    composer install

pokud aplikace vrací error týkající se složky \bower, tak je nutné přejmenovat složku \vendor\bower-asset na \vendor\bower v instalačním adresáři XAMPP



## Vygenerování souborů pro překlad
```
yii message/extract @app/config/i18n.php
```

## XDebug
Stáhnout `.dll` knihovnu pomocí nástroje na stránkách https://xdebug.org/wizard a umístit ji do adresáře:

    xampp/php/ext



Přidat do `php.ini` v instalačním adresáři XAMPP konfiguraci:
```ini
[xdebug]
zend_extension="C:\xampp\php\ext\php_xdebug-3.1.4-7.4-vc15-x86_64.dll"
xdebug.mode = debug
xdebug.start_with_request = yes
```

Vytvořit `launch.json` ve složce `.vscode`, která je v kořenovém adresáři projketu, s tímto obsahem:
```json
{
    "version": "0.2.0",
    "configurations": [
        {
            "name": "Listen for XDebug",
            "type": "php",
            "request": "launch",
            "port": 9003
        }
    ]
}
```


## Struktura projektu
Pro přehlednost jsou vypsány pouze důležité soubory a složky. Nejdůležitější položky jsou popsány tučně.

```
    RephotoWeb\
    ├─ #elasticsearch-6.2.4\                        - stažený elastic search engine pro snadné spuštění
    ├─ .git\                                        - složka s historií vývoje webové aplikace
    ├─ api\                                         - API ROZHRANÍ APLIKACE
    │  ├─ config\
    │  │  └─ main.php
    │  │ 
    │  └─ modules\
    │      ├─ v1\
    │      │  └─ controllers\
    │      │  │  ├─ PlacesController.php
    │      │  │  ├─ TestController.php
    │      │  │  └─ UserController.php
    │      │  │
    │      │  └─ docs\
    │      │
    │      └─ v2\                                   - SKRZE TUTO VERZI KOMUNIKUJE MOBILNÍ APLIKACE
    │          └─ controllers\
    │          │  ├─ PlacesController.php
    │          │  ├─ TestController.php
    │          │  └─ UserController.php
    │          │
    │          └─ docs\
    │  
    ├─ backend\                                     - ADMIN ROZHRANÍ APLIKACE
    │  ├─ config\
    │  │  └─ main.php
    │  │ 
    │  ├─ controllers\
    │  │  ├─ BackendController.php
    │  │  ├─ PhotoController.php
    │  │  ├─ PlaceController.php
    │  │  ├─ SiteController.php
    │  │  └─ UserConstroller.php
    │  │  
    │  ├─ messages\                                 - překlad admin rozhraní
    │  ├─ models\
    │  │  ├─ search\
    │  │  │  ├─ PhotoSearch.php
    │  │  │  ├─ PlaceSearch.php
    │  │  │  └─ UserSearch.php
    │  │  └─ LoginForm.php
    │  │
    │  ├─ views\                                    - PHP SOUBORY S POHLEDY PSANÝMI V HTML
    │  │  ├─ layouts\
    │  │  ├─ photo\
    │  │  ├─ place\
    │  │  ├─ site\
    │  │  └─ user\
    │  │    
    │  └─ web\
    │      ├─ assets\
    │      └─ source-assets\
    │        ├─ css\
    │        ├─ img\
    │        └─ js\                                 - SKRIPTY JAVASCRIPTU PRO JEDNOTLIVÉ STRÁNKY V BACKENDU
    │           ├─ dropzone.js
    │           ├─ editor.js
    │           ├─ main.js
    │           ├─ map.js
    │           └─ photo-aligner.js
    │  
    ├─ common\                                      - SPOLEČNÉ SOUBORY PRO VŠECHNY ODDĚLENÉ ČÁSTI APLIKACE
    │  ├─ config\
    │  │  ├─ main.php                               - společná nastavení
    │  │  └─ main-local.php                         - ZMĚNA PŘÍSTUPOVÝCH ÚDAJŮ K DB                 
    │  │ 
    │  ├─ mail\
    │  │  ├─ passwordResetToken-html.php
    │  │  └─ passwordResetToken-text.php         
    │  │ 
    │  ├─ models\
    │  │  ├─ File.php
    │  │  ├─ LoginForm.php
    │  │  ├─ Photo.php
    │  │  ├─ PhotoEdited.php
    │  │  ├─ PhotoWishList.php
    │  │  ├─ Place.php
    │  │  ├─ PlaceSaved.php
    │  │  ├─ Rephoto.php
    │  │  ├─ SingupForm.php
    │  │  └─ User.php
    │  
    ├─ frontend\                                    - FRONTEND APLIKACE
    │  ├─ assets\                                   - mapování js, css a dlaších závislostí
    │  ├─ config\
    │  │  └─ main.php
    │  │ 
    │  ├─ controllers\
    │  │  ├─ EditorController.php
    │  │  ├─ MapController.php
    │  │  ├─ PhotoController.php
    │  │  ├─ PlaceController.php
    │  │  ├─ SiteController.php
    │  │  ├─ SystemController.php
    │  │  ├─ TakephotoController.php
    │  │  └─ UserController.php
    │  │ 
    │  ├─ messages\                                  - překlady frontendu 
    │  ├─ models\
    │  │  ├─ elasticsearch\
    │  │  │  └─ Place.php
    │  │  │ 
    │  │  ├─ search\
    │  │  │  ├─ PhotoSearch.php
    │  │  │  └─ PlaceSearch.php
    │  │  │ 
    │  │  ├─ ContactForm.php
    │  │  ├─ PasswordResetRequestForm.php
    │  │  ├─ PlaceFilter.php
    │  │  ├─ ResetPasswordForm.php
    │  │  ├─ SingupForm.php
    │  │  └─ UploadForm.php
    │  │ 
    │  ├─ views\                                    - PHP SOUBORY S POHLEDY PSANÝMI V HTML
    │  └─ web\
    │     └─ source-assets\
    │        ├─ css\
    │        ├─ img\
    │        └─ js\                                 - SKRIPTY JAVASCRIPTU PRO JEDNOTLIVÉ STRÁNKY VE FRONTENDU
    │           ├─ dropzone.js
    │           ├─ editor.js
    │           ├─ main.js
    │           ├─ map.js
    │           └─ photo-aligner.js
    │        
    │  
    ├─ python27\                                    - lokální python pro snadný přesun na jiný systém
    ├─ uploads\                                     - SLOŽKA S NAHRANÝMI FOROGRAFIEMI
    ├─ homography-points.py                         - skripty pro deformaci refotografie pomocí homografie
    ├─ homography-transformation.py
    ├─ homography-transformation-points.py
    ├─ README.md
    ├─ run - backend.bat                            - spuštění backendu přes yii server (nepoužívat, používat XAMPP)
    ├─ run.bat                                      - spuštění frontendu přes yii server (nepoužívat, používat XAMPP)
    └─ run-elastic.bat                              - ZÁSTUPCE PRO SPUŠTĚNÍ ELASTIC SEARCH ENGINU
```


Oficiální popis Yii 2 Advanced Project Template
===============================

Yii 2 Advanced Project Template is a skeleton [Yii 2](http://www.yiiframework.com/) application best for
developing complex Web applications with multiple tiers.

The template includes three tiers: front end, back end, and console, each of which
is a separate Yii application.

The template is designed to work in a team development environment. It supports
deploying the application in different environments.

Documentation is at [docs/guide/README.md](docs/guide/README.md).

[![Latest Stable Version](https://poser.pugx.org/yiisoft/yii2-app-advanced/v/stable.png)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Total Downloads](https://poser.pugx.org/yiisoft/yii2-app-advanced/downloads.png)](https://packagist.org/packages/yiisoft/yii2-app-advanced)
[![Build Status](https://travis-ci.org/yiisoft/yii2-app-advanced.svg?branch=master)](https://travis-ci.org/yiisoft/yii2-app-advanced)

DIRECTORY STRUCTURE
-------------------

```
common
    config/              contains shared configurations
    mail/                contains view files for e-mails
    models/              contains model classes used in both backend and frontend
    tests/               contains tests for common classes    
console
    config/              contains console configurations
    controllers/         contains console controllers (commands)
    migrations/          contains database migrations
    models/              contains console-specific model classes
    runtime/             contains files generated during runtime
backend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains backend configurations
    controllers/         contains Web controller classes
    models/              contains backend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for backend application    
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
frontend
    assets/              contains application assets such as JavaScript and CSS
    config/              contains frontend configurations
    controllers/         contains Web controller classes
    models/              contains frontend-specific model classes
    runtime/             contains files generated during runtime
    tests/               contains tests for frontend application
    views/               contains view files for the Web application
    web/                 contains the entry script and Web resources
    widgets/             contains frontend widgets
vendor/                  contains dependent 3rd-party packages
environments/            contains environment-based overrides
```
