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
  body {
    dir: rtl;
  }
  div.mainmenu.reportmain::before {
    content: "\f201";
  }
  ```
- need to deploy the "report" folder to public_html/report


# Changes to the system:
## defaults [relative-url, field, value]
- default for create service "not for purchase" [product/card.php?leftmenu=service&action=create&type=1, statut_buy, 0]

## Product module config (press on module's cog icon)
- Base of prices per default (with versus without tax) when adding new sale prices: Inc. Tax
- שדות נוספים:
  - איזור גאוגרפי, type:select, values: 1,צפון\n ...

## EMAIL setup
- in cPanel :
  - create a mail account for the site (e.g. accounting@aloneandella-dev.site) with strong pwd
  - press the "connect devices" and find out the host/port of SMTP
- in Dolibar: settings->Email: email sending method:
  - Email sending methof: SMTP
  - set host/port/user (accounting@alonandella...)/ pwd
  - set USE TLS=true

## Connect Git
 - using jobs define every 1 hour (at min 20):
   `cd /home/alonand1/repositories/dolibarr && git reset --hard HEAD && git pull`
 - using jobs define every 1 hour (at min 30):
   `rsync -av --update /home/alonand1/repositories/dolibarr/htdocs/ /home/alonand1/public_html/`

## Translations:
- in table llx_c_currencies, change ILS to שקל