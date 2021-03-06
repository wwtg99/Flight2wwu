# ChangeLog

## 0.3.13
    - Disable start session by default
    - Change CSRF and captcha to use session instead of cache
    - Update medoo to 1.2
    - Update pgauth and data_pool
    - Change forbidden and method not allowed to flight map
    - Change default auth post to api

## 0.3.12
    - Fix cookie bugs

## 0.3.11
    - Fix logout bugs when not login

## 0.3.10
    - Update internal controllers to non-static
    - Add DefaultFilter

## 0.3.9
    - Add internal default controllers

## 0.3.8
    - Update pg_auth

## 0.3.7
    - Fix OAuth bugs
    - Remove unused files

## 0.3.6
    - Update and simplify auth logic

## 0.3.5
    - Fix paging bugs

## 0.3.4
    - Add response type raw, string
    - Add request input for put, patch and delete

## 0.3.3
    - Add put, patch, delete, options, head access in auth

## 0.3.2
    - Add default headers in api and view response

## 0.3.1
    - Add getBody() in Request
    - Update update method in RestfulAPIController

## 0.3.0
    - Support restful API
    - Unify routes
    - Add resquest and response
    - Remove unnessary files

## 0.2.10
    - Add login method

## 0.2.9
    - Update pgauth

## 0.2.8
    - Update flight json
    - Fix bugs in roles
    - Update AuthController
    - Add forget password

## 0.2.7
    - Fix wwtinit bugs

## 0.2.6
    - Add refreshSession in RoleAuth
    - Add user edit
    - Add admin module

## 0.2.5
    - Integrate pgauth
    - Update auth for pgauth

## 0.2.4
    - Add sign up and user info
    - Fix auth bugs
    - Add captcha

## 0.2.3
    - Fix state bugs if failed in login and change password
    - Add OAuth server controller

## 0.2.2
    - Fix logout last_path bug
    - Change session to logout if not active for specifid seconds
    - Add PRedis to support redis
    - Add AjaxRequest

## 0.2.1
    - Add InstanceController and RestfulInstanceController

## 0.2.0
    - Use Config to manager config files
    - Use ClassLoader to load classes
    - Change framework start workflow
    - Remove ServiceProvider and change services
    - Change all components and remove unused components
    - Add bin/wwtinit.php to initialize project
    - Add bin/maskphp.php to mask and unmask App and bootstrap scripts
    - Remove some libs
    - Change session_start
    - Clear composer packages
    - Add RestfulController

## 0.1.11
    - Add DataPool

## 0.1.10
    - Update BaseController
    - Fix oauth bugs

## 0.1.9
    - Add FWException
    - Change Message to fit FWException
    - Skip ajax in last_path

## 0.1.8
    - Add database pool interface, use MedooPlus instead of medoo, MedooPool instead of MedooDB
    - Split app_config.php into two files, app_config.php and register_config.php
    - Add autoload.php, Loader is only used for loading class
    - Add Register to register instead of Loader
    - Simplify init.php
    - Add last_path and change redirect_path after login
    - Add refreshUser in IAuth
    - Add formatPathArray
    - Add log_directory, backup_directory to config
    - Add TwigView
    - Add twig views
    - Use two templates for normal pages and admin pages
    - Add more Message
    - Add maintainence mode
    - Update asserts lib

## 0.1.7
    - Add reset password in user info
    - Fix getUser() in AuthController when use oauth
    - Add translations

### 0.1.6
    - Fullfil Admin model, admin for departments, roles, users and plugins
    - Add views for admin
    - Add cookie path and domain in config
    - Add Message Model
    - Add sql files for user
    - Change normal login
    - Add user center and user info edit
    - Fix login cookie bugs
    