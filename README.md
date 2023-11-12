# shiny-palm-tree
Backend - Patient Registration

## Instalacion
### Construir docker
    sudo docker-compose build

### Ejecutar docker
    sudo docker-compose up -d

o

    sail ./vendor/bin/sail up -d
### Crear migraciones
    sudo docker exec -it shiny-palm-tree_laravel.test_1 php artisan migrate

## Adicional
En la raiz del documento se encuentra el postman para probar el proyecto. Lo unico que hay que agregar es la foto para el documento.