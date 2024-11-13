# UM Black listing email domains Version 3.1.0
Extension to Ultimate Member for additional blocking possibilities like subdomains and top level domains and online updates of disposable email domains.

https://github.com/MissVeronica/um-black-listing-email-domains/blob/main/development.md

## UM Settings -> Access -> Other -> Blocked Email Addresses (Enter one email per line)
1. UM blocking formats: <code>user@block.com</code> <code>*@block.com</code>
2. Additional settings by plugin: Top level blocking '*.xyz' and subdomain blocking '*.company.com'
3. Top level blocking: <code>*.xyz</code>
4. Subdomain blocking: <code>*.somedomain.com</code>
5. Subdomain blocking will allow registration for <code>@somedomain.com</code> but block <code>@subdomain.somedomain.com</code>

## UM Settings -> Access -> Other -> Black listing email domains
1. Number of hours to keep cached email domains - Enter the number of hours until refreshing current downloaded GitHub allowed and disposable email domains. Hours set to 0 disables caching.
2. Enable listing of Plugin current caches - Tick to create HTML pages with listings of the Plugin's current caches with allowed and disposable email domains. - Untick and Save to remove the HTML files
3. WHITE list: Additional allowed local email domains - Enter allowed local email domains (one per line) to be used together with the GitHub allowed emails during email validation. The update will be made at the next cache refresh. Example 'mydomain.com'
4. BLACK list: Additional blocked local email domains - Enter blocked local email domains (one per line) to be used together with the GitHub disposable emails during email validation. The update will be made at the next cache refresh. Example 'notdomain.com'
5. Enable UM error message for all blocked domains - Tick to use the UM "blocked_domain" error message also for all Plugin invalid email domains.

## Disposable email domains
1. Removal from version 3.0.0 of the old admin supplied list of uploaded disposable email domains
2. Online download of "List of well-known email domains" and "List of known e-mail domains used disposable email services" from GitHub
3. Source downloads: https://github.com/amieiro/disposable-email-domains
4. Current sizes of the download files are 919 and 172813 (3,7 MByte) email addresses and updates are generated every quarter of an hour
5. From Version 3.1.0 allowed and denied email domains are cached with a setting for the number of hours a cache refresh, Zero hours cache time disables caching and every user registration will download email domains from GitHub.
6. A temporary solution with listing of all cached email domains to a HTML page is available and will be replaced by a more secure solution later.
7. HTML files are located at .../wp-content/uploads/ultimatemember/disposable_emails/
8. Cached email domain download and cache read times are displayed at the settings page.

## Screen copy
https://imgur.com/a/L1YLHmd

## User registration error messages
1. We do not accept registrations from this top level email domain.
2. We do not accept registrations from this email subdomain.
3. We do not accept registrations from this temporary email domain.
4. All error messages can be replaced by the UM error message: We do not accept registrations from that domain.

## Translations or Text changes
1. Use the "Loco Translate" plugin
2. Text domain: black-listing-domains
3. https://wordpress.org/plugins/loco-translate/

## Updates
1. Version 2.0.0 Admin user addition of disposable email domains
2. Version 3.0.0 Addition of online update of disposable email domains
3. Version 3.1.0 Local caching of the email domains, custom White and Black email domains, listing of cache content

## Installation & Updates
1. Install or update by downloading the ZIP file via the green Code button.
2. Install as a new Plugin, which you upload in WordPress -> Plugins -> Add New -> Upload Plugin.
3. Activate the Plugin: Ultimate Member - Black listing email domains
