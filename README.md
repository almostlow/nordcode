Requirements: PHP7.4, MySQL>=5.6

# How to launch
From root project directory  

``composer install``  
``php bin/console doctrine:database:create``  
``php bin/console doctrine:migrations:migrate``  

For faster performance cache warmer can be ran  
``php bin/console cache/warmup``  

This command loads feed to cache for 5 minutes.

Run server  
``php -S 127.0.0.1:8000 -t public/``
