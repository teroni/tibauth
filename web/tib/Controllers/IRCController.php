<?php
namespace Tib\Controllers;

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

use App\Http\Requests;
use App\Http\Controllers\Controller;
use \Sentinel;
use Illuminate\Http\Request;
use \Redirect;
use \Input;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Config;

use Phergie\Irc\Event\EventInterface as IRCEvent;
use Phergie\Irc\Bot\React\EventQueueInterface as IRCQueue;
use Phergie\Irc\Bot\React\PluginInterface;
use Phergie\Irc\Connection;

class IRCController extends Controller {
	public function __construct()
	{
		$this->server = array(); //we will use an array to store all the server data. 
    	//Open the socket connection to the IRC server 
    	if(!$this->server['SOCKET'] = @fsockopen('services.tackledinbelt.com', 6667, $errno, $errstr, 2))
    	{
    		sleep(5);
    		$this->server['SOCKET'] = @fsockopen('services.tackledinbelt.com', 6667, $errno, $errstr, 2);
    	}
   		if(@!$user->name)
		{
			$this->user = \Tib\Models\User::find(Sentinel::getUser()->id);
		}
		$this->authname = str_replace(' ', '', $this->user->first_name);
		$this->beforeFilter('auth');
	}

	public function getOwnchans()
	{
		if(!$this->user->hasAccess(['superuser']))
		{
			\Alert::warning("You're not supposed to do that.");
			return Redirect::to('services');
		}
		$commands = [];
		foreach(\Tib\Models\AuthGroups::get() as $group)
		{
			if($group->channels)
			{
				foreach(@json_decode(@$group->channels) as $chan)
				{
					$channame = "#". $chan;
					$commands[] = "JOIN ". $channame;
					$commands[] = "PRIVMSG ChanServ REGISTER ". $channame;
					$commands[] = "PRIVMSG ChanServ SET ". $channame. " MLOCK +s";
					$commands[] = "PRIVMSG ChanServ SET ". $channame. " restricted on";
				}
			}
		}
		$this->execute($commands, true);
	}
	public function getClaimchan($slug)
	{
		if(!$this->user->hasAccess(['superuser']))
		{
			\Alert::warning("You're not supposed to do that.");
			return Redirect::to('services');
		}
		$commands = [];
		foreach(\Tib\Models\AuthGroups::where('slug', '=', $slug)->get() as $group)
		{
			if($group->channels)
			{
				foreach(@json_decode(@$group->channels) as $chan)
				{
					$channame = "#". $chan;
					$commands[] = "JOIN ". $channame;
					$commands[] = "PRIVMSG ChanServ REGISTER ". $channame;
					$commands[] = "PRIVMSG ChanServ SET ". $channame. " MLOCK +s";
					$commands[] = "PRIVMSG ChanServ SET ". $channame. " restricted on";
				}
			}
		}
		$this->execute($commands, true);
		\Alert::info('Channel Claimed');
		return Redirect::to('roles');
	}
	public function getDeleteaccount()
	{
		
		$commands = array();
		$commands[] = "JOIN #tib";
		$commands[] = "PRIVMSG UserServ fdrop ". $this->authname;
		return $this->execute($commands, true); // Commands and if should login as admin or now
	}
	public function getCreateaccount()
	{
		if(\Cache::has($this->user->id. 'remakeacc'))
		{
			$now = \Carbon::now();
			$time = \Carbon::parse(\Cache::get($this->user->id. 'remakeacc'));
			@\Alert::error('You cannot refresh that often. Please try again in '. $now->diffInSeconds($time) . ' seconds');
			//return Redirect::to('services');
		} else {
			\Cache::put($this->user->id. 'remakeacc', \Carbon::now()->addSeconds(60)->toDateTimeString(), 1);
		}
		$commands = array();
		$commands[] = "JOIN #tib";
		$commands[] = "PRIVMSG UserServ logout";
		$commands[] = "PRIVMSG UserServ register " . $this->authname . " ". $this->user->irc_pass . ' false@email.tib';
		$this->execute($commands, false); // Commands and if should login as admin or now
		\Alert::info('IRC Account Remade. Refresh Permissions');
		return Redirect::to('services');
	}
	public function getReregister()
	{
		if(\Cache::has($this->user->id. 'remakeacc2'))
		{
			$now = \Carbon::now();
			$time = \Carbon::parse(\Cache::get($this->user->id. 'remakeacc2'));
			@\Alert::error('You cannot refresh that often. Please try again in '. $now->diffInSeconds($time) . ' seconds');
			return Redirect::to('services');
		} else {
			\Cache::put($this->user->id. 'remakeacc2', \Carbon::now()->addSeconds(60)->toDateTimeString(), 1);
		}
		$this->getDeleteaccount();	
		return Redirect::to('irc/createaccount');
	}
	public function getRefreshall()
	{
		if(!$this->user->hasAccess(['superuser']))
		{
			\Alert::warning("You're not supposed to do that.");
			return Redirect::to('services');
		}
		$commands = [];
		foreach(\Tib\Models\User::all() as $currentUser)
		{
			$authname = str_replace(' ', '', $currentUser->first_name);
			$approved = []; 
			foreach(\Tib\Models\AuthGroups::get() as $group)
			{
				if($currentUser->hasAccess(json_decode($group->acl)) && $group->channels)
				{
					foreach(@json_decode(@$group->channels) as $chan)
					{
						$approved[] = $chan;
						if($currentUser->hasAccess('superuser') || $group->role->users()->where('user_id', '=', $currentUser->id)->first()->pivot_owner) {
	                        $perm = "SOP";
	                    } elseif($currentUser->hasAccess('director') || $group->role->users()->where('user_id', '=', $currentUser->id)->first()->pivot_moderator)
	                    {
	                        $perm = "AOP";
	                    } else{
	                        $perm = "VOP";
	                    }
						$channame = "#". $chan;
						$commands[] = "PRIVMSG ChanServ CLEAR ". $channame. " BANS";
						$commands[] = "PRIVMSG ChanServ ACCESS ". $channame ." ADD ". $authname . " ". $perm;
					}
				} elseif(!$currentUser->hasAccess(json_decode($group->acl)) && $group->channels) {
					foreach(@json_decode(@$group->channels) as $chan)
					{
						if(!in_array($chan, $approved))
						{
							$channame = "#". $chan;
							$commands[] = "PRIVMSG ChanServ ACCESS ". $channame ." DEL ". $authname;
						}
					}
				}
			}
			
		}
		//print_r($commands);
		$this->execute($commands, true); // Commands and if should login as admin or now
		\Alert::info('IRC channel permissions for all set');
		return Redirect::to('services');
	}
	public function getRefresh($user = null)
	{
		if(\Cache::has($this->user->id. 'refreshirc'))
		{
			$now = \Carbon::now();
			$time = \Carbon::parse(\Cache::get($this->user->id. 'refreshirc'));
			@\Alert::error('You cannot refresh that often. Please try again in '. $now->diffInSeconds($time) . ' seconds');
			return Redirect::to('services');
		} else {
			\Cache::put($this->user->id. 'refreshirc', \Carbon::now()->addSeconds(60)->toDateTimeString(), 1);
		}
		$approved = [];
		foreach(\Tib\Models\AuthGroups::get() as $group)
		{
			if($this->user->hasAccess(json_decode($group->acl)) && $group->channels)
			{
				foreach(@json_decode(@$group->channels) as $chan)
				{
					$approved[] = $chan;
					if($this->user->hasAccess('superuser') || $group->role->users()->where('user_id', '=', $this->user->id)->first()->pivot_owner) {
                        $perm = "SOP";
                    } elseif($this->user->hasAccess('director') || $group->role->users()->where('user_id', '=', $this->user->id)->first()->pivot_moderator)
                    {
                        $perm = "AOP";
                    } else{
                        $perm = "VOP";
                    }
					$channame = "#". $chan;
					$commands[] = "PRIVMSG ChanServ CLEAR ". $channame. " BANS";
					$commands[] = "PRIVMSG ChanServ ACCESS ". $channame ." ADD ". $this->authname . " ". $perm;
				}
			} elseif(!$this->user->hasAccess(json_decode($group->acl)) && $group->channels) {
				foreach(@json_decode(@$group->channels) as $chan)
				{
					if(!in_array($chan, $approved))
					{
						$channame = "#". $chan;
						$commands[] = "PRIVMSG ChanServ ACCESS ". $channame ." DEL ". $this->authname;
					}
				}
			}
		}
		$this->execute($commands, true); // Commands and if should login as admin or now
		\Alert::info('IRC Channel Permissions Set');
		return Redirect::to('services');
	}

	function SendCommand ($cmd) 
	{ 
		try
		{
			//echo "SEND: ". $cmd . "<br>";
			@fwrite($this->server['SOCKET'], $cmd, strlen($cmd)); //sends the command to the server 	
		}
	    catch(\Exception $e)
	    {
	    	\Alert::warning("You're already in ". $role->name);
	    }
	}
	function execute($commands, $admin = false)
	{
		if($this->server['SOCKET']) 
	    { 
	        //Ok, we have connected to the server, now we have to send the login commands. 
	        $this->SendCommand("PASS NOPASS\n\r"); //Sends the password not needed for most servers 
	        $this->SendCommand("NICK ". str_random(8) ."\n\r"); //sends the nickname 
	        $this->SendCommand("USER ". str_random(8) ." USING PHP IRC\n\r"); //sends the user must have 4 paramters 
	        while(!feof($this->server['SOCKET'])) //while we are connected to the server 
	        { 
	            $this->server['READ_BUFFER'] = fgets($this->server['SOCKET'], 1024); //get a line of data from the server 
	            //echo "[RECIEVE] ".$this->server['READ_BUFFER']."<br>\n\r"; //display the recived data from the server 
	             
	            /* 
	            IRC Sends a "PING" command to the client which must be anwsered with a "PONG" 
	            Or the client gets Disconnected  
	            */ 
	            //Now lets check to see if we have joined the server 
	            if(strpos($this->server['READ_BUFFER'], "422")) //422 is the message number of the MOTD for the server (The last thing displayed after a successful connection) 
	            {
	            	if($admin)
	            	{
	            		$this->SendCommand("PRIVMSG UserServ : login ". config('tib.config.ircadmin') ." ". config('tib.config.irc_password') ."\n\r"); //Join the chanel
	            		$this->SendCommand("OPER ". config('tib.config.ircadmin') ." ". config('tib.config.irc_password') ."\n\r"); //Join the chanel
	            		$this->SendCommand("OPER : ". config('tib.config.ircadmin') ." ". config('tib.config.irc_password') ."\n\r"); //Join the chanel
	            		$this->SendCommand("OPER :". config('tib.config.ircadmin') ." ". config('tib.config.irc_password') ."\n\r"); //Join the chanel
	            		$this->SendCommand("OPER: ". config('tib.config.ircadmin') ." ". config('tib.config.irc_password') ."\n\r"); //Join the chanel
	            	}
	                foreach($commands as $command)
	                {
	                	$this->SendCommand($command."\n\r"); // Execute it
	                }
	                sleep(2);
	                $this->SendCommand("QUIT : CYA NOOBS\n\r"); //Join the chanel
	                break;
	            } 
	            if(substr($this->server['READ_BUFFER'], 0, 6) == "PING :") //If the server has sent the ping command 
	            { 
	                $this->SendCommand("PONG :".substr($server['READ_BUFFER'], 6)."\n\r"); //Reply with pong 
	                //As you can see i dont have it reply with just "PONG" 
	                //It sends PONG and the data recived after the "PING" text on that recived line 
	                //Reason being is some irc servers have a "No Spoof" feature that sends a key after the PING 
	                //Command that must be replied with PONG and the same key sent. 
	            } 
	            flush(); //This flushes the output buffer forcing the text in the while loop to be displayed "On demand" 
	        } 
	    } 
	}
}