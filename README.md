# Doctrine object mapping

[![Latest Version][badge-release]][release]
[![Software License][badge-license]][license]
[![PHP Version][badge-php]][php]
[![Total Downloads][badge-downloads]][downloads]

Maps immutable object and object list to single json field.

Installation
============

Make sure Composer is installed globally, as explained in the
[installation chapter](https://getcomposer.org/doc/00-intro.md)
of the Composer documentation.

Applications that don't use Symfony 
-----------------------------------

### Step 1: Download library

```console
$ composer require sbooker/doctrine-object-mapping 
```
### Step 2: Create and register Doctrine type
```php
class Concrete { /* ... */ }

class ConcreteType extends \Sbooker\DoctrineObjectMapping\ObjectType 
{
    protected function getObjectClass(): string
    {
        return Concrete::class;
    }

    public function getName()
    {
        return 'concrete';
    }
}

\Doctrine\DBAL\Types\Type::addType('concrete', ConcreteType::class);

// Create or get normalizer
$normalizer = new \Symfony\Component\Serializer\Normalizer\ObjectNormalizer(/*...*/);

// Set serializer to type on boot application
/** @var \Sbooker\DoctrineObjectMapping\NormalizableType $type */
$type = \Doctrine\DBAL\Types\Type::getType('concrete');
$type->setNormalizer($normalizer);
$type->setDenormalizer($normalizer);
```

Applications that use Symfony 
-----------------------------

Use [`sbooker/doctrine-object-mapping-bundle`](https://github.com/sbooker/doctrine-object-mapping-bundle)

## License
See [LICENSE][license] file.

[badge-release]: https://img.shields.io/packagist/v/sbooker/doctrine-object-mapping.svg?style=flat-square
[badge-license]: https://img.shields.io/badge/license-MIT-brightgreen.svg?style=flat-square
[badge-php]: https://img.shields.io/packagist/php-v/sbooker/doctrine-object-mapping.svg?style=flat-square
[badge-downloads]: https://img.shields.io/packagist/dt/sbooker/doctrine-object-mapping.svg?style=flat-square

[release]: https://img.shields.io/packagist/v/sbooker/doctrine-object-mapping
[license]: https://github.com/sbooker/doctrine-object-mapping/blob/master/LICENSE
[php]: https://php.net
[downloads]: https://packagist.org/packages/sbooker/doctrine-object-mapping