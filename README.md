## INTRODUCTION

The reqres Import module is a demo module that connects to the `reqres.in`
API and retrieves data from it.

This is the basic module that provides generic utilities that other
sub-modules can use. The plan is to create different sub-modules to deal
with individual endpoints (eg `reqresimport_users`) and have them use the
generic features in here but allow them to override where appropriate.

As the family of sub-modules grows, any features that are created for them
and could be used in other sub-modules can be incorporated here and made
available to other sub-modules.

## REQUIREMENTS

This is currently a stand-alone module that does not require any other modules.

## INSTALLATION

Install as you would normally install a contributed Drupal module.
See: https://www.drupal.org/node/895232 for further information.

## CONFIGURATION
- go here:  Administration -> Configuration -> System -> Reqres settings -> Reqres basic settings
- review the default values and adjust as necessary

## TEST DRIVE
- go here: `/reqresimport/fetch-json-test`
- amend the parameters in the form and click `Send`
- check that the fetched data is what you're expecting

Note: this test page is simply a way to make sure the module is connecting to
the API and retrieving usable data. You should use it to run basic tests on the
different endpoints, eg `/api/users` or `/api/unknown`.



