# KIMAI2 HTTP REMOTE USER PLUGIN

A plugin for Kimai2 to use the X-REMOTE-USER variable to login to Kimai

If the User doesn't exist it creates a user object and assignes the USER Role

## Installation 
Install Kimai as usual, then: 
``` 
cd var/plugin 
git clone ... HttpRemoteAuthBundle
```
and you are done

## Usage

Open http://kimaihost/auth/remote with the X-REMOTE-USER and X-REMOTE-EMAIL headers set.

## Why this is usefull 

With this plugin its possible to combine Kimai with Authentication modules from your webserver. 
