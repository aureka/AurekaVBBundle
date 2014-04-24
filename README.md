AurekaVBulletinBundle
=====================
[![Build Status](https://travis-ci.org/aureka/AurekaVBBundle.png)](https://travis-ci.org/aureka/AurekaVBBundle)

Integrates vbulletin with a Symfony application, providing a Single Sign In. Any user logging into a Symfony application will automatically log into vBulletin.

## Installation

Add the following line to your `composer.json`:

```json
{
    "require": {
        "aureka/vb-bundle" : "dev-master"
    }
}
```

Execute `composer update`.

Add the following line to your `AppKernel.php`.

```php
public function registerBundles()
{
    $bundles = array(
        // your other bundles
        new Aureka\VBBundle\AurekaVBBundle(),
    );
}
```


## Configuration

You must add some vBulletin settings in your `config.yml`.


```yaml
aureka_vb:
    license: 'YOURLICENSEGOESHERE' # can be found in functions.php
    default_user_group: 2 #optional
    ip_check: 1 #optional
    cookie_prefix: 'bb_' #optional
    database:
        driver: 'pdo_mysql'
        host: 'localhost'
        name: 'vb_database_name'
        port: null
        user: 'vb_database_user'
        password: 'vb_database_password'
        table_prefix: 'vb3_' #optional
```

## Automatic logout

In order to logout users from vbulletin, add the following lines to your `security.yml`:

```yaml
security:
    # ...
    firewalls:
        main:
            # ...
            logout:
                handlers: [aureka_vb.logout_handler]
```
