# Patchwork

*Note : the docs aren't fully finished yet, but it is coming very soon.*

**Patchwork** is a **PHP 5.4+ full-stack web framework**, which main purpose is to gather the best tools available together in order to provide a minimalist yet complete start point for building small to medium **web applications**.

## Round table

### Package management

- [Composer](https://getcomposer.org/) : the now standard package manager for PHP.

- [NPM](https://www.npmjs.org/) : node.js's package manager, which is used to install front-end dependencies along with Napa.

### Back-end

- [Silex](https://github.com/silexphp/Silex) : a micro-framework built around Symfony components, which provides a dependency injection container and a basic web application structure. It comes in Patchwork along with multiple service providers.

- [Twig](https://github.com/twigphp/Twig) : Symfony's *de facto* templating engine, used by default in Silex. It comes in Patchwork along with multiple extensions.

- [Swift Mailer](http://swiftmailer.org/) : Symfony's *de facto* mail sender, used by default in Silex.

- [RedBean](https://github.com/gabordemooij/redbean) : an ORM which particularity is that it creates your database schema itself as you write code. Its integration with Silex is handled by a dedicated service provider, in order to make it available as an instance rather than a static facade.

- [Monolog](https://github.com/Seldaek/monolog) : the standard logger for PHP.

- [Environ](https://github.com/neemzy/environ) : an environment manager which allows defining environments triggered by a boolean callback (typically based on hostname detection) and executing environment-specific code.

- [Image Workshop](http://phpimageworkshop.com/) : an image library used for resizing and cropping images.

- [PHPUnit](https://github.com/sebastianbergmann/phpunit) : one of the major unit tests tools for PHP.

### Front-end

- [gulp](http://gulpjs.com/) : a task runner for asset processing and optimization, which allows for live compilation and reloading during development. Along with the libraries listed below, it makes use of [Autoprefixer](https://github.com/postcss/autoprefixer), [CSSO](https://bem.info/tools/optimizers/csso/), [JSHint](http://jshint.com/), [UglifyJS]() and image minification and font conversion utilities.

- [LESS](http://lesscss.org/) : a CSS preprocessor.

- [Browserify](http://browserify.org/) : a front-end JavaScript dependency manager, which allows using CommonJS's `require` method to help keep a good code structure.

- [Bootstrap](https://github.com/twbs/bootstrap) : Twitter's HTML/CSS framework, powered by LESS and used to build back-office user interfaces.

- [Behat](https://github.com/Behat/Behat) and [Mink](https://github.com/Behat/Mink) : functional tests tools, that can pilot a browser to use your app. Running JavaScript-capable tests will require the use of [Selenium](http://docs.seleniumhq.org/) and [PhantomJS](http://phantomjs.org/).

## Structure and installation

All code that could possibly handle inheritance/dispatching in one way or another (which includes PHP classes, basic LESS stylesheets and [NicEdit](http://nicedit.com/), a JavaScript WYSIWYG editor) is contained into the framework's **core**, available [as a separate package](https://github.com/neemzy/patchwork-core). The framework's repository itself is mainly a sample app - about pizzas - designed to help you getting started quickly.

The best way to start is to use Composer, which will clone that repository and install all required dependencies (make sure Composer and NPM are installed first) :

```
composer create-project neemzy/patchwork -s dev [directory] && cd $_
```

You must use the `-s dev` option to match Patchwork's `minimum-stability` flag, which is required to provide the latest version of some packages (such as Behat 3).

After installing dependencies, the installer will ask you if you want to remove Git history from the repository. You want to agree to that, since you are building your own project and not contributing to this one !

You then have to check a few steps :

- `var/db` and `var/log` should be writable by your web server's user.
- Your web server/virtual host should be configured right. A sample `.htaccess` file is provided in the `public` directory, if by any chance you use [Apache](https://www.apache.org/).
- For development, Gulp should be installed globally (`npm install -g gulp`).
- For development as well, [SQLite](https://sqlite.org/) and the corresponding PHP extension should be installed (`sudo apt-get install sqlite php5-sqlite` on Debian and Ubuntu).

At this point, you may want to adapt some configuration settings :

- Set your app's root URL in `gulpfile.js` and `behat.yml`.
- Define your project's PHP namespace in `composer.json` and `app/config/settings/common.yml`.
- In the latter file, edit your app's title and short description.

Finally, run `gulp` to have your browser open at the URL you have chosen above and a livereload server started, and start coding !

## Directory structure

This is a quick tour around Patchwork's directory structure, in order to take a first glance at how it works. The following represents the directory structure's state as of installation, and is by no mean an obligation to follow it in its deepest details, as most of it can be set up differently through appropriate configuration :

```
app                : application data and code
|-- assets         : raw assets (LESS stylesheets, JS modules...)
|-- config         : YAML configuration files
    |-- i18n       : translations
    |-- settings   : per-environment application settings
|-- src            : PHP code
    |-- Controller : business logic classes
    |-- Model      : entity classes
|-- tests          : automated tests
    |-- functional : Behat tests
    |-- unit       : PHPUnit tests
|-- views          : Twig templates
public             : web root
|-- assets         : compiled assets (CSS stylesheets, single JS file...)
|-- upload         : user-uploaded files
var                : runtime-specific files
|-- db             : SQLite development databases
|-- log            : log files
```

## Bootstrap file

Patchwork's main file is located at `app/bootstrap.php`. This file is included by `public/index.php` in order to bootstrap the Silex application, which the latter then runs. It is responsible to service and controller binding as well as some extra configuration, both of which you will most certainly find yourself tweaking when starting crafting your own app.

Note that topics explained below are, for the most part, already pre-configured in the initial setup, and are detailed here for clarity purposes (helping you tweak them to suit your needs).

### Setting your application's base path

Use `$app['base_path'] = dirname(__DIR__);` to expose your app's base path (`FileModel` needs it for file uploads).

### Defining environments

Register an instance of `Neemzy\Silex\Provider\EnvironServiceProvider` to your Silex app and feed it `Environment` instances :

```php
use Neemzy\Silex\Provider\EnvironServiceProvider;
use Neemzy\Silex\Provider\Environment;

$app->register(
    new EnvironServiceProvider(
        [
            'dev' => new Environment(
                function () {
                    return preg_match('/localhost|192\.168/', $_SERVER['SERVER_NAME']);
                },
                function () {
                    // development-specific code
                }
            ),
            'prod' => new Environment(
                function () {
                    return true;
                },
                function () {
                    // production-specific code
                }
            )
        ]
    )
);
```

### Configuration

Config parameters are read from YAML files read from `app/config/settings`, the best practice being to define one file per environment (see above) that all include and extend a `common.yml` file if required.

### Translations

Translations are also read from YAML files, this times coming from `app/config/i18n`. As they are fed to an instance of `Symfony\Component\Validator\Validator`, they eventually belong a specific domain (allowing Silex to automatically fetch them for translating validation error messages or date formatting, among other things). Patchwork thus allows you to easily get your translations loaded correctly, by naming your files `[domain].[locale].yml`, e.g. `validators.fr.yml`.

A file name without a domain (like `en.yml`) will be loaded to the default domain, and may serve for generic translations.

## Back-end development

### Views

Views are handled by Twig. The main thing to remember is that your application is accessible in templates through the `app` variable, which will let you expose pretty much anything you may need. Twig extensions are also defined in `app/bootstrap.php`.

The recommended directory structure for your `app/views` directory looks like this :

```
admin             : back-office templates
|-- [model name]  : pages for a model
    |-- list.twig : list template
    |-- post.twig : form template
|-- layout.twig   : back-office layout
front             : front-office templates
|-- includes      : reusable components (often linked to a stylesheet and maybe a JS module)
|-- partials      : pages
|-- layout.twig   : front-office layout
```

[Twig manual](http://twig.sensiolabs.org/documentation)

#### Asset paths

Patchwork brings in [Simple Twig Asset Extension](https://github.com/Entea/Silex-Twig-Simple-Asset-Extension) :

```twig
<script src="{{ asset('js/script.js') }}"></script>
```

### Models

Model classes (located at `app/src/Model`) must extend `Neemzy\Patchwork\Model\Entity`. See `app/src/Model/Pizza.php` for a short yet working example.

#### RedBean

The `Entity` class itself extends `RedBean_SimpleModel`, empowering your models with ORM functionalities. Table names in database are thus the same as your classes' names (lowercased).

RedBean's configuration (including connection informations as well as model class namespacing) is done in the service provider's registration in `app/bootstrap.php`.

[RedBean manual](http://www.redbeanphp.com/)

#### Validation

Validation constraints are defined through `public static function loadValidatorMetadata(ClassMetadata $metadata)`. You must [getter constraints](http://api.symfony.com/2.0/Symfony/Component/Validator/Mapping/ClassMetadata.html#method_addGetterConstraint) and to define getters for your model's members accordingly as no properties shall be directly defined, in order to let RedBean's catch-all getter do its magic. Doing so also allows you to keep control over the validated data.

[Symfony validation manual](http://symfony.com/doc/current/book/validation.html)

#### Traits

Patchwork uses a poor man's event dispatching system through PHP traits in order to share common entity functionality.
It works by enabling the possibility of dispatching a method call among the class's used traits, through an eponymous method :

```php
$pizza = $app['redbean']->dispense('pizza');

// Let's assume the Pizza model uses FileModel, SortableModel and TogglableModel traits (see below)
$pizza->dispatch('example'); // will call fileExample, sortableExample and togglableExample methods, if they exist

// Some dispatching is already pre-handled through RedBean's hooks
$app['redbean']->store($pizza); // will dispatch "update" and call fileUpdate, sortableUpdate and togglableUpdate
$app['redbean']->trash($pizza); // will dispatch "delete" and call fileDelete, sortableDelete and togglableDelete
```

As you understood, methods called by `dispatch` are typically brought in by traits but remain overrideable by the target class if required.

Some traits are available out of the box but you can roll out your own if required.

##### `FileModel`

This trait enables support for files :

- Upon file upload, moves it to a permanent location and generate its name
- Upon model deletion, deletes related files

File fields getters in your models should expose the file's full path, but returning `$this->getFilePath($field, true)`;

##### `ImageModel`

This trait extends `FileModel` (which should no more be `use`d in your model once you `use` this one) and adds automatic image resizing support by detecting `MaxWidth` and `MaxHeight` validation constraints.

##### `SlugModel`

(TODO)

##### `SortableModel`

(TODO)

##### `TimestampModel`

(TODO)

##### `TogglableModel`

(TODO)

### Controllers

#### Routing

Controllers in Patchwork are plain Silex controllers, where you basically bind a callback to an URL/HTTP method couple. You can "mount" them to an URL endpoint in `app/bootstrap.php` :

```php
use Neemzy\Patchwork\Controller\FrontController;

$app->mount(
    '/',
    new FrontController()
);
```

[Silex manual](http://silex.sensiolabs.org/documentation)

#### `EntityController`

The `EntityController` class describes a controller dedicated to managing a certain model (its constructor takes a table name as a parameter). This class is abstract and extended by :

- `AdminController`, that binds an HTTP authentication to itself and exposes CRUD GUI routes.
- `ApiController`, that exposes a RESTful API for your models and takes a boolean `$readonly` (second) parameter to know whether it should expose `POST`/`PUT` routes.

You can inherit from these classes in your own controllers in order to customize their behaviour. To do so, you simply need to declare a `connect` method :

```php
public function connect(Application $app)
{
    $ctrl = parent::connect($app);

    // Bind your own routes on top of the parent ones

    return $ctrl;
}
```

You can also create your own entity controllers by extending the abstract class itself (e.g. if you need a `RSSController` or something among these lines).

### Third-party packages

Adding new packages to your application is as simple as running `composer require [package]` (eventually along with the `--dev` option, depending on whether the package will be used on production).

## Front-end development

### Workflow

When running `gulp` without a parameter, it runs a `workflow` task which processes your assets and launches a livereload server triggered by a file change in a template or config file, or directly in an asset (which also rebuilds it specifically).

Once you're done with coding, kill the livereload server and run `gulp --dist` to generate production-ready assets.

### Stylesheets

You can either use LESS or plain CSS.

The recommended structure for your `app/assets/less` directory looks like this :

```
front                  : front-office stylesheets
|-- imports            : generale purpose styles and scaffolding
    |-- fonts.less     : font configuration (see below)
    |-- mixins.less    : helper mixins
    |-- variables.less : style variables
|-- modules            : reusable components
|-- main.less          : main stylesheet that includes both directories' contents
admin.less             : back-office stylesheet
```

#### Main stylesheet

Check out `app/assets/less/front/main.less` to see how core stylesheets are used :

- It includes `reset.less`, which homogenizes styles across browsers.
- It includes a local `variables.less` file which extends the core's eponymous one (see below).
- It includes a local `fonts.less` file to define fonts (see below).
- It includes a local `mixins.less` file which extends the core's eponymous one (see below).

#### Variables

Here are the core-defined (and overrideable) variables :

```less
@max-width; // Maximum page width, above which no more responsive styles will be applied (and the content will not enlarge more)
@min-width; // Minimum page width, under which no more responsive styles will be applied (and the content will not narrow more)
@background; // Default background color
@font-family; // Default text font
@font-size; // Default text size
@font-color; // Default text color
@placeholder-color; // HTML5 placeholder text color

@phone-max; // "Phone" responsive screen category maximum width
@phone-only; // Media query targetting "phone" screens only (from @min-width to @phone-max)

@hybrid-max; // "Hybrid" responsive screen category maximum width
@hybrid-up; // Media query targetting "hybrid" screens and larger
@hybrid-only; // Media query targetting "hybrid" screens only (from @phone-max + 1 to @hybrid-max)
@hybrid-down; // Media query targetting "hybrid" screens and narrower

@tablet-max; // "Tablet" responsive screen category maximum width
@tablet-up; // Media query targetting "tablet" screens and larger
@tablet-only; // Media query targetting "tablet" screens only (from @hybrid-max + 1 to @tablet-max)
@tablet-down; // Media query targetting "tablet" screens and narrower

@desktop-max; // "Desktop" responsive screen category maximum width
@desktop-up; // Media query targetting "desktop" screens and larger
@desktop-only; // Media query targetting "desktop" screens only (from @tablet-max + 1 to @desktop-max)
@desktop-down; // Media query targetting "desktop" screens and narrower

@large-only; // Media query targetting "large" screens only (from @desktop-max + 1 to infinity)

@hi-density; // Minimum pixel density ratio to be considered "high"
@retina; // Media query targetting "high density" screens
```

You can thus write media queries like `@media screen and @large-only, @retina`.

#### Mixins

The following mixins are available :

```less
.appearance(@value); // shortcut for appearance CSS rule with and without prefixes (not handled by Autoprefixer)
.hide-responsive(@query); // apply display: none; to the element when the media query is truthy
.container(); // makes the element a centered block with a width of @max-width
.mono-height(@height); // sets height and line-height to given value, useful for vertical centering
.text-size-adjust(@value); // shortcut for text-size-adjust CSS rule with and without prefixes (not handled by Autoprefixer)
```

### JavaScript

You `app/assets/js` directory should contain a `main.js` file, which will be the entry point of your front-end code. It can make use of other files through the `require` method, as long as these files expose a `module.exports` property, according to the CommonJS module definition.

Everything will then be compiled into a single `public/assets/js/main.js` file by gulp.

You may use the provided [domqueryall](https://github.com/timmak/domqueryall) to get an array instead of a `NodeList` when querying the DOM for multiple elements :

```js
(function ($, $$) {
    'use strict';

    // $('#one-element');
    // $$('.multiple-elements');
})
(document.querySelector.bind(document), require('domqueryall'));
```

### Images

Images are copied from `app/assets/img` to `public/img` by gulp, and minified when it is ran in production mode.

### Fonts

TTF fonts are copied from `app/assets/font` to `public/font` and declined in EOT and WOFF formats by gulp.

Webfont handling is helped by Patchwork's LESS mixins :

```less
// fonts.less
// Automatically declare the font with multiple file formats (here, /bebasneue.(ttf|eot|woff)/)
@font-face {
    .font-face(BebasNeue, bebasneue);
}

// You can use such mixins if you want to reset font-weight and font-style at the same time
// to avoid display issues on some browsers
.bebas() {
    .font-reset(BebasNeue);
}
```

### Back-office

In the back-office, Patchwork relies on Twitter's Bootstrap for building CRUD interfaces.

[Bootstrap manual](http://getbootstrap.com/getting-started/)

### Third-party packages

Adding new packages to your application is as simple as running `npm install [package] --save-dev`. You will always use this option since production JS code will always consist of files of your own, where third-party code is compiled within, and will thus never deploy such code "as-is".

You can also install a package from its GitHub repository if it not available on NPM (e.g. Bootstrap), through [napa](https://github.com/shama/napa).

Extra [gulp plugins](http://gulpjs.com/plugins/) may be installed as well, in order to enhance further the front-end build process by editing `gulpfile.js`.

[gulp manual](https://github.com/gulpjs/gulp/blob/master/docs/README.md)

## Testing

### Unit

PHPUnit classes are to be located in `app/tests/unit` and to wear the `[App]\Tests' namespace. You can then simply run `phpunit` at the application's root to play your tests.

PHPUnit's configuration is done through the `phpunit.xml` file.

[PHPUnit manual](https://phpunit.de/manual/current/en/phpunit-book.html)

### Functional

Behat features are to be located in `app/tests/functional`, and context classes go in `bootstrap`, which is a subdirectory of the latter. A sample context class is already provided and extends `Neemzy\Patchwork\Tests\FeatureContext`, which adds some vocabulary to Mink.

[Behat manual](http://docs.behat.org/en/latest/)
[Mink manual](http://mink.behat.org/)

## Deployment

How to will deploy your app to production is up to you. If you run `composer install` on your production server, make sure to use the `--no-dev` option to avoid installing development dependencies such as test tools.

### Assets

You are not supposed to compile your assets on your production server. It is not his role to worry about that. This is why, in its basic setup, Patchwork doesn't `.gitignore` the `public/assets` folder. You can then version production-ready (compiled and minified) assets to have them deployed instead (if you version-control compiled assets anyway, you may as well only commit these).

If you use continuous integration (better), you can safely `.gitignore` `public/assets` and have production-ready asset compilation be handled by your build.

## Credits

Written by [neemzy](http://www.zaibatsu.fr) since 2012.

You may check out the following PHP packages of mine, which are used in Patchwork :

- [patchwork-core](https://packagist.org/packages/neemzy/patchwork-core) : Core files for Patchwork
- [environ](https://packagist.org/packages/neemzy/environ) : Lightweight environment manager
- [environ-service-provider](https://packagist.org/packages/neemzy/environ-service-provider) : Environ service provider for Silex micro-framework
- [redbean-service-provider](https://packagist.org/packages/neemzy/redbean-service-provider) : RedBean ORM service provider for Silex micro-framework
- [share-extension](https://packagist.org/packages/neemzy/share-extension) : Twig extension providing social share links

Contributions and pull requests are very welcome :)