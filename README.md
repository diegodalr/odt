# ODT

Drupal ODT project.

## Requirements

- [Lando](https://lando.dev/) (optional)
- Drupal 11.1
- Apache 2.4 (or Nginx)
- PHP 8.3
- Mysql 8.0 (or MariaDB)

## Installation

This is a composer managed project and it's ready to work with Lando.

```
lando start
lando composer install
```

## Usage
This Drupal project uses CM and the Minimal profile.
```
# Install the project
lando drush site:install minimal --config-dir=../config/sync --db-url=mysql://drupal11:drupal11@database/drupal11 -y
# Login
lando drush user:login --uri=https://odt.lndo.site/
```
## Features
- Navigation menu https://odt.lndo.site/admin/structure/menu/manage/main
- Banners https://odt.lndo.site/admin/content/block
- List of products https://odt.lndo.site/admin/content/product
- Product of the day queue https://odt.lndo.site/admin/structure/entityqueue/product_featured/product_featured
- Product of the day view block https://odt.lndo.site/admin/structure/views/view/product_featured
