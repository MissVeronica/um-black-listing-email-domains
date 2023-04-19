# UM black listing email domains
Extension to Ultimate Member for additional blocking possibilities like subdomains and top level domains and disposable email domains.

## Updates
Version 2.0.0 addition of disposable email domains

## Settings 
UM Settings -> Access -> Other -> Blocked Email Addresses (Enter one email per line)

In addtion to UM blocking options this plugin will add:

1. Top level blocking: *.xyz
2. Subdomain blocking: *.somedomain.com

Subdomain blocking will allow registration for @somedomain.com but block @subdomain.somedomain.com

## Disposable email domains
1. Get a disposable emails list in text format one domain per line from your preferred source.
2. Example of a public disposable emails list: https://disposable-emails.github.io/
3. Create a new folder in your UM uploads path .../wp-content/uploads/ultimatemember/disposable_emails
4. Upload your disposable emails list file to this new folder and use the file name: disposable_emails_list.txt

## Error messages
1. We do not accept registrations from this top level email domain.
2. We do not accept registrations from this email subdomain.
3. We do not accept registrations from this temporary email domain.

## Translations or Text changes
1. Use the "Say What?" plugin with text domain ultimate-member
2. https://wordpress.org/plugins/say-what/

## Installation
Install by downloading the ZIP file and install as a new Plugin, which you upload in WordPress -> Plugins -> Add New -> Upload Plugin.

Activate the Plugin: Ultimate Member - Blocked Email Domains
