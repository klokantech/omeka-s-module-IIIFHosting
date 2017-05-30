# omeka-s-module-IIIF

With this modele Omeka-S will submit copies of the uploaded images to the dedicated hosting storage where these are going to be automatically converted to JPEG2000 and exposed via IIIF. So these images will be available in a fullscreen zoomable viewer and in variable pixel size via a responsive image service.
This module also provides IIIF manifest on URL `/<item_id>/manifest.json` so the items can be easily displayed and used in other tools such as Mirador, UniversalViewer or Georeferencer.

## Install

Copy this folder to your Omeka instalation to folder "modules" and rename it as "IIIFHosting". Install "IIIFHosting" module in "modules" administration and provide your customer email which is registered in IIIFHosting.com service (there is a free plan). From IIIFHosting.com administration section "Ingest API" copy secure payload to this module configuration. Check Webhook configuration on IIIFHosting.com if it points to your Omeka installation URL + `/iiif_ingest`. It is created during Omeka module configuration.


## Local development with docker-compose

Just clone this folder and run "docker-compose up" on it. Configure module and theme as
descripted above.

Omeka will run on localhost port 80. There is also phpmyadmin on port 8080, password is in docker-compose.yml
