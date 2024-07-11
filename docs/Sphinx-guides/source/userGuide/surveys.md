# Survey Code Analysis
This is an analysis implemented to understand the surveys implemented in PPR plugin.

## Analysis
There are currently 4 surveys, 3 survey widgets shown in the OJS UI and 1 notification email that will send a survey.

**Submission Completed Survey Widget:** This survey widget will be shown at the create submission wizard confirmation page. This survey will only be shown the first time a submission is completed. The system will set a flag once shown and will not show this survey again for the same author.

**Author Dashboard Survey:** This survey will be shown in the author submission dashboard. This survey will be shown when the submission has at least one completed review. There are no time restrictions on this survey.

**Review Completed Survey Widget:** This survey widget will be shown at the create review wizard confirmation page. This survey will be shown every time the reviewer access the final page of a review.

**Review Sent Author Notification:** This is an email with a survey link that is sent to the author. The email is sent 7 days (configurable in the plugin settings) after a review file has been sent to the author. There will be an email sent for every review in the submission that has been sent to the author."