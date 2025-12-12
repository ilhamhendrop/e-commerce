# e-commerce
Test Back End Ilham Hendro Prasetyo

# cara pakai
- git clone
- replace .env.production menjadi .env isikan semua
- masuk ke file src
- replace .env menjadi .env isikan yang sama dengan .env docker
- jika sudah 
    - jalankan docker compose up -d
    - jalankan docker compose exec app composer install
    - jalankan docker compose exec app php artisan key:generate
    - jalankan docker compose exec app php artisan migrate --seed
    - jalankan docker compose exec app php artisan storage:link

