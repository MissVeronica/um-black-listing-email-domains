# UM Black listing email domains
Current GitHub list content: 919 `allowDomains` and 172 893 `denyDomains`.
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
To exclude a department of a company you enter `*.department.company.com` in the textarea. Always one entry per line.
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
#### Next version
Improvements being worked on today is adding local cache for the GitHub `allowDomains` and `denyDomains` lists where you can specify how many hours to use the downloaded lists until refreshing the caches again from GitHub.

With the caching implemented, loading time of the largest `denyDomains` list with the size of 3.8 MBytes,
will be less than 0.05 seconds.
#### Future updates
I am also looking for an option to list these GitHub lists page per page, 
which is not possible within the UM Settings.

An option to add custom email domains to the `allowDomains` local caching list will also be implemented,
to avoid common local addresses to be searched for in the `denyDomains` list.
### Testing examples
Examples of disposable email addresses in the `denyDomains` list for your testing: 
`detroitdaily.com` `michigan-web-design.com` `topmail.com` `volvogroup.tk`