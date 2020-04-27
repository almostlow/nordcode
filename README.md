Requirements: PHP7.4, MySQL>=5.6

# How to launch
From root project directory  
``composer install``  
``php bin/console doctrine:database:create``  
``php bin/console doctrine:migrations:migrate``  
Run server  
``php -S 127.0.0.1:8000 -t public/``
