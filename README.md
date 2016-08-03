# Flight2wwu framework

## Installation
#### Use composer to install 
 ```
 composer require wwtg99/flight2wwu
 ```
 Or, add require in composer.json `"wwtg99/flight2wwu": "*"`

#### Use command tool wwtinit (vendor/bin) to initialize project
```
vendor/bin/wwtinit <project_dir>
```

#### Config Apache
Set web directory to web and set AllowOverride to All.

#### Config framework
Change conf files in App/config. At most time only change app_config.php.

## Directories
 - App: application directory
    - config: config files
      - lang: translation files
    - Controller: controller class
    - Model: model class
    - view: view templates
    - view_twig: twig view templates
    - Plugin: plugin class
 - bootstrap: bootstrap scripts
 - storage: storage directory, write access
    - log: default log directory
    - tmp: default tmp directory (ex. config cache file)
 - web: web document root
