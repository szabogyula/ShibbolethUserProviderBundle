The bundle provides roles for authenticated users according the saml entitlement attributes.

You can define regexp for ROLE_ADMIN, ROLE_USER, ROLE_GUEST and ROLE_whatever what you get from entitlement value.

Then you can implement access control as symfony does.

# Install
Insert lines above to ```composer.json```:

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

Update ```app/AppKernel.php```

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

* *entitlement_serverparameter*, the key of the $_SERVER array, that contain the users role values.
* *entitlement_prefix*, the prefix of the role, for example urn:geant:niif.hu:hexaa:40:
* *generate_custom_roles*, generate roles with the entitlement value, for example ROLE_customer from urn:geant:niif.hu:hexaa:40:customer entitlement. Default is FALSE.
* *custom_role_prefix*, the prefix of custom role if exists. For example urn:geant:niif.hu:hexaa:40:org:sztaki the prefix is org: and the role will be: ROLE_sztaki. Default "".
* *custom_additional_role*, if exists, the role is taking to user if has custom role, typically ROLE_USER. Default: ROLE_USER.
* *admin_role_regexp*, what value is the ROLE_ADMIN. Default is /^admin$/
* *user_role_regexp*, what value is the ROLE_USER. Default is /^user$/
* *guest_role_regexp*, what value is the ROLE_GUEST. Default is /^guest$/

TODO custom_additional_role

update your ```app/config/config.yml```

```
...
niif_shibboleth_user_provider:
    entitlement_serverparameter: %shibboleth_user_provider_entitlement_serverparameter%
    entitlement_prefix: %shibboleth_user_provider_entitlement_prefix%
    generate_custom_roles: %shibboleth_user_provider_generate_custom_roles%
#    custom_role_prefix: %shibboleth_user_provider_custom_role_prefix%
#    custom_additional_role: %shibboleth_user_provider_custom_additional_role%
#    admin_role_regexp: %shibboleth_user_provider_admin_role_regexp%
#    user_role_regexp: %shibboleth_user_provider_user_role_regexp%
#    guest_role_regexp: %shibboleth_user_provider_guest_role_regexp%
...
```

in ```app/config/parameters.yml```

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