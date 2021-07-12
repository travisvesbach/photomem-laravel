# PhotoMem
This is a rebuild of my original version that was done with Ruby using Hanami.  This rebuild is done with PHP using Laravel.

### Lando
After lando rebuild, need to manually instal python3-pip:
- lando ssh --user root
- apt-get update
- apt-get install python3-pip -y
- python3 -m pip install pillow


### Docker Compose
current setup uses sqlite
- clone repo
- run `docker-compose up --build -d`
- create db: `touch database/photomem.sqlite`
- edit `.env` file with sqlite credentials and queue_connection
```
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/html/database/photomem.sqlite
DB_FOREIGN_KEYS=true

QUEUE_CONNECTION=database
```
- inside the container as the `www-data` user, run `composer install --no-dev` and `php artisan migrate`.
- if mounting outside volume for syncing images, it needs to be mounted to both the main laravel container and the php-queue container


### Images
images to be synced should be in public/storage/sync


### Notes
Intervention Image used for photo syncing: http://image.intervention.io/api/save
