<?php

namespace AminSamadzadeh\Simorgh\Tests;

use Illuminate\Database\Eloquent\Model;
use AminSamadzadeh\Simorgh\Filterable;
use Illuminate\Database\Capsule\Manager as Capsule;
use Orchestra\Testbench\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QueriesTest extends TestCase
{
    use RefreshDatabase;
    public $capsule;

    protected function setUp(): void
    {
        parent::setUp();
        $this->createSchema();
    }

    /** @test */
    public function emptyFilterRequestQueryTest()
    {
        $users = User::filter(['foo' => 'bar']);
        $rawQuery = 'select * from "users"';
        $this->assertEquals($users->toSql(), $rawQuery);
    }

    /** @test */
    public function simpleQueryTest()
    {
        $users = User::filter(['filter' => ['email' => 'amin']]);
        $rawQuery = 'select * from "users" where "email" = ?';
        $this->assertEquals($users->toSql(), $rawQuery);
    }

    /** @test */
    public function intervalQueryTest()
    {
        $users = User::filter([
                'filter' => [
                    'created_at' => '(1970-01-01,1970-02-01)'
                ]
            ]);
        $rawQuery = 'select * from "users" where "created_at" between ? and ?';
        $this->assertEquals($users->toSql(), $rawQuery);
    }

    /** @test */
    public function arrayQueryTest()
    {
        $users = User::filter([
            'filter' => [
                'id' => [1,2,3]
            ]
        ]);
        $rawQuery = 'select * from "users" where "id" in (?, ?, ?)';
        $this->assertEquals($users->toSql(), $rawQuery);
    }

    /** @test */
    public function relationalManyToManyQueryTest()
    {
        $articles = Article::filter([
            'filter' => [
                'users.name' => 'amin'
            ]
        ]);
        $rawQuery =
        'select * from "articles" where exists (select * from "users" inner join "article_user" on "users"."id" = "article_user"."user_id" where "articles"."id" = "article_user"."article_id" and "name" = ?)';
        $this->assertEquals($articles->toSql(), $rawQuery);
    }

    /** @test */
    public function relationalOneToManyQueryTest()
    {
        $articles = Article::filter([
            'filter' => [
                'images.id' => 1
            ]
        ]);

        $rawQuery =
            'select * from "articles" where exists (select * from "images" where "articles"."id" = "images"."article_id" and "id" = ?)';
        $this->assertEquals($articles->toSql(), $rawQuery);
    }

    /** @test */
    public function relationalManyToManyArrayQueryTest()
    {
        $articles = Article::filter([
            'filter' => [
                'users.name' => ['amin', 'nima', 'samin']
            ]
        ]);
        $rawQuery =
        'select * from "articles" where exists (select * from "users" inner join "article_user" on "users"."id" = "article_user"."user_id" where "articles"."id" = "article_user"."article_id" and "name" in (?, ?, ?))';
        $this->assertEquals($articles->toSql(), $rawQuery);

    }

    /** @test */
    public function relationalOneToManyArrayQueryTest()
    {
        $articles = Article::filter([
            'filter' => [
                'images.id' => [1, 2, 3]
            ]
        ]);

        $rawQuery =
            'select * from "articles" where exists (select * from "images" where "articles"."id" = "images"."article_id" and "id" in (?, ?, ?))';
        $this->assertEquals($articles->toSql(), $rawQuery);
    }

    /** @test */
    public function sortQueryTest()
    {
        $usersDefaultSort = User::filter(['filter' => ['sort' => 'created_at']]);
        $usersAscSort = User::filter([
            'filter' => ['sort' => 'created_at'],
            'filter-meta' => 'foobar'
        ]);
        $usersDescSort = User::filter([
            'filter' => ['sort' => 'created_at'],
            'filter-meta' => ['sort-order' => 'desc']
        ]);

        $ascRawQuery = 'select * from "users" order by "created_at" asc';
        $descRawQuery = 'select * from "users" order by "created_at" desc';

        $this->assertEquals($usersDefaultSort->toSql(), $ascRawQuery);
        $this->assertEquals($usersAscSort->toSql(), $ascRawQuery);
        $this->assertEquals($usersDescSort->toSql(), $descRawQuery);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('database.default', 'testdb');
        $app['config']->set('database.connections.testdb', [
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);
    }
     /**
     * Setup the database schema.
     *
     * @return void
     */
    public function createSchema()
    {
        $this->capsule = new Capsule;
        $this->capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:'
        ]);
        $this->capsule->bootEloquent();
        $this->capsule->setAsGlobal();

        $this->capsule->schema()->create('users', function ($table) {
            $table->increments('id');
            $table->string('email')->unique();
            $table->timestamps();
        });

        $this->capsule->schema()->create('articles', function ($table) {
            $table->increments('id');
            $table->string('title');
        });

        $this->capsule->schema()->create('article_user', function ($table) {
            $table->integer('article_id')->unsigned();
            $table->foreign('article_id')->references('id')->on('articles');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')->references('id')->on('users');
        });

        $this->capsule->schema()->create('images', function ($table) {
            $table->increments('id');
            $table->integer('article_id')->unsigned();
            $table->foreign('article_id')->references('id')->on('articles');
        });

    }

}

class User extends Model
{
    use Filterable;
    protected $filterable = [
        'id', 'email', 'created_at'
    ];

    public function articles()
    {
        return $this->belongsToMany(Article::class);
    }
}

class Article extends Model
{
    use Filterable;
    protected $filterable = [
        'users.name', 'images.id'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class);
    }

    public function images()
    {
        return $this->hasMany(Image::class);
    }
}

class Image extends Model
{
    use Filterable;
    protected $filterable = ['id'];

    public function article()
    {
        return $this->belongsTo(Article::class);
    }
}
