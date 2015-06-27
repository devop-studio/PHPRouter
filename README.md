# Router
**PHP Router** is totally inspired by [Gatakka/PGF-Router](https://github.com/gatakka/PGF-Router/) and does not use regular expressions too.

| SensioLabs Insight | Travis CI | Scrutinizer CI|
| ------------------------|-------------|-----------------|
|[![SensioLabsInsight](https://insight.sensiolabs.com/projects/3455db82-ebd0-4767-852b-15e96b3aca74/mini.png)](https://insight.sensiolabs.com/projects/3455db82-ebd0-4767-852b-15e96b3aca74)|[![Build Status](https://travis-ci.org/desertknight/symfony2-extensions.svg?branch=master)](https://travis-ci.org/desertknight/symfony2-extensions)|[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/desertknight/PHPRouter/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/desertknight/PHPRouter/?branch=master) [![Code Coverage](https://scrutinizer-ci.com/g/desertknight/PHPRouter/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/desertknight/PHPRouter/?branch=master) [![Build Status](https://scrutinizer-ci.com/g/desertknight/PHPRouter/badges/build.png?b=master)](https://scrutinizer-ci.com/g/desertknight/PHPRouter/build-status/master)

[![Dependency Status](https://www.versioneye.com/user/projects/5562f6e3366466001fb30000/badge.svg?style=flat)](https://www.versioneye.com/user/projects/5562f6e3366466001fb30000) [![Latest Stable Version](https://poser.pugx.org/millennium/router/v/stable)](https://packagist.org/packages/millennium/router) [![Total Downloads](https://poser.pugx.org/millennium/router/downloads)](https://packagist.org/packages/millennium/router) [![Latest Unstable Version](https://poser.pugx.org/millennium/router/v/unstable)](https://packagist.org/packages/millennium/router) [![License](https://poser.pugx.org/millennium/router/license)](https://packagist.org/packages/millennium/router)


## Requirements:
-    "symfony/yaml": "*"

## Suggest
-    "php": ">=5.4.0"
-    "ext-mbstring": "*"

## Features:

- *Integrate Memcache or other cache systems*
- *Maybe some refactoring and optimizing*

## Installation and configuration:

Install with [Composer](http://packagist.org), run:

```sh
composer require millennium/router
```

<a name="routers"></a>

### Routers example

This is sample router configuration

```yaml
path: /                         # require
action:                         # Some action, we use Namespace:Controller:action
methods: []                     # any valid HTTP methods combination (GET, POST, PUT, DELETE) **ONLY UPPER STRING**
requires: []                    # requires url parameters
defaults: []                    # defaults parameters
security: []                    # this is future, for now is only validation ip request (eg. allow only from ips array)
import: path                    # you can import other yaml routing files, path, security and methods will be overriding
```

File look like this

```yaml
homepage:
    path: /
    action: Namespace:Controller:action
user_action:
    path: /user/:id/:action
    action: Namespace:Controller:action
    methods:
        - GET
    requires:
        id: digit
        action: [add, view, edit, delete]
admin_user:
    path: /admin
    import: ./config/routes_user_admin.yml
    security:
        - { ip: [127.0.0.1, ::1] }
```

## Usage examples:

```php
<?php

include_once '../vendor/autoload.php';

use Millennium\Router;
use Millennium\RouterCollection;

$routerCollection = new RouterCollection();
$collections = $routerCollection->collectRouters("router.yml");

$router = new Router();

try {
    $route = $router->findRoute('/', $collections);
} catch (\Exception $e) {
    trigger_error($e->getMessage(), E_USER_ERROR);
}
```


### Server Configuration

#### Apache

You may need to add the following snippet in your Apache HTTP server virtual host configuration or **.htaccess** file.

```apacheconf
RewriteEngine on
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond $1 !^(index\.php)
RewriteRule ^(.*)$ /index.php/$1 [L]
```

Alternatively, if you’re lucky enough to be using a version of Apache greater than 2.2.15, then you can instead just use this one, single line:
```apacheconf
FallbackResource /index.php
```

#### IIS

For IIS you will need to install URL Rewrite for IIS and then add the following rule to your `web.config`:
```xml
<?xml version="1.0" encoding="UTF-8"?>
<configuration>
    <system.webServer>
        <rewrite>
          <rule name="Toro" stopProcessing="true">
            <match url="^(.*)$" ignoreCase="false" />
              <conditions logicalGrouping="MatchAll">
                <add input="{REQUEST_FILENAME}" matchType="IsFile" ignoreCase="false" negate="true" />
                <add input="{REQUEST_FILENAME}" matchType="IsDirectory" ignoreCase="false" negate="true" />
                <add input="{R:1}" pattern="^(index\.php)" ignoreCase="false" negate="true" />
              </conditions>
            <action type="Rewrite" url="/index.php/{R:1}" />
          </rule>
        </rewrite>
    </system.webServer>
</configuration>
```

#### Nginx

Under the `server` block of your virtual host configuration, you only need to add three lines.
```conf
location / {
  try_files $uri $uri/ /index.php?$args;
}
```

## Contributions

Contributions to PHPRouter are welcome via pull requests.


## License

PHPRouter was created by [Zlatko Hristov](http://z-latko.info) and released under the MIT License.
