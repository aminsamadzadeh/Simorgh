# Simorgh

This package allows you to create simple search query just with array.

## Installing
just run below command:
```sh
composer require aminsamadzadeh/simorgh
```
## Usage

You can use this package in your controller when you want to search somethig.
In first time you must add this package in model that you want to serching.
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use AminSamadzadeh\Simorgh\Filterable;

class Post extends Model
{
    use Filterable;
}
```

If you want just spesific filed can be search you must add filterable in your model.
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use AminSamadzadeh\Simorgh\Filterable;

class Post extends Model
{
    use Filterable;
    protected $filterable = ['name', 'email'];
}
```

in controller you can use use this functionality.

```php
<?php

namespace App\Http\Controllers;
use App\Http\Controllers\Controller;

class PostController extends Controller
{

    public function index()
    {
    	$posts = Post::filter(request()->all())->get();
        return view('users.index', compact('posts'));
    }

}
```

sample of request:

```
/posts?filter[name]=amin&filter[created_at]=(2019/01/01,2020/01/01)&filter[id][]=1&filter[id][]=2&filter[id][]=3&filter[id][]=4
```

```json
{
    "filter": {
        "name": "amin",
        "created_at": "(2019/01/01,2020/01/01)",
        "id": [1,2,3,4,5],
        "category.name": "IT",
        "tag.id": [1,2,3,4,5],
        "sort": "created_at"
    }
}
```
