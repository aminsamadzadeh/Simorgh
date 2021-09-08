# Simorgh

This package allows you to create simple search query just with query string.

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

class User extends Model
{
    use Filterable;
}
```

If you want just spesific filed just can be search you must add `$filterable` in your model.
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use AminSamadzadeh\Simorgh\Filterable;

class User extends Model
{
    use Filterable;
    protected $filterable = ['name', 'email'];
}
```

In controller you can use this functionality.

```php
<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;

class UserController extends Controller
{

    public function index()
    {
    	$users = User::filter(request()->all())->get();
        return view('users.index', compact('users'));
    }

}
```

## Queries
### Simple Where
Request query string with simorgh:
```
/users?filter[name]=amin
```
Eloquent Query without simorgh:
```php
$users = User::where('name', 'amin')->get();
```
SQL Raw Query:
```sql
select * from "users" where "name" = 'amin'
```

### Relational
Request query string with simorgh:
```
/articles?filter[images.id]=1
```
Eloquent Query without simorgh:
```php
$articles = Article::whereHas('images',function ($q) {
                $q->where($id, 1);
            }
        );
```
SQL Raw Query:
```sql
select * from "articles" where exists (select * from "images" where "articles"."id" = "images"."article_id" and "id" = 1)
```
### Range (Between)
Request query string with simorgh:
```
/users?filter[created_at]=(1970-01-01,1970-02-01)
```
Eloquent Query without simorgh:
```php
$users = User::whereBetween('created_at', ['1970-01-01', '1970-02-01'])->get();
```
SQL Raw Query:
```sql
select * from "users" where "created_at" between 1970-01-01 and 1970-02-01
```
### Array
Request query string with simorgh:
```
/users?filter[id][]=1&filter[id][]=2&filter[id][]=3
```
Eloquent Query without simorgh:
```php
$users = User::where('id', [1,2,3])->get();
```
SQL Raw Query:
```sql
select * from "users" where "id" in (1, 2, 3)
```
### Sort
Request query string with simorgh:
```
/users?filter[sort]=created_at&filter-meta[sort-order]=desc
```
Eloquent Query without simorgh:
```php
$users = User::orderBy('created_at', 'desc')->get();
```
SQL Raw Query:
```sql
select * from "users" order by "created_at" desc
```

## Settings
### Meta
To change oporator of query you can use meta in query string.
```
https://host.com/users?filter[name]=amin?filter-meta[name][op]=like
```
set default meta in model:
```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use AminSamadzadeh\Simorgh\Filterable;

class User extends Model
{
    use Filterable;
    protected $filterable = ['name', 'email'];
    protected $filterMeta = ['name' => ['op' => 'like']];
}
```
Note: Support oporators `like`, `=`

### Alias for query string names(filter and filter meta)
If want change filter and filter meta query string name you can add changed name to model.

```php
.
.
.

class User extends Model
{
    use Filterable;
    protected $filter_name = 'f';
    protected $filter_meta = 'fm';

}
```