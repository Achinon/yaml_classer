# Symfony YAML Classer Bundle
Transforms YAML files into callable classes for easy reference inside of your IDE.

### Setup:

To install the package, run ```composer require achinon/yaml_classer```.

After having the package installed, to generate PHP code of off your YAML file, peform the command with the filename and the name of the class you wish for it to have.

```php bin/console achinon:yaml_classer example_config.yml ExampleConfig```

The PHP Class should be created.

Now you can use Dependency Injection to access your config, or just create a new instance without any additional parameters required.
