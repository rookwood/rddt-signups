# [rddt] Sign-ups
## This is the sign-up website for [rddt] in Guild Wars.

This is still a work in progress and open to anyone wishing to contribute.

### Features completed
* User registration (including handling logins, profiles, and optional email verificaiton)
* Access control (using the excellent [Vendo-ACL](http://github.com/vendo/acl)
* Rough user administration to edit user data including application roles
* Character creation and management
* Event creation and managment
* Automatic handling of stand-by status for any given event
* Build creation and management (including in-game roles)

### Features in progress
* Filter displayed roles in event sign-up by users' eligible characters

### Features to be added
* Administrative versions of event, build, and character management


### Idea scratchpad (i.e. things I need to add)
* Get rid of all the silly redirects... just set up the view class manually; it'll be cleaner in the end (in-progress)
* Go through all policy classes and controllers to make sure that potential errors are all accounted for
* Write message files for aforementioned potential errors
* Add comments that I failed to write on the first draft
* Consider what general users will need access to should they be allowed to create events freely.
* Email notification of event changes?
