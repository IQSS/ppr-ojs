# Environments

Currently, the PPR infrastructure is composed be the following installations:

## Production environment

- [Production](https://pre-review.iq.harvard.edu/index.php/iqss/login)

In the production environment, all the files related to the project are located in the `www` directory and you will be able to find the logs on `logs`. Once there the file `config.inc.php` will contain all the important settings created when the application is initialized for the first time. 

Some of the settings that can be configured in this file are:

- Database
- Cache
- Localization
- Files
- MIME
- Security
- Email
- Search

In this environment, you will also find the `plugins` folder which will contain the plugin code, the reports plugin will be located in the `reports` directory while the main PPR plugin is located in the `generic` folder.

## Sandbox environment

- [Sandbox-PKP](https://iqss.sandbox.sfulib8.publicknowledgeproject.org)

In the sandbox environment, you will find more projects that are not related to this plugin, these were set up by PKP probably for testing or setup purposes. In the server, the folders that are related to our installation are `https://iqss.sandbox.sfulib8.publicknowledgeproject.org/` and `pre-review.iq.harvard.edu`.

