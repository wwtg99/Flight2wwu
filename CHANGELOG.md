# ChangeLog

## 0.2.0
    - Use Config to manager config files
    - Use ClassLoader to load classes
    - Change framework start workflow
    - Remove ServiceProvider and change services
    - Change all components and remove unused components
    - Add bin/wwtinit.php to initialize project
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
    