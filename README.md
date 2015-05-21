The bundle provides roles for authenticated users according the saml entitlement attributes.

You can define regexp for ROLE_ADMIN, ROLE_USER, ROLE_GUEST and ROLE_whatever what you get from entitlement value.

Then you can implement access control as symfony does.

# Install
Insert lines above to composer.json:

```
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

Configure the shibboleth bundle as you see in https://github.com/rmoreas/ShibbolethBundle.

Configure the user provider.

app/config/config.yml

```
...
niif_shibboleth_user_provider:
    entitlement_serverparameter: %shibboleth_user_provider_entitlement_serverparameter%
    entitlement_prefix: %shibboleth_user_provider_entitlement_prefix%
    generate_custom_roles: %shibboleth_user_provider_generate_custom_roles%
#    admin_role_regexp: %shibboleth_user_provider_admin_role_regexp%
#    user_role_regexp: %shibboleth_user_provider_user_role_regexp%
#    guest_role_regexp: %shibboleth_user_provider_guest_role_regexp%
...
```

in app/config/parameters.yml

```
parameters
    ...
    shibboleth_user_provider_entitlement_serverparameter: edupersonentitlement
    shibboleth_user_provider_entitlement_prefix: urn:oid:
    shibboleth_user_provider_generate_custom_roles: true
    ...
```


# Simulate shibboleth authentication in development environment

When you develop an application you shoud simulate shibboleth authentication anyhow.
You can do it in apache config, after enable *headers* and *env* modules:

```
        Alias /my_app /home/me/my_app/web
        <Directory /home/me/my_app/web>
           Options Indexes FollowSymLinks
           AllowOverride All
           Require all granted           
           SetEnv Shib-Person-uid myuid
           SetEnv Shib-EduPersonEntitlement urn:oid:whatever
           RequestHeader append Shib-Identity-Provider "fakeIdPId"
           RequestHeader append eppn "myeppn"
        </Directory>
```