# CakeSlimImage plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](https://getcomposer.org).

The recommended way to install composer packages is:


```
composer require lrnzfrr/CakeSlimImage
```

## Use the Plugin

Open Application.php and include the library:

use lrnzfrr\CakeSlimImage\Middleware\CakeSlimImageMiddleware;
```
and in the middleware Queue:
```php
    public function middleware($middlewareQueue)
    {
        $middlewareQueue
            ... middleware ..
            ->add(new CakeSlimImageMiddleware($this))
            .. other middleware....
    }
```

The plugins will catch all json post request with "slim" param, single or multi images, so in your controller just write:
```php      
    if($this->request->getData('slimImage')) {
        $photoData = $this->request->getData('slimImage');
        // process photo data ... 
    } 
```
