# Install
Insert lines above to composer.json:
```json
...
 "repositories": [
        {
            "type": "vcs",
            "url":  "git@dev.niif.hu:gyufi/shibbolethuserproviderbundle.git"
        }
    ],
...
```

Install the bundle,

```
composer require niif/shibboleth-user-provider-bundle
```

Update app/AppKernel.php

```
$bundles = array(
            ...
            new KULeuven\ShibbolethBundle\ShibbolethBundle(),
            new Niif\ShibbolethUserProviderBundle\NiifShibbolethUserProviderBundle(),
            ...
        );

```

Configure the shibboleth bundle

Configure the user provider.


The bundle provides roles for authenticated users according the saml entitlement attributes.

You can define regexp for ROLE_ADMIN and ROLE_whatever what you get from entitlement value.

Then you can implement access control as symfony does.