## Test assignment for [AlifTech](https://aliftech.uz)


## Installation

#### Prerequisites:
- [Docker Engine](https://docs.docker.com/engine)
- [Docker Compose](https://docs.docker.com/compose)

1. Copy contents of **.env.example** to **.env** file.
   You can change values if you want.
```
cp .env.example .env
```
2. Build and Up **Docker** containers.
```
docker compose up --build -d
```
3. Install **Composer** dependencies.
```
docker compose exec app composer install
```
4. Run **migrations** and **seeders**.
```
docker compose exec app php artisan migrate --seed
```
Note that the **Elasticsearch** container starts up slower than the others, which can cause **seeders** to fail (All mock data must be added to **Elasticsearch** indices).      
If **seeders** fail, try again after a couple of seconds.    

**You can now access the application at http://localhost**.
