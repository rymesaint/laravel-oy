## About (Unofficial) LARAVEL OY Payment Indonesia

This is a laravel library for OY Payment Indonesia.

**Installation**

You can install the package via composer :
```
composer require rymesaint/laravel-oy
```

The package will register itself automatically.

Then publish the package configuration file
```
php artisan vendor:publish --provider=rymesaint\LaravelOY\LaravelOYServiceProvider
```

**Usage**

Setup your OY Payment configuration then

```
$payment = new OYPayment();
$payment->getInvoices($offset, $limit, $status);
```

or using an alias

```
OYPayment::getInvoices($offset, $limit, $status);
```

**Contributing**

Suggestions, pull requests, bug reporting and code improvements are all welcome.