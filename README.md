# EasyAdmin Commands

> work in progress

## Installation

```bash
composer require artgris/easy-admin-commands-bundle
```

## Configuration:

in config/packages
### add artgris_easy_commands.yaml:

```yaml
artgris_easy_admin_commands:
    dir: '%kernel.project_dir%/config/packages/easy_admin/entities/'
```
 
and create a new config/packages/easy_admin/ directory 

## Generate easyadmin conf 

```bash
php bin/console artgris:easyadmin:export
```
## Edit easyadmin yaml

```yaml
# config/packages/easy_admin.yaml
imports:
    - { resource: easy_admin/ }
```