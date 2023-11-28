# Peer Pre-Review Program OJS Customizations Plugin
OJS plugins to implement the customizations for the Harvard Peer Pre-Review Program.

## Production Release
In order to deploy into the PKP servers, we need to create ``tar.gz`` file with the plugin folder and
send it to PKP support: ``support@publicknowledgeproject.org``. They will review the code and deploy into the production servers.

We use the ``./VERSION`` file to generate the release version number.
This version number is then used in the plugin ``<plugin>/version.xml`` file and in the generated ``tar.gz`` artifact name.

To create a release, execute the Makefile target:
```
make release
```
This will increase the version number, update the PPR plugins version data, and create the ``tar.gz`` files under the project ``releases`` folder. 

## Installation
Use the OJS ``Upload A New Plugin`` feature in the ``Website > Plugins`` section for the first time deployment.

To deploy upgrades, select the ``upgrade`` function within the deployed plugin submenu.

In both cases, you need to upload the ``tar.gz`` file with the plugin contents created using the ``make release`` target mentioned before.

### Manual Installation
Copy or mount the plugin's root folder into the OJS plugins directory.

To deploy the ``pprOjsPlugin`` plugin, copy/mount root folder ``pprOjsPlugin`` into the ``generic`` plugins folder of the OJS installation.

This is typically ``ojs/plugins/generic``

To deploy the ``pprReportPlugin`` plugin, copy/mount root folder ``pprReportPlugin`` into the ``reports`` plugins folder of the OJS installation.

This is typically ``ojs/plugins/reports``

## Local Environment
The ``environments`` folder contains the Docker configuration to run the OJS application locally
mounting all the plugin directories in the appropriate OJS folders.

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

## Automated testing
There is a suite of automated tests that run automatically in GitHub when a PR is created or after a commit in the ``main`` branch.

The tests used a custom Docker image to execute the tests: ``hmdc/ppr_ojs_test``. More information of how to build this image in the section below.

These tests can be executed locally with the make target test:  
``make test``
``make test_report``

### Build the PPR test Docker image
Build test image with PHP, PHPUnit and the OJS source code.
``make docker-test``

The Docker image is based on the OJS installation images from https://gitlab.com/pkp-org/docker/ojs

The definition of the PPR test Docker image is located under: ``environment/Dockerfile.test``

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

### Scheduled Tasks
The recommendation from OJS to implement scheduled tasks was to use the OJS ``acron`` plugin.
This plugin allows other plugin to configure scheduled tasks.
The ``acron`` plugin does not use the OS crontab functionality to operate. Instead, it is executed in each OJS request and determines if any of the scheduled tasks need to be executed.

To configure the PPR plugin scheduled tasks, we need to reload the ``acron`` plugin. This needs to be requested to the PKP team.

The PPR plugin scheduled tasks are configured in the The ``./scheduledTasks.xml`` file.

The code for all scheduled tasks are under the ``tasks`` folder.

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

### OJS reports
We can create custom reports and deploy them as plugins within the OJS plugins infrastructure.

Report plugins are very simple, we just need to create the report in a method called ``display`` within the plugin class.
This method will be executed when the report is launched within the user interface.

See the ``pprReportPlugin`` folder for more details on how to create a report plugin.

### Custom Email Templates
These are the custom email templates created for the PPR program.

 * ``PPR_SUBMISSIONS_REPORT_TASK`` => pprReviewsReportPlugin/taks/PPREditorReportTask.inc.php
 * ``PPR_REVIEW_DUE_DATE_EDITOR`` => pprOjsPlugin/tasks/PPRReviewDueDateEditorNotification.inc.php
 * ``PPR_REVIEW_DUE_DATE_REVIEWER`` => pprOjsPlugin/tasks/PPRReviewReminder.inc.php
 * ``PPR_REVIEW_REQUEST_DUE_DATE_REVIEWER`` => pprOjsPlugin/services/email/PPRReviewReminderEmailService.inc.php
 * ``PPR_REQUESTED_REVIEWER_UNASSIGN`` => pprOjsPlugin/services/reviewer/PPRUnassignReviewerForm.inc.php
 * ``PPR_CONFIRMED_REVIEWER_UNASSIGN`` => pprOjsPlugin/services/reviewer/PPRUnassignReviewerForm.inc.php
 * ``PPR_SUBMISSION_APPROVED`` => pprOjsPlugin/services/submission/PPRSubmissionActionsService.inc.php
 * ``PPR_REVIEW_ACCEPTED`` => pprOjsPlugin/services/reviewer/PPRReviewAcceptedService.inc.php
 * ``PPR_REVIEW_SUBMITTED`` => pprOjsPlugin/services/reviewer/PPRReviewSubmittedService.inc.php
 * ``PPR_REVIEW_DUE_DATE_WITH_FILES_REVIEWER`` => pprOjsPlugin/tasks/PPRReviewReminder.inc.php
 * ``PPR_REVIEW_PENDING_WITH_FILES_REVIEWER`` => pprOjsPlugin/tasks/PPRReviewReminder.inc.php
 * ``PPR_REVIEW_SENT_AUTHOR`` => pprOjsPlugin/tasks/PPRReviewSentAuthorNotification.inc.php







