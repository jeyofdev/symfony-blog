# Personal project of a blog with Symfony


&nbsp;
&nbsp;


## Tools

Check that the latest version of [Nodejs](https://nodejs.org/en/download/) is installed :
```sh
$ node -v
```

Check that the latest version of [Yarn](https://yarnpkg.com/en/docs/install) is installed :
```sh
$ yarn -v
```

Check that the latest version of [Composer](https://getcomposer.org/download/) is installed :
```sh
$ composer -v
```


&nbsp;
&nbsp;


## Install the necessary dependencies

```sh
$ composer install
$ yarn install
```


&nbsp;
&nbsp;


## Generate assets with [@symfony/webpack-encore](https://symfony.com/doc/current/frontend.html)

```sh
$ yarn encore dev --watch
```


&nbsp;
&nbsp;


## Run the symfony dev server

```sh
$ symfony server:start
```


&nbsp;
&nbsp;


## Initialize the database

### Create the database
```sh
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migration:migrate          
```

### Add the fixtures
```sh
$ php bin/console doctrine:fixtures:load          
```