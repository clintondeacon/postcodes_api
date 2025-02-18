# Postcodes/stores API example

A simple app built with Laravel for showing postcodes and stores

- Downloads all postcodes from http://parlvid.mysociety.org/os/ and stores data to POstcode model using a command.
- Creates stores at random locations
- Shows all stores within a specific distance of a postcode
- Show all postcodes within a specific distance of a store
- Unit tests for API endpoints

### A few notes/comments

- Data is output using resources and collection. This could possible have been done using read-only DTO classes which can be quicker.
- Tests could've gone into a little more detail like using assertExactJson to check the specific data.

## Installation

1. Clone repository


2. Copy .env.example to.env with the following command

```bash
cp .env.example .env
```


3. Generate the application key
```bash
php artisan key:generate
```


4. Generate the database

```bash
php artisan migrate:fresh --seed
```


5. Start the Laravel server:

```bash
php artisan serve
```


6. Run the tests

```bash
php artisan test
```
