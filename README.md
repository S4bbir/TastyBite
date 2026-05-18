# TastyBite

## Folder Structure

text
TastyByte/
|-- food_page/
|   |-- controllers/  request handlers and JSON endpoints
|   |-- core/         base model/controller classes
|   |-- models/       PDO models for shared schema tables
|   `-- views/        PHP templates
|-- config/           database, bootstrap, helpers
|-- public/
|   |-- css/          styles
|   |-- js/           JavaScript validation and AJAX
|   |-- uploads/      profile and menu uploads
|   `-- index.php     front controller
`-- database/         schema.sql


## Setup With XAMPP

1. Copy TastyBite into xampp/htdocs/.
2. Start Apache and MySQL
3. Import database/schema.sql