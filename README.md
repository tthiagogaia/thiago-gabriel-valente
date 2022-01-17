# Users import
This repository contains the code for the proposed challenge. Which is capable of importing the data from .json and .xml files into the database.

## Stack

This project is based on the following technologies:

**Laravel 8** + **PHP 8** + **REDIS** + **MySQL**

## Setup

### 1. Starting the environment

**1.1** In the root of the **thiago-gabriel-valente** project create the **.env** file:
```bash
cp .env.example .env
```

**Note:** In the **.env** file enter your REDIS and DATABASE credentials.

**1.2** Install the project packages:
```bash
composer install
```

**1.3** Generate the project **APP_KEY**:
```bash
php artisan key:generate
```

**1.4** Run the migrations:

```bash
php artisan migrate
```

## How to use
**1.** Start the queue and the worker
```bash
php artisan queue:work
```
Or you can use the horizon
```bash
php artisan horizon
```
**2.** Execute the **import** command:
```bash
php artisan import:users [filePath]
```
**Note 1:** The *filePath* argument is optional. If you don't pass the *filePath* argument, the default file path is */challenge.json*.  
**Note 2:** The *filePath* argument is the absolute path to the file that will be imported.  
**Note 3:** You can import .json or .xml files with the same content.

## Extras

###1. Unit and Feature tests

The entire project was developed following TDD. Then you can run the project tests

```bash
php artisan test
```
