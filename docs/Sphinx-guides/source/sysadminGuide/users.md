# Users

## User types

Within the following table, you'll encounter a list of user types alongside descriptions of their respective tasks. This guide serves to clarify the roles and responsibilities associated with each user category.

You will find different roles that are on the PPR-OJS installation but not all of them are used, here is a list of the roles that are used on our implementation:

| User Type | Description |
| ----------- | ----------- |
| Managing Editor | Receive requests for reviews and assigns Associate Editors|
| Associate Editor | Assigns reviewers for submissions |
| Author | Will submit requests for reviews |
| Reviewer | Will be assigned by the Associate Editor to make a review |
| Reader | This is the default role |

Please note that the ``Managing Editor`` has the same privileges than the ``Journal Manager`` on OJS and the ``Associate Editor`` is the equivalent of the ``Section Editor`` on the original implementation of PPR.

## Log In as another user

As an administrator, you have the capability to log in as any user within the system. This feature is particularly useful for troubleshooting user-specific issues or providing assistance with account-related inquiries. To log in as another user, follow these steps:

- Under the **Settings** menu go to **Users && Roles** 
- On the **Users** tab find the user that you want to log in as.
- Click the triangle to the left of the user name to reveal the options.
- Select **Login As** and confirm **ok** on the popup message.

Please be aware that all actions you perform will be attributed to this user.

## Making other users a ``Super User``

You can create multiple ``Super Users`` on the application by adding a special group to it but currently this has to be done directly on the Database. 

```
TODO: Document the process of adding super users
```