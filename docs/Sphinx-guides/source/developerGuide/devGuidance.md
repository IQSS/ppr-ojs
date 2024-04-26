# Development guidance

## Plugin Development
When starting the local environment, the contents of the PPR plugin is mounted into the OJS ``plugins/generic`` directory and it will be ready to use.

The OJS code in the Docker container is mounted into the ``environment/data/ojs/src`` local folder for review and debugging.

The local Docker image has been created with PHP XDebug to allow local debugging.

## Template Override
OJS provides a mechanism for plugins to override templates. This is achieved by using an OJS Hook to intercept the template file lookup.
The ``services/PPRTemplateOverrideService`` is managing the templates that the PPR plugin overrides.

The overridden templates are located within the ``templates``, with the same path as original OJS template. This makes it easier to locate the original templates in case changes need to be added to our version.

In OJS, templates are located within the ``<root>/templates`` or the ``<root>/lib/pkp/templates`` folders

We have developed a mechanism to wrap existing templates and load the original template with the new wrapper.
This is achieved by using the OJS ``TemplateManager`` feature ``include`` and the template suffix ``.load_ojs``.

There is an example in ``templates/submission/submissionMetadataFormTitleFields.tpl``

## Component Handler Override
We have implemented an override for the OJS ``AuthorGridHandler`` in order to fulfill the PPR program requirements. Handler override is one of the features supported by the OJS plugin framework.

The ``services/PPRWorkflowService`` has an example of how to override a component handler.

## Scheduled Tasks
The recommendation from OJS to implement scheduled tasks was to use the OJS ``acron`` plugin.
This plugin allows other plugin to configure scheduled tasks.
The ``acron`` plugin does not use the OS crontab functionality to operate. Instead, it is executed in each OJS request and determines if any of the scheduled tasks need to be executed.

To configure the PPR plugin scheduled tasks, we need to reload the ``acron`` plugin. This needs to be requested to the PKP team.

The PPR plugin scheduled tasks are configured in the ``./scheduledTasks.xml`` file.

The code for all scheduled tasks are under the ``tasks`` folder.

## CSS Updates
We have created a custom CSS file to style the forms and hide some of the fields in the OJS frontend and backend.

In OJS terminology, frontend is the reader's website, where the homepage, registration, and articles are displayed.  
The backend is the admin interface where submissions, reviews, and the site administration are displayed.

The configuration for the custom CSS is in the ``PeerPreReviewProgramPlugin`` class and the file is under ``css/iqss.css``

## Feature Flags
Most of the PPR features has been added to the plugin behind feature flags.
This has been implemented to reduce the risk of deployments as there is no test environment available.

The list of feature flags are configured in: ``pprOjsPlugin/settings/PPRPluginSettingsForm.inc.php``

To follow how they are used, look at the usage of the settings class: ``pprOjsPlugin/settings/PPRPluginSettings.inc.php``

## OJS reports
We can create custom reports and deploy them as plugins within the OJS plugins infrastructure.

Report plugins are very simple, we just need to create the report in a method called ``display`` within the plugin class.
This method will be executed when the report is launched within the user interface.

See the ``pprReportPlugin`` folder for more details on how to create a report plugin.

## Custom Email Templates
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