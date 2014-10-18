# Patchwork

**Patchwork** is a **PHP 5.4+ full-stack web framework**, which main purpose is to gather the best tools available together in order to provide a minimalist yet complete start point for building small to medium **web applications**.

## Round table

### Package management

- [Composer]() : the now standard package manager for PHP.

- [NPM]() : node.js's package manager, which is used to install front-end dependencies along with Napa.

### Back-end

- [Silex]() : a micro-framework built around Symfony components, which provides a dependency injection container and a basic web application structure. It comes in Patchwork along with multiple service providers.

- [Twig]() : Symfony's *de facto* templating engine, used by default in Silex. It comes in Patchwork along with multiple extensions.

- [Swift Mailer]() : Symfony's *de facto* mail sender, used by default in Silex.

- [RedBean]() : an ORM which particularity is that it creates your database schema itself as you write code. Its integration with Silex is handled by a dedicated service provider, in order to make it available as an instance rather than a static facade.

- [Monolog]() : the standard logger for PHP.

- [Environ]() : an environment manager which allows defining environments triggered by a boolean callback (typically based on hostname detection) and executing environment-specific code.

- [Image Workshop]() : an image library used for resizing and cropping images.

- [PHPUnit]() : one of the major unit tests tools for PHP.

### Front-end

- [gulp]() : a task runner for asset processing and optimization, which allows for live compilation and reloading during development. Along with the libraries listed below, it makes use of [JSHint]() and image minification and font conversion utilities.

- [LESS]() : a CSS preprocessor.

- [Browserify]() : a front-end JavaScript dependency manager, which allows using CommonJS's `require` method to help keep a good code structure.

- [Behat]() and [Mink]() : functional tests tools, that can pilot a browser to use your app. Running JavaScript-capable tests will require the use of [Selenium]() and [PhantomJS]().

## Structure and installation

All code that could possibly handle inheritance/dispatching in one way or another (which includes PHP classes, basic LESS stylesheets and [NicEdit](), a JavaScript WYSIWYG editor) is contained into the framework's **core**, available as a separate package. The framework's repository itself is mainly a sample app - about pizzas - designed to help you getting started quickly.

The best way to start is to use Composer, which will clone that repository and install all required dependencies (make sure Composer and NPM are installed first) :

```
composer create-project neemzy/patchwork pizza
```

You then have to check a few steps :

- The `var` folder and its subdirectories should be writable by your web server's user.
- Your web server/virtual host should be configured right. A sample `.htaccess` file is provided in the `public` directory, if by any chance you use [Apache]().
- For development, Gulp should be installed globally (`npm install -g gulp`).
- For development as well, [SQLite]() and the corresponding PHP extension should be installed (`sudo apt-get install sqlite php5-sqlite` on Debian and Ubuntu).

(configuration)

Finally, run `gulp` to have your browser open at the URL you have chosen above and a livereload server started, and start coding !

## Directory structure

(coming soon)

## Back-end development

### Views

(coming soon)

### Models

(coming soon)

### Controllers

(coming soon)

## Front-end development

### Stylesheets

(coming soon)

### JavaScript

## Testing

(coming soon)