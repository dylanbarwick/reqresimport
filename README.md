# DRPG Drupal Coding Test

This is our coding test to assess your skills and thought process when coming to build features in an application.

Please send any questions you have through to ollie.selly@drpgroup.com / paul.stevenson@drpgroup.com

Please read through the task below and send through any questions you may have before getting started.
As a guide, we'd expect this task to take between 2-3 hours with better examples at the upper end of that timescale. (Excluding initial Framework setup)


## Our Expectation

Using a fresh Drupal project we want you to integrate with a 3rd party API to pull in user data.

We want this to be maintainable and flexible to potentially add other API calls to the future.
This code might also be used in other parts of the application to get a fresh set of the data from the API.

There's a few pointers we would suggest to think about when coming up with your solutions:

- How we would be able to test this using PHPUnit in the future if we needed to?
- For Senior/Lead roles, we would expect to see some automated tests.
- Don't always write everything yourself you can pull in other packages if you feel they are needed.
- Error handling of the API, what if the API is unavailable?

## Task

Build a command that pulls data from an API and stores the User records.

- Call the [https://reqres.in/] API to pull in the first page of users only.

We could potentially use this command in a schedule to repeatedly update the users from the API.

Please don’t view this task as just completing the functional requirements. This is your chance to wow us with what you can do…
