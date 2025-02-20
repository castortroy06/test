1. clone repository.
2. go to the main repo and run docker-compose up -d, wait until images will be loaded.
3. run docker ps command and find id of test-php container like 657bef313401
4. run docker exec -it 757bef613400 /bin/bash to enter php container
5. go to /var/www/html/test and run php yii migrate, apply migration
6. launch REST requests
7. spent time: 7h