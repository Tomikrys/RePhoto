Je nutné nainstalovat XAMPP
Nainstalovat Composer

mít PHP v PATH
    C:\xampp\php
mít Javu jako JAVA_HOME v proměnných
    JAVA_HOME - C:\Program Files\Java\jre1.8.0_311

composer install
php init
  // V soboru src/common/config/main-local.php změnit přístupy k DB
php yii migrate
php yii elasticsearch/init

run-elastic.bat
  // or run bin/elastic.bat in https://artifacts.elastic.co/downloads/elasticsearch/elasticsearch-6.2.4.zip
run.bat
    
je potřeba mít nainstalovaný Python 2.7, PHP 7.1, MariaDB 10.2, Elasticsearch 6.2 a případná rozšíření požadovaná při spuštění: composer install



## vygenerování souborů pro překlad
```
yii message/extract @app/config/i18n.php
```

## XDebug
download the right .dll file and place it into xampp/php/ext
https://xdebug.org/wizard
add to php.ini in xampp configuration
```ini
[xdebug]
zend_extension="C:\xampp\php\ext\php_xdebug-3.1.4-7.4-vc15-x86_64.dll"
xdebug.mode = debug
xdebug.start_with_request = yes
```
create launch.json in .vscode folder in rephoto root
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



Yii 2 Advanced Project Template
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
