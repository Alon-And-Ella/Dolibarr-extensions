# overview
this repo is for code we write which should be delployed to the Dolibarr installation

# Reports
- Adding hand-crafted report based on sql-query
- Manual adding of menus to call the report:
  - Home -> הגדרות
                  -> תפריטים
  - לשונית עורך התפריט
  - add a menu pointing to report/main.php ("Menu entry code":reportmain)
  - add a sub-menu pointing to report/menHoursByProduct.php
  - to have an icon for the menu at the top menu, a CSS fragment needs to be added:
  ```css
  div.mainmenu.reportmain::before {
    content: "\f201";
  }
  ```
- need to deploy the "report" folder to public_html/report

## reports based on PHPReport.php (https://github.com/vernes/PHPReport)
- we need the package manager 'composer' to be able to install its dependecies (phpoffice):
  - with no ssh to the machine, follow this:
    - deploy the `./report/webroot/composerExtractor.php`
    - download `Composer.phar` - make sure it has same case as here (the code is case sensitive)
    - upload it also into webroot
    - upload the report/composer.json
    - run the code `/report/webroot/composerExtractor.php`
    - that is it. for better security - remove this file from the server.
  - upload the xlsx templates and other php who use PHPReport - they would now work

# Changes to the system:
## defaults [relative-url, field, value]
- default for create service "not for purchase" [product/card.php?leftmenu=service&action=create&type=1, statut_buy, 0]

## Product module config (press on module's cog icon)
- Base of prices per default (with versus without tax) when adding new sale prices: Inc. Tax
- שדות נוספים:
  - איזור גאוגרפי, type:select, values: 1,צפון\n ...
  - מקסימום משתתפים
  - איש קשר
  - טלפון איש קשר

## Company settings:
- מס מכירה לא פעיל

## Vendor module
- Use a 3 steps approval when amount (without tax) is higher than... - put "1"

## EMAIL setup
- in cPanel :
  - create a mail account for the site (e.g. accounting@aloneandella-dev.site) with strong pwd
  - press the "connect devices" and find out the host/port of SMTP
- in Dolibar: settings->Email: email sending method:
  - Email sending methof: SMTP
  - set host/port/user (accounting@alonandella...)/ pwd
  - set USE TLS=true
- Add an SPF record: in the DNS settings, add TXT type record Name: `@` and Value: `v=spf1 ip4:195.201.169.229 include:alonandella-dev.site ~all`

## Connect Git
 - using jobs define every 1 hour (at min 20):
   `cd /home/alonand1/repositories/dolibarr && git reset --hard HEAD && git pull`
 - using jobs define every 1 hour (at min 30):
   `rsync -av -I --update /home/alonand1/repositories/dolibarr/htdocs/ /home/alonand1/public_html/`

## Translations:
- settings->dictionaries->מטבעות
  - change ILS to שקל

## UI right to left
- in global settings: MAIN_CHECKBOX_LEFT_COLUMN=1
- a CSS fragment needs to be added:
  ```css
  body {
    dir: rtl;
  }
  ```

## ref generated for services
- in global settings: PRODUCT_GENERATE_REF_AFTER_FORM=1
- in Product Module settings=> enable "Elephant" and set mask to {000000}

## Events
- settings->dictionaries->סוגי אירועי יומן
  - disable anything not relevant
  - add a new type for volenteering


## Global Settings:
- PROJECT_ALLOW_TO_LINK_FROM_OTHER_COMPANY=all