.xlsx file import
===============

This is a simple app developer with symfony version 7 and AngularJs which allows you to import an .xlsx file

Once the file is imported, you can view the content of the file and can manage or edit imported data.

Date is stored into database

Installation
----------------

- Clone the repository on your local environment
- Enter the new created app folder
- run `composer install`
- run `php bin/console doctrine:migrations:migrate`
- run `symfony server:start`
- open file located in: /public/angular/app.js - and change the `var URL` to your localhost path
- Download the `test.xlsx` file and use it for upload, app is hardcoded to work only with this file
