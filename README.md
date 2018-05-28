# Scid plugin for CakePHP

## Installation

You can install this plugin into your CakePHP application using [composer](http://getcomposer.org).

The recommended way to install composer packages is:

```
composer require sdevore/cakephp-scid-plugin
```

So this plugin is expecting to install some npm packages using the [Asset Packagist](https://asset-packagist.org) a tool for installing npm and brower packages with composer.  In order to do this you need to let it know where the repository of those mapped packages are add the following to your composer.json file
```
"repositories": [
    {
        "type": "composer",
        "url": "https://asset-packagist.org"
    }
]
```

Then to ensure that the plugin finds those npm and brower assets in the expected location please add the following to the composer files as well.  The plugin uses []oomphinc/composer-installers-extender](https://github.com/oomphinc/composer-installers-extender) to install them inside the webroot.
```
"extra": {
    "installer-types": [
      "bower-asset",
      "npm-asset"
    ],
    "installer-paths": {
      "webroot/assets/{$vendor}/{$name}/": [
        "type:bower-asset",
        "type:npm-asset"
      ]
    }
  },
 ```

note that the `webroot/assets` directory can be added to your `.gitignore` file
