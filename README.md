# Craft CMS User Log

This plugin will log user logins with their IP address and location.

## Location Lookup

This plugin currently only supports ipstack.com for the location lookup with the users IP address.
This service is free for up to 100 requests per month.

## Cronjob

Add the following console command to a daily cronjob to automatically
clear the logs after X days:

    ./craft userlog/clean