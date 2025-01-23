# ODT

Drupal ODT project.

## Requirements

- [Lando](https://lando.dev/) (optional)
- Drupal 11.1
- Apache 2.4 (or Nginx)
- PHP 8.3
- Mysql 8.0 (or MariaDB)
- Mailhog

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
# Get site URLs
lando info --format=table
# Login
lando drush user:login --uri=https://odt.lndo.site/
```
## Features
- [x] Installable site from config, please follow Installation and Usage steps
- [x] Content generation, most of the content should be generated after Usage steps
- [x] Navigation menu https://odt.lndo.site/admin/structure/menu/manage/main
- [x] Banners https://odt.lndo.site/admin/content/block
- [x] List of products https://odt.lndo.site/admin/content/product
- [x] Product of the day queue (please fill the queue) https://odt.lndo.site/admin/structure/entityqueue/product_featured/product_featured
- [x] Product of the day view block (random) https://odt.lndo.site/admin/structure/views/view/product_featured
- [x] Product of the day block instance https://odt.lndo.site/admin/structure/block/manage/olivero_views_block__product_featured_block_1
- [ ] Product of the day CTA clicks statistics
- [x] Webform used to send Product summary notification https://odt.lndo.site/admin/structure/webform/manage/product_featured_notification
- [x] Cron ob for Product summary notification https://odt.lndo.site/admin/config/system/cron/jobs/manage/product_featured_notification
- [x] SMTP settings https://odt.lndo.site/admin/config/system/smtp
- [x] Email testing tool http://mailhog.lndo.site:8080/
