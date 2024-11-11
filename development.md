# UM Black listing email domains
Current GitHub list content: White list 919 `allowDomains` and Black list 172 893 `denyDomains`.
## Validation process
Both email domain lists are downloaded if required from GitHub 
and used in the plugin for each email domain validation during an User Registration.
### UM Default validation
First step in the email domain validation is taken from the textarea at your screen copy page 
from UM Settings -&gt; Access -&gt; Other -&gt; "Blocked Email Addresses".

Default UM single email address blocking like `*@domain.com` or `qwerty@domain.com`
### Plugin validation
#### Step 1
The plugin will add the option to exclude top domains also entered in this textarea.
For example no Registrations with email addresses like `company.xyz` or `abc.xyz` you enter `*.xyz`.
To exclude departments of a company you enter `*.company.com` in the textarea. Always one entry per line.
#### Step 2
If the email domain is not being disqualified yet by the entries in the textarea 
the plugin is downloading the 919 `allowDomains` list from GitHub.

At my tests this will add about 0.2 seconds to the Registration process.

These allowed email domains will cover most commonly used email domains like `gmail,com`, `hotmail.com` etc.
The plugin is testing the current Registration email domain against this `allowDomains` list.
#### Step 3
If not found to be a common email domain the `denyDomains` list is downloaded from GitHub 
which takes max 1 second at my Web hosting company. 

If this final test for this `denyDomains` list fails ie not found in the `denyDomains` list the email domain is being accepted.
### Development
#### Next version 3.1.0 release date 2024-11-13
Local cache for the GitHub `allowDomains` and `denyDomains` lists where you can specify how many hours to use the downloaded lists until refreshing the caches again from GitHub.
Caching lifetime set to 0 hours will disable caching.
With the caching implemented, loading time of the largest `denyDomains` list with the size of 3.8 MBytes,
will be less than 0.05 seconds.

An option to add custom email domains to the `allowDomains` local caching list, to avoid common local addresses to be searched for in the `denyDomains` list.

An option to add custom email domains to the `denyDomains` local caching list.

Basic HTML formatted pages with "White email domains" (`allowDomains`) and "Blocked email domains" (`denyDomains`) from current caching data
#### Future updates

### Testing examples
Examples of disposable email addresses in the `denyDomains` list for your testing: 
`detroitdaily.com` `michigan-web-design.com` `topmail.com` `volvogroup.tk`
