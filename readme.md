# Laravel 5 Gateway package

An laravel implementation of the Gateway Pattern

```
A gateway encapsulates the semantic gap between the object-oriented domain layer and the relation-oriented persistence layer.
```

Further reading: http://ryantablada.com/post/two-design-patterns-that-will-make-your-applications-better


### Table of contents
 
[TOC]

## Usage

### Step 1: Add the Service Provider

In your `config/app.php` add `Eilander\Repository\Providers\RepositoryServiceProvider:class` to the end of the `providers` array:


```
<?php
'providers' => [
    ...
    Eilander\Gateway\Providers\GatewayServiceProvider::class,
],

```

### Step 2: Add package to composer.json for autoloading

Add the package to the main `composer.json` for autoloading and run `composer dump-autoload`, like so:

```
<?php
   "autoload": {
        "classmap": [
            "database"
        ],
        "psr-4": {
            "App\\": "app/",
            "Eilander\\Gateway\\": "../library/eilander/gateway/src/"
        }
    },
```


```
#!json

composer dump-autoload
```

## Controller example

```
<?php 

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Gateways\ProductGateway as Gateway;

class ProductController extends Controller
{
    /**
     * @var VodafoneGateway
     */
    protected $gateway;

    public function __construct(Gateway $gateway){
        $this->gateway = $gateway;
    }

    public funcion create() {
        return $this->gateway->createProduct();
    }

    ....
}
```

## Gateway example
```
<?php 

namespace  App\Gateways;

use Illuminate\Http\Request;

class TestGateway {

    public function __construct(Request $request) 
    {
        $this->request = $request;
    }

    public function createProduct() 
    {
        // some validation
        ...
        // screate new product
        $product = App\Product::create($this->request->all());
    }
}
```

## Gateway types
Laravel's eloquent (single and multiple) gateways are included.
An empty elasticsearch gateway is also included

## Presenters

Presenters function as a wrapper and renderer for objects.

Fractal Presenter

Requires Fractal.

### Usage

### Create a Transformer

```
use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{
    public function transform(\Post $post)
    {
        return [
            'id'      => (int) $post->id,
            'title'   => $post->title,
            'content' => $post->content
        ];
    }
}
```

### Create a Presenter

```
use Eilander\Gateway\Presenter\FractalPresenter;

class PostPresenter extends FractalPresenter {

    /**
     * Prepare data to present
     *
     * @return \League\Fractal\TransformerAbstract
     */
    public function getTransformer()
    {
        return new PostTransformer();
    }
}
```

### Enabling in your Gateway

```
<?php

namespace  App\Gateways\Eloquent;

use App\Gateways\Eloquent\Contracts\GebruikerGateway as Gateways;
use App\Repositories\Eloquent\Contracts\GebruikerRepository as Repository;
use App\Validators\GebruikerValidator as Validator;
use Eilander\Gateway\Eloquent\EloquentGateway;
use App\Presenters\GebruikerPresenter as Presenter;

/**
 * Class FruitRepository
 */
class GebruikerGateway extends EloquentGateway implements Gateways {
{

    ...

    public function presenter()
    {
        return Presenter::class;
    }
}
```

## Skip Presenter defined in the gateway
Use skipPresenter before any other chaining method

```
$posts = $this->gateway->skipPresenter()->all();
```