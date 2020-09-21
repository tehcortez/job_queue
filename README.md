REST Api Job Queue Application implementing RabbitMQ
======================================

## Introduction

This is a REST Api Job Queue Application application using the Laminas(Zend Framework) MVC layer and module
systems. This application was designed to test my knowledge of web technologies and assess my ability to
create robust PHP web applications with attention to software architecture​ and security.

It sends shell commands to your linux through PHP. It creates a queue of commands that can be monitored.

Requirements
------------

Please see the [composer.json](composer.json) file.

Installation
------------
### Git (clone)

First, clone the repository:

```bash
# git clone https://github.com/laminas-api-tools/api-tools-skeleton.git # optionally, specify the directory in which to clone
$ cd path/to/install
```

### Composer

At this point, you need to use [Composer](https://getcomposer.org/) to install
dependencies. Assuming you already have Composer:

```bash
$ composer install
```

###Installing RabbitMQ

Make sure the file RabbitMQ.sh on root directory is with execution privilege:
```bash
$ chmod +x ./RabbitMQ.sh
```

At this point, you need to use [Docker](https://docs.docker.com/engine/install/) to install RabbitMQ. Assuming you already have Docker, there's a bash script to help your way:
```bash
$ sudo ./RabbitMQ.sh
```

### Importing MySQL database

We need to create a new database. This is where the contents of the 'db/db.sql' file will be imported.

First, log in to the database as root or another user with sufficient privileges to create new databases:
```bash
$ mysql -u root -p
```

This will bring you into the MySQL shell prompt. 
Next, create a new database with the following command. In this example, the new database is called exchange:
```bash
CREATE DATABASE exchange;
```
You’ll see this output confirming that it was created.

```output
Query OK, 1 row affected (0.00 sec)
```
Then exit the MySQL shell by pressing CTRL+D. 
From the regular command line, you can import the dump file with the following command:
```bash
$ mysql -u username -p newdatabase < path/to/chatbot/db/db.sql
```
username: is the username you can log in to the database with
newdatabase: is the name of the freshly created database
db.sql: is the data dump file to be imported, located in the current directory


### Environment-specific application configuration

Now we need to setup 'config/autoload/global.php' (or 'config/autoload/local.php', recommended) file database data:

'database' => 'jobqueue', #mysql database name;
'hostname' => '127.0.0.1', #host address to access your mysql server
'username' => 'root', #username to access your Mysql database
'password' => 'myPassword', #password to access your Mysql database

and RabbitMQ data:

"RABBITMQ_HOST" => "127.0.0.1", #host address to access your RabbitMQ server
"RABBITMQ_PORT" => 5672, #port address to access your RabbitMQ server
"RABBITMQ_USERNAME" => "root", #username to access your RabbitMQ server
"RABBITMQ_PASSWORD" => "myPassword", #password to access your RabbitMQ server
"RABBITMQ_QUEUE_NAME" => "job_queue" #name of your queue

### All set

Once you have the basic installation, you need to do one of the following:

- Create a vhost in your web server that points the DocumentRoot to the
  `public/` directory of the project
- Fire up the built-in web server in PHP(**note**: do not use this for
  production!)

In the latter case, do the following:

```bash
$ cd path/to/install
$ php -S 0.0.0.0:8080 -ddisplay_errors=0 -t public public/index.php
# OR use the composer alias:
$ composer serve
```

You can then visit the site at http://localhost:8080/ - which will bring up a
welcome page and the ability to visit the dashboard in order to create and
inspect your APIs.

If you click "Documentation" button, and click "Ver. 1" afterwards, you will get all documentation you need for interacting with the API.

There is a Postman file for helping with your API tests.

After you get enough jobs on your app, it's time to work the queue. You just have to execute the command:
```bash
$ php worker.php NAME_OF_WORKER
```
changing NAME_OF_WORKER by the name of the server that you are starting.
It's a nice idea to have a different name for each server, to keep control of which server is running which job.

You can then visit the RabbitMQ admin page at http://localhost:15672/ . There you can access lots of informations about your queues, including current average processing time.

### NOTE ABOUT USING APACHE

Apache forbids the character sequences `%2F` and `%5C` in URI paths. However, the Laminas API Tools Admin
API uses these characters for a number of service endpoints. As such, if you wish to use the
Admin UI and/or Admin API with Apache, you will need to configure your Apache vhost/project to
allow encoded slashes:

```apacheconf
AllowEncodedSlashes On
```

This change will need to be made in your server's vhost file (it cannot be added to `.htaccess`).

### NOTE ABOUT OPCACHE

**Disable all opcode caches when running the admin!**

The Laminas API Tools Admin cannot and will not run correctly when an opcode cache, such as APC or
OpCache, is enabled. Laminas API Tools does not use a database to store configuration;
instead, it uses PHP configuration files. Opcode caches will cache these files
on first load, leading to inconsistencies as you write to them, and will
typically lead to a state where the admin API and code become unusable.

The admin is a **development** tool, and intended for use a development
environment. As such, you should likely disable opcode caching, regardless.

When you are ready to deploy this API to **production**, however, you can
disable development mode with command:
```bash
composer development-disable
```
thus disabling the admin interface, and safely run an
opcode cache again. Doing so is recommended for production due to the tremendous
performance benefits opcode caches provide.

### NOTE ABOUT DISPLAY_ERRORS

The `display_errors` `php.ini` setting is useful in development to understand what warnings,
notices, and error conditions are affecting your application. However, they cause problems for APIs:
APIs are typically a specific serialization format, and error reporting is usually in either plain
text, or, with extensions like XDebug, in HTML. This breaks the response payload, making it unusable
by clients.

For this reason, we recommend disabling `display_errors` when using the Laminas API Tools admin interface.
This can be done using the `-ddisplay_errors=0` flag when using the built-in PHP web server, or you
can set it in your virtual host or server definition. If you disable it, make sure you have
reasonable error log settings in place. For the built-in PHP web server, errors will be reported in
the console itself; otherwise, ensure you have an error log file specified in your configuration.

`display_errors` should *never* be enabled in production, regardless.

### Docker

If you develop or deploy using Docker, we provide configuration for you.

Prepare your development environment using [docker compose](https://docs.docker.com/compose/install/):

```bash
$ git clone https://github.com/laminas-api-tools/api-tools-skeleton
$ cd api-tools-skeleton
$ docker-compose build
# Install dependencies via composer, if you haven't already:
$ docker-compose run api-tools composer install
# Enable development mode:
$ docker-compose run api-tools composer development-enable
```

Start the container:

```bash
$ docker-compose up
```

Access Laminas API Tools from `http://localhost:8080/` or `http://<boot2docker ip>:8080/` if on Windows or Mac.

You may also use the provided `Dockerfile` directly if desired.

Once installed, you can use the container to update dependencies:

```bash
$ docker-compose run api-tools composer update
```

Or to manipulate development mode:

```bash
$ docker-compose run api-tools composer development-enable
$ docker-compose run api-tools composer development-disable
$ docker-compose run api-tools composer development-status
```

QA Tools
--------

The skeleton ships with minimal QA tooling by default, including
laminas/laminas-test. We supply basic tests for the shipped
`Application\Controller\IndexController`.

We also ship with configuration for [phpcs](https://github.com/squizlabs/php_codesniffer).
If you wish to add this QA tool, execute the following:

```bash
$ composer require --dev squizlabs/php_codesniffer
```

We provide aliases for each of these tools in the Composer configuration:

```bash
# Run CS checks:
$ composer cs-check
# Fix CS errors:
$ composer cs-fix
# Run PHPUnit tests:
$ composer test
```


