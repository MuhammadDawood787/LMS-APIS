1. Clone the repository: git clone https://github.com/MuhammadDawood787/LMS-APIS.git
2. composer install
3. Create a copy of the .env.example file and rename it to .env. Update the database configuration in the .env file
4. php artisan key:generate
5. php artisan migrate
6. php artisan db:seed (I use sanctum for Auth and I register a user using seeder so when you access api u should use token which you will get after login and apis collection are there in project with the name "LMS-API's.postman_collection.json" just import it into postman you can access apis)
7. php artisan serve





