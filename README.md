# [rddt] Sign-ups
## This is the sign-up website for [rddt] in Guild Wars.

This is still a work in progress and open to anyone wishing to contribute.

### Features completed
* User registration (including handling logins, profiles, and optional email verificaiton)
* Access control (using the excellent [Vendo-ACL](http://github.com/vendo/acl)
* Rough user administration to edit user data including application roles
* Adding characters to users' accounts and displaying a list of all that are attached
* Display of all guild events and detailed view of such which includes attendees
* Event creation, cancellation, and sign-up
* Event editing
* Automatic handling of stand-by status for any given event

### Features in progress
* Characrter editing and removal
* Build creation and editing

### Features to be added
* Administrative versions of event and character management


### Idea scratchpad (i.e. things I need to add)
* Sort through stand-by signups on cancelation... but what about voluntary stand-bys?
* Get rid of all the silly redirects... just set up the view class manually; it'll be cleaner in the end (in-progress)
* Go through all policy classes and controllers to make sure that potential errors are all accounted for
* Add comments that I was too lazy to write on the first draft
* Consider what general users will need access to should they be allowed to create events freely.
* Need sensibile form structure / names for slot (in-game role) handling