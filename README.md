# UM Black listing email domains
Extension to Ultimate Member for additional blocking possibilities like subdomains and top level domains and online updates of disposable email domains.

## UM Settings -> Access -> Other -> Blocked Email Addresses (Enter one email per line)
1. UM blocking formats: <code>user@block.com</code> <code>*@block.com</code> 
2. In addtion to UM blocking options this plugin will support:
3. Top level blocking: <code>*.xyz</code>
4. Subdomain blocking: <code>*.somedomain.com</code>
5. Subdomain blocking will allow registration for <code>@somedomain.com</code> but block <code>@subdomain.somedomain.com</code>

## Disposable email domains
1. Removal from version 3.0.0 of the old admin supplied list of uploaded disposable email domains
2. Online download of "List of well-known email domains" and "List of known e-mail domains used disposable email services" from GitHub
3. Source downloads: https://github.com/amieiro/disposable-email-domains
4. Current sizes of the download files are 919 and 172813 email addresses and updates are generated every quarter of an hour

## User registration error messages
1. We do not accept registrations from this top level email domain.
2. We do not accept registrations from this email subdomain.
3. We do not accept registrations from this temporary email domain.

## Translations or Text changes
1. Use the "Say What?" plugin with text domain ultimate-member
2. https://wordpress.org/plugins/say-what/

## Updates
1. Version 2.0.0 Admin user addition of disposable email domains
2. Version 3.0.0 Addition of online update of disposable email domains

## Installation & Updates
1. Install or update by downloading the ZIP file via the green Code button.
2. Install as a new Plugin, which you upload in WordPress -> Plugins -> Add New -> Upload Plugin.
3. Activate the Plugin: Ultimate Member - Black listing email domains
