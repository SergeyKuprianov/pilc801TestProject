1. Clone the project: git clone https://github.com/SergeyKuprianov/pilc801TestProject.git .
2. Install the vendors: composer install
3. Create the folders: sudo mkdir web/uploads && sudo mkdir web/uploads/images
4. Change the permissions: sudo chmod -R 0777 app/cache/ app/logs/ web/uploads/
5. Create the database: php app/console doctrine:database:create
6. Update the database: php app/console doctrine:schema:update --force