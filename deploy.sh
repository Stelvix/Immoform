#!/bin/sh  
echo "### Synchronisation des fichiers RSYNC ###"  
rsync  -e 'ssh' -S -av ./ Steeven@172.16.126.1:/var/www/ImmoformSymfony  --include="public/.htaccess" --include=".env"  --include=".env.prod.local" --exclude-from=".gitignore" --exclude=".*"  
echo "### CONNECTION SSH ###"  
ssh Steeven@172.16.126.1 -o "StrictHostKeyChecking=no" <<'eof'  
echo "### Déplacement dans le dossier web ###"  
cd /var/www/ImmoformSymfony
echo "### RENOMMAGE .env.local ###"  
mv .env.prod.local .env.local  
echo "### COMPOSER INSTALL ###"  
composer install  
echo "### DOCTRINE MIGRATION ###"  
symfony console --no-interaction doctrine:database:create --if-not-exists  
symfony console --no-interaction doctrine:migrations:migrate
