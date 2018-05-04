For this laravel tutorial, we will create a bare bone forum and define the CRUD method of its database.

The first step is to create the project with composer:

`composer create-project --prefer-dist laravel/laravel forum`

Then we create the controller and the model:

```
cd forum
php artisan make:model -m -c -r Forum
```

Next, we create the mysql database along with a user:

```
mysql -u root -p 
create database forum;
grant all privileges on forum.* to user identified by 'password';
flush privileges;
```

Then we have to fill in the DB_* fields in the .env file with database info (APP_KEY is auto created, there's no need to touch it):

```
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:<something>
APP_DEBUG=true
APP_URL=http://localhost

LOG_CHANNEL=stack

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=forum
DB_USERNAME=user
DB_PASSWORD=password

BROADCAST_DRIVER=log
CACHE_DRIVER=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
QUEUE_DRIVER=sync

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

MAIL_DRIVER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_APP_CLUSTER=mt1

MIX_PUSHER_APP_KEY="${PUSHER_APP_KEY}"
MIX_PUSHER_APP_CLUSTER="${PUSHER_APP_CLUSTER}"`
```

Then we edit `database/migrations/*create_forum_models_table.php` to add the line:

```php
$table->text('content');
```

You should end up with something like this:

```php
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForumsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('forums', function (Blueprint $table) {
            $table->increments('id');
            $table->text('content');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('forums');
    }
}
?>
```

Then we actually create the tables in the database with :

```
php artisan migrate
```

Now you can start programming :) Let's start by creating a route in `routes/web.php` :

```php
<?php
	Route::get('/', 'ForumController@index');
?>
```

This one refers to the index method of the controller created by `php artisan make:controller ForumController --resource`
It implements the R (Read) part of the CRUD.

So we now modify the index method of the file `app/Http/Controllers/ForumController.php` like this :

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use App\Forum;

class ForumController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $contents = Forum::All();
	return view('home', ['content' => $content, 'title' => 'index']);
    }
}
?>
```

It loads the content of the forums table by calling the superclass of our model in `app/Forum.php`, since it is still empty. We also have to specify the
namespace `Illuminate\Database\Eloquent\Model` for using the eloquent framework and `App\Forum` for our model.
Then it returns the home view with the content (an associative array containing the content of the database table forum_models) and a title of our own
choosing.

The home view is located in `resources/views/home.blade.php`, we'll fill it with a few pseudo-instructions for displaying the content of the database table and some
html to implement the C (create) part of the CRUD :

```laravel
@include('header')
<h1><a href="/index.php/create">Create</a></h1>
@each('forumView', $content, 'content')
@include('footer')
```

the `@include` at the beginning and the end include two other views with opening and closing html. So the content of the header sub-view in 
`resources/views/header.blade.php` is straightforward :

```html
<html>
	<head>
		<title>{{$title}}</title>
	</head>
    <body>
```

It is actually only the opening html which displays the title passed in parameter by the controller. The content of the footer sub-view in 
`resources/view/footers.blade.php` is even simpler, since it does not uses any controller parameter :

```html
    </body>
</html>
```

Last, but not the least, that `@each` directive in `resources/views/home.blade.php` loops on each result of the database table and inserts a sub-view
`resources/views/forumView.blade.php` for each row of the content column in the database. It associates a `$content` variable with the content parameter of the
controller.

The sub-view in `resources/views/forumView.blade.php` is the heart of our web application. It displays the content column of the row in `$content`, and adds html forms
to implement the U (update) and D (delete) of the CRUD. They're implemented by forms which overload their POST method with a `@method`, and add a `@csrf` for security.

```html
<p>
	{{$content->content}}
	<form action="/index.php/show/" method="POST">
	   @csrf
	   <input type="hidden" name="id" value={{$content->id}}>
	   <input type="submit" value="Read">
	</form>
	<form action="/index.php/edit/" method="POST">
	   @method('PUT')
	   @csrf
	   <input type="hidden" name="id" value={{$content->id}}>
	   <input type="submit" value="Update">
	</form>
	<form action="/index.php/destroy/" method="POST">
	   @method('DELETE')
	   @csrf
	   <input type="hidden" name="id" value={{$content->id}}>
	   <input type="submit" value="Delete">
	</form>
</p>
```

The next method managed by the controller is `create()`. So we next add a route to match it in `routes/web.php` :

```php
Route::get('/create', 'ForumController@create');
```

The method corresponding to that route in the controller merely returns the corresponding view :

```php
public function create()
{
    return view('create', ['title' => 'create']);
}
```

The `resources/views/create.blade.php` it references is a textarea html form :

```html
@include('header')
<form action="/index.php/store/" method="POST">
    @csrf
    <textarea name="content" placeholder="enter your comment here"></textarea>
	<input type="submit" value="post">
</form>
@include('footer')
```

This form action in turn references the store route, albeit this time with a POST method, not a GET, so we'll add it to our routes in `routes/web.php` :

```php
Route::post('/store/', 'ForumController@store');
```

This in turn references the store method in our `app/Http/Controllers/ForumController.php` controller, which we modify thusly :

```php
public function store(Request $request)
{
    $myDb = new Forum();
    $myDb->content = $request->get('content');
    $myDb->save();
	return redirect('/');
}
```

It takes the content part of what is passed to the controller by the form in the `$request` parameter, and put it in the database using the `save()` method of the
Eloquent class.

The following method handled by the controller is show, to display the content of a single item. So we'll create a corresponding route :

```php
Route::post('/show', 'ForumController@show');
```

It is called by the first form in our `resources/views/forumView.blade.php` sub-view. This one uses a POST method to send the id parameter, so we'll have to use a more
convoluted way to get it. It is implemented like this:

```php
public function show(Request $request)
{
    $id = $request->input('id');
    $content = Forum::find($id);
    return view('show', ['content' => $content, 'title' => 'Read a single item']);
}
```

This time, instead of capturing the parameter in the route, it is accessed by the controller using the input method of the parameter `Request` class. Then the `find()`
method of the Eloquent database access retrieve the id row in the database. Finally, the show view is returned with the database row and a title.

The show view in `resources/views/show.blade.php` displays all the fields of the database row :

```html
@include('header')
id : {{$content->id}}<br>
content : {{$content->content}}<br>
created_at : {{$content->created_at}}<br>
updated_at : {{$content->updated_at}}<br>
<a href="/index.php">Home page</a>
@include('footer')
```
 
The next method handled by the controller is `edit()`, which implements the U (update) part of the CRUD. Its implementation is identical to the show() method, 

```php
public function edit(Request $request)
{
    $id = $request->input('id');
    $content = Forum::find($id);
    return view('edit', ['content' => $content, 'title' => 'edit a single item']);
}
```

However the called view in `resources/views/edit.blade.ph`p is a form with a `@csrf` for security, a hidden id input tag, and a predefined value to update the
database record:

```html
@include('header')
<form action="/index.php/update" method="POST">
    @csrf
    @method('PUT')
    <input type="hidden" name="id" value="{{$content->id}}" >
    <textarea name="content">{{$content->content}}</textarea>
	<input type="submit" value="Update">
</form>
@include('footer')
```

The route called in the previous view must be added in `routes/web.php`:

```php
Route::put('/update', 'ForumController@update');
```

And the update method in the `app/Http/Controllers/ForumController` controller must also be filled in:

```php
public function update(Request $request)
{
    $id = $request->input('id');
    $content = Forum::find($id);
	$content->content = $request->input('content');
	$content->save();
	   
	return redirect('/');
}
```

It retrieves the id parameter in the input tag of the form `view resources/views/edit.blade.php` (`<input ... name="id">`) on line 3, gets the database record
corresponding to that id, update it with the content of the `textarea` tag (`<textarea ... name="id">`) and finally update the database with the `save()` method.
Then it returns a redirect to the home page.

The final method of the `app/Http/Controllers/ForumController` controller is `destroy(`) which is called by the delete button of our main form in
`resources/views/home.blade.php`. We must then add a route in `routes/web.`:

```php
Route::delete('/destroy', 'ForumController@destroy');
```

This route calls then the `destroy() method in the `/Http/Controllers/ForumController` controller:

```php
public function destroy(Request $request)
{
    $id = $request->input('id');
    $content = Forum::find($id);
    $content->delete();

    return redirect('/');
}
```

Similar to the previous methods of the controller, it gets the id value from an html element with the `name='id'` attribute, gets the corresponding row in the database,
and calls the delete eloquent method to do the `DROP DATABASE` sql command. Then it returns the home page.
