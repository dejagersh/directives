# Directives

this package allows you to easily add new directives from Blade files.

## How to set it up.
1. First thing you need to do is install the package. You can do this with composer by running 'composer require thejager/directives'.
2. Next thing you need to do is add the service provider `TheJager\Directives\DirectivesServiceProvider` to your config/app.php.
3. Create the directory 'resources/views/directives'.


## How to use it (example)
Let's create a `@date()` directive. When using `@date(Carbon\Carbon::now())` in a blade view, we want it to output the current date in dd-mm-yyyy format.

1. First thing we do is create the blade file. Create a new Blade template 'date.blade.php' in the 'resources/views/directives/' folder containing `{{ $param->format('d-m-Y') }}`. Note that the parameter passed to the directive is stored in `$param`. At this moment you can not pass multiple parameters, but you could pass an array of parameters if you want ;)
2. That's it. You can now use the `@date()` directive in your Blade  files to which you can pass any Carbon instance.


## Suggestions
Feel free to suggest changes, collaborate, suggest new features etc.
