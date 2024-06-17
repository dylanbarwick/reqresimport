# DRPG Drupal Coding Test

This is our coding test to assess your skills and thought processes when coming to build features in an application.

Please send any questions you have through to ollie.selly@drpgroup.com / paul.stevenson@drpgroup.com

Read through the task below and send through any questions you may have before getting started.
As a guide, we'd expect this task to take between 2-3 hours with better examples at the upper end of that timescale. (Excluding initial Framework setup)


## Our Expectation

Using a fresh Drupal project we want you to integrate with a 3rd party API to pull in user data.

We want this to be maintainable and flexible to potentially add other API calls in the future.
This code might also be used in other parts of the application to get a fresh set of the data from the API.

Here are a few pointers we would suggest to think about when coming up with your solutions:

- How would we be able to test this using PHPUnit in the future if we needed to?
- For Senior/Lead roles, we would expect to see some automated tests.
- Don't always write everything yourself you can pull in other packages if you feel they are needed.
- Error handling of the API, what if the API is unavailable?

## Task

Build a command that pulls data from an API and stores the User records.

- Call the [https://reqres.in/] API to pull in the first page of users only.

We could potentially use this command in a schedule/cron to repeatedly update the users from the API.

Please don’t view this task as just completing the functional requirements. This is your chance to wow us with what you can do.


# Operating instructions

1 - install module
2 - go here: `/admin/people` check list of users
3 - go here: `reqresimport/fetch-json` and adjust "parameter value" to `2`
4 - click `send` - you should see a table of six retrieved entries starting with `michael.lawson@reqres.in`
5 - uncheck "preview option" and click `send`
6 - go back to `/admin/people` and you should see six new user accounts.
7 - call up a terminal window and sh into the web server
8 - type `drush mim reqres_user_data` and hit return
9 - go back to `/admin/people` and you should see six new user accounts, these will have an image saved against `field_reqres_avatar_image`.
10 - you may need to adjust visibility of fields here: `/admin/config/people/accounts/form-display` to make sure all of the `field_reqres_*` fields are visible on the form.

The automated tests are not finished. Any advice you can give would be much appreciated.