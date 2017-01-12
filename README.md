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

#### Config Nginx
Add in server 
```
location / {
    try_files $uri $uri/ /index.php;
}
```

#### Config framework
Change conf files in App/config. At most time only change app_config.php.

## Directories
 - App: application directory
    - config: config files
      - lang: translation files
    - Controller: controller class
    - Model: model class
    - view: view templates
    - Plugin: plugin class
 - bootstrap: bootstrap scripts
 - storage: storage directory, write access
    - log: default log directory
    - tmp: default tmp directory (ex. config cache file)
 - web: web document root

## Controllers
#### BaseController
The super class for all controllers.

#### RestfulController
The super class for all Restful controllers.
Subclass must implement 7 functions.
- index: URI /resources Method Get, list all resources
- show: URI /resources/{id} Method Get, get one resource by id
- create: URI /resources/create Method Get, view to create resource, used for restful+
- store: URI /resources Method Post, create resource
- edit: URI /resources/{id}/edit Method Get, view to update resource, used for restful+
- update: URI /resource/{id} Method Put/Patch, update resource, Put to update all data (null for data not provided), Patch to update provided data
- destroy: URI /resource/{id} Method Delete, delete resource

#### RestfulAPIController
The best super class for restful APIs.
Implement all functions in RestfulController (disable create and edit), and provided 5 functions to handle resources:
- listResources: list resources
- getResource: get one resource
- createResource: create resource
- updateResource: update resource
- deleteResource: delete resource

And 3 attributes:
- filterFields: fields to filter
- createFields: fields for creation
- updateFields: fields for update

#### RestfulPlusController
The best super class for restful view controllers. 
Implement all functions in RestfulController, all functions will return by view.

## RestFul APIs Definition
#### Resource URI，represent one resource, example: http://example.com/resources/

#### If have errors, error message will be provided as {"error": "message", "code": 1} .

#### URIs and Methods（zoos as example）:

| Method |     URI    |  Action  |
|--------|------------|----------|
|GET     |/zoos       |get resource list   |
|POST    |/zoos       |create resource  |
|GET     |/zoos/{id}  |get resource by id    |
|PUT     |/zoos/{id}  |update resource by id (must provided all fields)  |
|PATCH   |/zoos/{id}  |update resource by id (provided partial fields)  |
|DELETE  |/zoos/{id}  |delete resource by id |

#### Paging、Order and Filters
Get resource list support paging, order and filters.

Paging：

Method one:

page: page number
page_size: number per page, optional, default 100
Example: ?page=1&page_size=100

Method two:

limit: number limitation
offset：number offset, optional, default 0
Example: ?limit=100&offset=10

Order:

Use sort parameter， >field to sort by field ascending, <field to sort by field descending, use comma (,) to separate fields,
Example: ?sort=>name,<age

Filters:

Use expression to filter, supported expression: =, >=, <=, !=。
Example：?name=Tom
?age>=10
?name!=Tim

#### Selected fields
Get resource list and get one resource support select fields.
Use fields parameters, comma (,) to separate.
Example: ?fields=name,age,dob,pob 
If fields is count, then show number of data.

#### Return HTTP code
* GET return `200`。
* POST create resource successfully returning `201`, create resource failed returning `200` and error message, server error returning `500`.
* PUT/PATCH update resource successfully returning `201`, update resource failed returning `200` and error message, server error returning `500`。
* DELETE delete resource successfully returning `204`, delete resource failed returning `200` and error message, server error returning `500`。
