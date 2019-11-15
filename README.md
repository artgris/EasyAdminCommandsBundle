# EasyAdmin Commands

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

# Basic Configuration

Example Entity :

```php
<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity()
 */
class Example
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(max=255)
     * @Assert\NotBlank
     */
    private $name;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=true)
     */
    private $description;

    /**
     * @var \DateTime
     * @ORM\Column(type="date")
     * @Assert\Date()
     * @Assert\NotBlank()
     */
    private $date;
    
    ...
```

```yaml
artgris_easy_admin_commands:
    dir: '%kernel.project_dir%/config/packages/easy_admin/entities/'
    namespaces:
        - 'App\Entity'
    types:
        text:
            type_options:
                attr: {class: 'tinymce'}
        date:
            type: date
            type_options:
                attr:
                    class: 'flatpickr'
                    autocomplete: 'off'
                widget: 'single_text'
                format: 'dd/MM/yyyy'

    list:
        excluded: 
            - id
    form:
        excluded: 
            - id
```

```bash
php bin/console artgris:easyadmin:export
```

#### generated configuration:
  
```yaml 
# entities/example.yaml :
easy_admin:
    entities:
        example:
            class: App\Entity\Example
            list:
                fields:
                    - name
                    - description
                    - date
            form:
                fields:
                    - name
                    - { property: description, type_options: { attr: { class: tinymce } } }
                    - { property: date, type: date, type_options: { attr: { class: flatpickr, autocomplete: 'off' }, widget: single_text, format: dd/MM/yyyy } }
            edit:
                fields:
                    - name
                    - { property: description, type_options: { attr: { class: tinymce } } }
                    - { property: date, type: date, type_options: { attr: { class: flatpickr, autocomplete: 'off' }, widget: single_text, format: dd/MM/yyyy } }
            new:
                fields:
                    - name
                    - { property: description, type_options: { attr: { class: tinymce } } }
                    - { property: date, type: date, type_options: { attr: { class: flatpickr, autocomplete: 'off' }, widget: single_text, format: dd/MM/yyyy } }
```

## Exhaustive configuration

```yaml 
artgris_easy_admin_commands:
    dir: '%kernel.project_dir%/config/packages/easy_admin/entities/'
    namespaces:
        - 'App\Entity'
    entities:
        included:
            - 'App\Entity\Example'
        excluded:
            - 'App\Entity\User'
    types:
        text:
            type_options:
                attr: {class: 'tinymce'}
        image:
            type: image
            base_path: '%app.path.product_images%'
    regex:
        ^image*: image
        ...
        
    list:
        included: 
            - name
            - ...
        excluded: 
            - id
            - ...
        position: 
            - name
            - ...
    form:
        included: 
            - name
            - ...
        excluded: 
            - id
            - ...
        position: 
            - name
            - ...
```

**dir** : The folder in which the configuration is generated

**namespaces** : Entity search namespaces

**entities**
- **included** : only includes these entities
- **excluded** : exclude the following entities

**types** : If a doctrine type [metadata type](https://www.doctrine-project.org/projects/doctrine-dbal/en/2.9/reference/types.html) is found, the generator will use the associated configuration

**regex** : forces the type of an entity field according to its name and a regex

**list**
- **included** : only includes these fields in the list (if they are present in the entity)
- **excluded** : exclude the following fields from the list (if they are present in the entity)
- **position** : position of fields in the list (if they are present in the entity)
    
**form** : *same as list*

## Export a specific Entity 

> :warning: this command override the configuration parameter 'entities' ('included/excluded')

    php bin/console artgris:easyadmin:export 'App\Entity\Example'
    or  
    php bin/console artgris:easyadmin:export App\\Entity\\Example
