.xlsx file import
===============

This is a simple app developer with symfony version 7 and AngularJs which allows you to import an .xlsx file

Once the file is imported, you can view the content of the file and can manage or edit imported data.

Date is stored into database

Installation
----------------

- Clone the repository on your local environment
- run `composer install`
- run `php bin/console doctrine:migrations:migrate`
- open file located in: /public/angular/app.js - and change the `var URL` to your localhost path
- Download the [b]test.xlsx[/b] file and use it foe upload, app is hardcoded to work only with this file
