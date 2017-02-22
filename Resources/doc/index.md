LdapBundle
==========

LdapBundle provides a Ldap authentication system without the `apache mod_ldap`. He use `php-ldap` package with a form to authenticate the users. LdapBundle also can be used for the authorization. He retrieves the  Ldap users' roles.

This bundle is based on the original work of Maks3w and adapted for multi-domains.

This bundle requires Zend Ldap v2.

Install
-------
1. Add DoLLdapBundle in your composer.json
2. Enable the bundle
3. Configure security.yml
4. Configure config.yml
5. Enable FOSUserBundle as User Provider

### 1. Add DoLLdapBundle in your composer.json

Add this bundle to your `vendor/` dir:

```json
{
    "require": {
        "dol/ldap-bundle": "^2.0"
    }
}
```

### 2. Enable the Bundle

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new DoL\LdapBundle\DoLLdapBundle(),
    );
}
```

### 3. Configure security.yml
``` yaml
# app/config/security.yml

security:
  # Preserve plain text password in token for refresh the user.
  # Analyze the security considerations before turn off this setting.
  erase_credentials: false
  
  firewalls:
    main:
      pattern:    ^/
      dol_ldap:   ~
      form_login:
        always_use_default_target_path: true
        default_target_path: /profile
      logout:     true
      anonymous:  true

  providers:
    dol_ldapbundle:
      id: dol_ldap.security.user.provider

  encoders:
      AcmeBundle\Acme\User\LdapUser: plaintext
```

**Optional you can also enable basic authentication**
```yaml
# app/config/security.yml

security:
  firewalls:
    api:
      pattern:            ^/api
      dol_ldap_httpbasic: ~
```

### 4. Configure config.yml
``` yaml
# app/config/config.yml
dol_ldap:
    domains:
        # First domain
        server1:
            driver:
                host:                your.first.host.foo
#               port:                389    # Optional
#               username:            foo    # Optional
#               password:            bar    # Optional
#               bindRequiresDn:      true   # Optional
#               baseDn:              ou=users, dc=host, dc=foo   # Optional
#               accountFilterFormat: (&(uid=%s)) # Optional. sprintf format %s will be the username
#               optReferrals:        false  # Optional
#               useSsl:              true   # Enable SSL negotiation. Optional
#               useStartTls:         true   # Enable TLS negotiation. Optional
#               accountCanonicalForm: 3 # ACCTNAME_FORM_BACKSLASH this is only needed if your users have to login with something like HOST\User
#               accountDomainName: HOST
#               accountDomainNameShort: HOST # if you use the Backslash form set both to Hostname than the Username will be converted to HOST\User
            user:
                baseDn: ou=users, dc=host, dc=foo
                filter: (&(ObjectClass=Person))
#               usernameAttribute: uid # Optional
                # Specify ldap attributes mapping [ldap attribute, user object method]
                attributes:
#                   - { ldap_attr: uid,  user_method: setUsername } # Default
#                   - { ldap_attr: cn,   user_method: setName }     # Optional
#                   - { ldap_attr: ...,  user_method: ... }         # Optional
        # Second domain
        server2:
            driver:
                host:                your.second.host.foo
#               port:                389    # Optional
#               username:            foo    # Optional
#               password:            bar    # Optional
#               bindRequiresDn:      true   # Optional
#               baseDn:              ou=users, dc=host, dc=foo   # Optional
#               accountFilterFormat: (&(uid=%s)) # Optional. sprintf format %s will be the username
#               optReferrals:        false  # Optional
#               useSsl:              true   # Enable SSL negotiation. Optional
#               useStartTls:         true   # Enable TLS negotiation. Optional
#               accountCanonicalForm: 3 # ACCTNAME_FORM_BACKSLASH this is only needed if your users have to login with something like HOST\User
#               accountDomainName: HOST
#               accountDomainNameShort: HOST # if you use the Backslash form set both to Hostname than the Username will be converted to HOST\User
            user:
                baseDn: ou=users, dc=host, dc=foo
                filter: (&(ObjectClass=Person))
#               usernameAttribute: uid # Optional
                # Specify ldap attributes mapping [ldap attribute, user object method]
                attributes:
#                   - { ldap_attr: uid,  user_method: setUsername } # Default
#                   - { ldap_attr: cn,   user_method: setName }     # Optional
#                   - { ldap_attr: ...,  user_method: ... }         # Optional
        # N domain
#       serverN:
#       ...
#   service:
#       user_hydrator: dol_ldap.user_hydrator.default   # Overrides default user hydrator
#       ldap_manager: dol_ldap.ldap_manager.default     # Overrides default ldap manager
```

**You need to configure the parameters under the dol_ldap section.**

### 5. Enable FOSUserBundle as User Provider

In security.yml make a chain_provider with fos_userbundle before dol_ldapbundle

``` yaml
# app/config/security.yml

security:
    providers:
        chain_provider:
            chain:
                providers: [fos_userbundle, dol_ldapbundle]

        dol_ldapbundle:
            id: dol_ldap.security.user.provider

        fos_userbundle:
            id: fos_user.user_manager

```

### Cookbook

Look the cookbook for another interesting things.

- [Create a custom hydrator](cookbook/custom_hydrator.md)
- [Prevent guess registration with a username that already exists on LDAP](cookbook/validator.md)
- [Example configuration for Active Directory](cookbook/active-directory.md)
