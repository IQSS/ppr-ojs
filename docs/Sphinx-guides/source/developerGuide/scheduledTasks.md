# Scheduled Tasks

For scheduled tasks, we use the [`Acron`](https://github.com/pkp/acron) plugin which is developed and recommended by OJS. It is important to note that this plugin relies on a request to execute the scheduled tasks, if the application is not used it won't be able to execute the scheduled tasks. This is not an issue in live environments since a web crawler or normal traffic normally triggers this functionality.


## Reloading scheduled tasks

Please note that this functionality is only available to administrators, the `Acron` plugin provides the option `Reload Scheduled Tasks` on:

```
Settings > Website > Plugins > Generic Plugins > Acron > Reload Scheduled Task
```

These tasks are configured on: 
- **pprOjsPlugin/scheduledTasks.xml**
- **pprReviewsReportPlugin/scheduledTasks.xml**