# Symfony YAML Classer Bundle
Transforms YAML files into callable classes for easy reference inside of your IDE.

### Setup:

To install the package, run ```composer require achinon/yaml_classer```.

With the package installed, to generate PHP code of off your YAML file, peform the command with the filename and the name of the class you wish for it to have.

```php bin/console achinon:yaml_classer example_config.yml ExampleConfig```

The PHP Class should be created.

Now you can use Dependency Injection to access your config, or just create a new instance without any additional parameters required.

![Usage example](https://drive.usercontent.google.com/download?id=1IoBl50Z1yI00bRqXhKCvoZxbDVZstBt2&export=view)

YAML file imported in example above:
```yaml
example: 1
example2: 'example'
example3:
  example4: 'hey'
  example5: 'hi'
  example6:
    example7: 
      - 'hello'
      - 'sadnioaseinko'
  example8: "https://github.com/Achinon/yaml_classer/"
```
