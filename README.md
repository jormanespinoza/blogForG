# blogForG

## Lenguajes aplicados

* PHP
* CSS
* HTML
* Javascript
* jQuery
* AJAX

## Frameworks
* Symfony 4
* Boostrap 4

## Módulos Principales

### Composer
* WebserverBundle (correr servidor local)
* VichUploaderBundle (carga de archivos)
* PrestaImageBundle (crop de imágenes)
* DoctrineFixturesBundles (carga de contenido )
* Symfony/Translation (traducir mensajes de error a español)
* Webpace Encore (compilar assets)
* Knplabs/knp-time-bundle (manejo de fechas)

### Npm
* CKEditor5
* Cropper.js
* speakingurl.js
* copy-webpack-plugin
* node-sass
* sass-loader

## Instalación
* Clonar repositorio de Github git clone https://github.com/jormanespinoza/blogForG.git
* Instalar/correr composer desde la terminal `php bin/console composer.phar install`
* Una vez instalado, ubicar archivo .env descomentar la siguiente línea:
`#DATABASE_URL=mysql://db_user:db_password@127.0.0.1:3306/db_name?serverVersion=5.7`
* Reemplazar con el nombre de usuario y la contraseña con uno propio, y reemplazar el nombre de la base de datos, también ajustar según la versión del server de phpMyAdmin
* Una vez hecho esto se procede a generar la base de datos con el siguiente comando:
`php bin/console doctrine:database:create`
* Con la base de datos generara se de procede a efectuar las migraciones para el armado de las tablas:
`php bin/console make:migration`
* Luego se ejecutan las migraciones con este comando:
`php bin/console doctrine:migrations:migrate`
* Presiona Y/y cuando se lo indique en la terminal para confirmar
* Los fixtures están implementados por lo que corriendo:
 `php bin/console doctrine:fixtures:load`
* Aparecerá otro mensaje de la terminal solicitando confirmación ingresar 'yes' esto generá algunos usuario de prueba, publicaciones y comentarios
* La ruta de de esto ultimo se encuentra en ./src/DataFixtures/BlogFixtures.php
* Ahora sería generar los assets mediante:
`php bin/console assets:install`
* Y luego correr `npm install`
* Una vez se instale correr lo siguiente: npm run dev esto compilará los assets en mode desarrollo también npm run build y npm run watch
* Una vez compilados los assets mediante el WebServerBundle:
`php bin/console server:start`) se habilitará un servidor local en el puerto 8000 `http://localhost:8000/`
* Esto ya levantará el blog en modo desarrollo para recorrer las vistas
* Para pasar la web a modo producción bastará con ubicar nuevamente el archivo .env y cambiar APP_ENV=dev por APP_ENV=prod (sería conveniente compilar nuevamente los assets `php bin/console assets:install`)
* Proceso completa

## Algunas vistas

* Login
![alt text](https://raw.githubusercontent.com/jormanespinoza/blogForG/master/assets/images/login.png)

* Header
![alt text](https://raw.githubusercontent.com/jormanespinoza/blogForG/master/assets/images/header.png)

* Posts
![alt text](https://raw.githubusercontent.com/jormanespinoza/blogForG/master/assets/images/posts.png)

* Comments
![alt text](https://github.com/jormanespinoza/blogForG/blob/master/assets/images/comments.png)

* Management
![alt text](https://github.com/jormanespinoza/blogForG/blob/master/assets/images/manage_posts.png)
