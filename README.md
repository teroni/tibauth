**How to set up the auth server**
[x] make docker image for this
[x] host docker image
[x] readme for linking config variables
[] sql dumps
[x] irc auth/nickserv system
[x] irc autojoin channels

All corps that are in the corp list in config will auto-generate a role in auth.
All members within an alliance in config list will be added to "blue" role.

To create a services group from an auth role, while logged in as as superuser, hit "add auth group" and it will generate the
required table rows with the correct acl, name, and slug.


Services Instructions
**/// TS3 Integration ///**
I recommend setting up your ts3 groups beforehand, as the server query likes to take ownership making it a pain to edit
Once you've set up the groups, name them exactly how you named your roles in the auth services.
ex: auth_groups Name: "Tackled In Belt" => TS3GroupName: "Tackled In Belt".

**/// Auth Server Config ///**
The way the site handles acl, if you have an auth group that requires "director", you can have an auth role that has multiple acl permissions (so you dont need 100 roles for simple auth groups)
Role/Auth Group permissions are stored in json format. Example:
Auth Group with name of "XXXTREME GAY" and ACL of: ["gay", "nartle"]
Any role with permissions of: {"gay":true} or {"nartle":true} will match.
What this means is that the person will be added to the services group of XXXTREME GAY.

**/// phpBB Integration ///**
phpBB works kind of similar, in the sense of acl lookups and auth group management.
Difference is that my implementation uses the phpBB group "description" as a lookup for the auth group "slug".
An example of this is an auth_group name of "Tackled In Belt"
Slug will be "tackled_in_belt"
phpBB config will have a group named "Tackled In Belt", with a DESCRIPTION of "tackled_in_belt".
The auth implementation will retrieve the user's auth groups and then apply the forum groups accordingly. This is done on every login to the forums.
The forum permissions will follow the group, which you'll need to set up yourself. Read up on phpBB forum permissions to learn about those.

**/// IRC Integration ///**
Edit the config files, make sure you have a directory with "atheme.conf, atheme.db, services.db". Touch the databases if necessary.
Config files:
-tib.config.php
-atheme.conf
-inspircd.conf
Connect to the irc server, and register your first use with the same name as in your configs. (ex: /msg UserServ register GMGay secretpassinconfig tib@auth.tld)
Now that you've got your admin set up (it will auto-op/oper when needed), you should be set. Go back to the auth platform and hit generate channels. It'll auto-make the channels
set in your auth groups, take ownership and lock anyone from joining. I may add public channels, but for the time being just create a channel regularily.
The users will now be able to create their user, refresh roles on demand, etc.
If you'd like to add your own account a services admin, you can add it to the end of atheme.conf. Pretty self explainatory. I'd also suggest you add another operater in inspircd config as well, so you can /oper up if needed.


**/// Auth FAQ ///**
Q: How to create a new auth role.
A: Currently, I'm only using database based additions, so you'll need access to the mysql database. You'll see the roles in "roles" table, which are pretty self explainatory.

Q: Link an auth role with services group
A: There will be a superadmin option to create a services group to a role from the touch of a button. More superuser functions are coming.

Q: Add more corps to the database
A: Simple adjust the config list in tib.config.php. Corps added to this list will be auto-generated

Q: Lookup user's characters
A: As a director, you can access your corp member's APIs. These will only include api responses for now, but I may expand to include storage of api calls (transactions, mails, journal entries, contracts, etc)

Q: Lookup user's roles/acl
A: You can see the user's roles and ACL from the same page as you can see the characters. (wip)

Q: How often are API pulls for users?
A: Since I'm only refreshing the user's character list, It'll probably be fairly often (60min cache time)

Q: How to start?
A: install docker-compose, cd to directory, docker-compose up


**// Notable config files**
.env
config/tib.config.php
forum-config/config.php
sconfig/atheme/atheme.conf
sconfig/inspircd/inspircd.conf

