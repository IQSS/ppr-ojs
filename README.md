# Peer Pre-Review Program OJS Customizations Plugin
OJS plugin to implement the customizations for the Harvard Peer Pre-Review Program.

## Production Release
In order to deploy into the PKP servers, we need to create ``tar.gz`` file with the ``pprOjsPlugin`` folder and
send it to PKP support: ``support@publicknowledgeproject.org``. They will review the code and deploy the plugin.

We use the ``./VERSION`` file to generate the release version number.
This version number is used in the plugin ``pprOjsPlugin/version.xml`` file and in the generated ``tar.gz`` artifact.

To create a release, execute the target:
```
make release
```

## Installation
Deploy the plugin root folder ``pprOjsPlugin`` into the ``generic`` plugins folder of the OJS installation.

This is typically ``ojs/plugins/generic``

## Local Environment
The ``environments`` folder contains the Docker configuration to run the OJS application locally mounting the ``pprOjsPlugin`` directory as a generic plugin.

The ``environments/data/db`` stores the DB data and log files.

The ``environments/data/ojs`` stores the OJS files, logs, and source code

### Build the OJS Docker images
Although PKP provides a set of Docker images to run the OJS application, we have extended the images to add support
for XDebug and to mount the OJS application code into the ``environment/data/ojs/src`` folder.

Build the OJS Docker image with XDebug support for OJS version 3.3:
``make docker``

Build the OJS Docker image with XDebug support for OJS version 3.4:
``make docker34``

### Start the OJS application
The first time we start the OJS application, the ojs-entry-point script will copy the source code into the ``environment/data/ojs/src`` folder.
This will take around 5 minutes to complete. After the copy is made, the script will start the Apache server. In subsequent runs, the copy is bypassed on startup.

The OJS appliaction will be ready when you the see the message: ``STARTING OJS...`` in the console log.

To start the environment for OJS version 3.3, execute:
``make``

To start the environment for OJS version 3.4, execute:
``make dev34``

### Clean the data directories
To start the OJS application fresh, you will need to clean the DB and OJS files within the data directory.

Execute the following target to delete all data files:
``make clean``

# Technical Notes
## Plugin Development
When starting the local environment, the contents of the PPR plugin is mounted into the OJS ``plugins/generic`` directory and it will be ready to use.

The OJS code in the Docker container is mounted into the ``environment/data/ojs/src`` local folder for review and debugging.

The local Docker image has been created with PHP XDebug to allow local debugging.

### Template Override
OJS provides a mechanism for plugins to override templates. This is achieved by using an OJS Hook to intercept the template file lookup.
The ``services/PPRTemplateOverrideService`` is managing the templates that the PPR plugin overrides.

The overridden templates are located within the ``templates``, with the same path as original OJS template. This makes it easier to locate the original templates in case changes need to be added to our version.

In OJS, templates are located within the ``<root>/templates`` or the ``<root>/lib/pkp/templates`` folders

We have developed a mechanism to wrap existing templates and load the original template with the new wrapper.
This is achieved by using the OJS ``TemplateManager`` feature ``include`` and the template suffix ``.load_ojs``.

There is an example in ``templates/submission/submissionMetadataFormTitleFields.tpl``

### Component Handler Override
We have implemented an override for the OJS ``AuthorGridHandler`` in order to fulfill the PPR program requirements. Handler override is one of the features supported by the OJS plugin framework.

The ``services/PPRWorkflowService`` has an example of how to override a component handler.

### CSS Updates
We have created a custom CSS file to style the forms and hide some of the fields in the OJS frontend and backend.

In OJS terminology, frontend is the reader's website, where the homepage, registration, and articles are displayed.  
The backend is the admin interface where submissions, reviews, and the site administration are displayed.

The configuration for the custom CSS is in the ``PeerPreReviewProgramPlugin`` class and the file is under ``css/iqss.css``

### Feature Flags
Most of the PPR features has been added to the plugin behind feature flags.
This has been implemented to reduce the risk of deployments as there is no test environment available.

The list of feature flags are configured in: ``pprOjsPlugin/settings/PPRPluginSettingsForm.inc.php``

To follow how they are used, look at the usage of the settings class: ``pprOjsPlugin/settings/PPRPluginSettings.inc.php``
