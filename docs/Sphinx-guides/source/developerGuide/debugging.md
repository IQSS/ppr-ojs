# Debugging

Depending on your IDE and environment debugging the application can have different configurations.

## VS Code & Docker

To enable debugging on VS Code you will need the [PHP Extension pack](https://marketplace.visualstudio.com/items?itemName=xdebug.php-pack), create a configuration file, and update this section:

```
{
    "name": "Listen for Xdebug",
    "type": "php",
    "request": "launch",
    "port": 9003,
    "pathMappings": {
        "/tmp/ojs-src": "${workspaceFolder}/environment/data/ojs/src"
    }
}
```