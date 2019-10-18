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
        exclude: ['id']
    form:
        exclude: ['id']
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
