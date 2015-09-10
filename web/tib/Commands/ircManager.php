<?php


/*
	Authenticate: /oper conf('tib.config.ircadmin') conf('tib.conf.ircadminpass')

	/ Change the pass
	/msg UserServ RESETPASS {{username}} # will return a string with the password between the last whitespace and period Save this to database

	// do this to login as the server admin

	/ Create a user if needed, all public registration should be disabled
	/msg UserServ register {{username}} {{password}} test@tib.com # This will log you in, shouldn't affect much though.
	// Response will be "already registered" or "user is now registered". Use these strings in php lookup

	// Always log out before making new groups. We want to create groups with the first user.

	/msg UserServ logout
	/msg UserServ login conf('tib.config.ircadmin') conf('tib.conf.ircadminpass')
	// Create a new channel and secure it
	/j #channel
	/msg ChanServ REGISTER #channel
	/msg ChanServ SET #channel MLOCK +s
	/msg ChanServ SET #channel restricted on

	// to add a user to access it, first removing a ban if it exists
	/msg ChanServ CLEAR #channel BANS
	/msg ChanServ ACCESS #channel ADD {{username}} VOP

	// Remove access to a user, this will be used to run through the auth groups the user doesn't have access to and delete them. Ignoring errors will be ok
	/msg ChanServ ACCESS #atheme DEL stitch

	// Instruct users to login via using /msg UserServ login {{username}} {{password}}. Can be done automatically in hexchat. Pidgin can do this in buddy pounces.

	

*/