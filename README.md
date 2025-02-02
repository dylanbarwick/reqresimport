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
1. Install the module.
2. Go to `/admin/people` and check the list of users.
3. Go to `reqresimport/fetch-json` and adjust the "parameter value" to `2`.
4. Click "send" - you should see a table of six retrieved entries starting with `michael.lawson@reqres.in`.
5. Uncheck the "preview option" and click "send".
6. Go back to `/admin/people` and you should see six new user accounts.
7. Call up a terminal window and ssh into the web server.
8. Type `drush migrate:import reqres_user_data` and hit return.
9. Go back to `/admin/people` and you should see six new user accounts. These will have an image saved against `field_reqres_avatar_image`.
10. You may need to adjust the visibility of fields here: `/admin/config/people/accounts/form-display` to make sure all of the `field_reqres_*` fields are visible on the form.

The automated tests are not finished. Any advice you can give would be much appreciated.

## Patch instructions

Insert this in the `composer.json` file under `extra`:

``
"extra": {
  "patches": {
    "drupal/migrate_plus": {
      "Allow for urls via callback, https://www.drupal.org/project/migrate_plus/issues/3040427": "https://www.drupal.org/files/issues/2023-02-15/3040427-42-migrate_plus_multiple_urls.patch"
    }
  },
  ...
``

## Update 2024-06-18 16:00 

There is now a view available (`/reqresimport/imported-users`) that will list all user accounts with a populated `field_reqres_id` field. It lists all the fields populated with data from `reqres` and also offers links to edit and also reimport each one. 

The reimport function will individually fetch data from reqres and overwrite any local changes you might have made.

In order to effect these changes you should run `drush updb`.

## Update 2024-06-25 13:00 

Credit where it's due, the bulk of the http client service ReqresApiClient comes from this github repo: https://github.com/daggerhart/drupal8_examples/tree/master/modules/cat_api

...although I did manage to adapt it and integrate it into my own efforts in under an hour. The FetchJson service should be renamed ReqresUtils for clarity and the http client code in that service is now redundant but I left it in there to give you an idea of how I eventually ended up where I did.

Come to think of it, the utility methods in FetchJson also violate the single responsibility principle.
