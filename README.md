# Simple web app for managing a pinewood derby.

## A few things to be aware of.

1. The `/admin` folder is protected via `.htaccess`. You can either delete the file or generate a password using http://localhost/gen_password.php then add that user:password string to the .htpasswd file. The default user is `pinewood` and the password `derby`.
2. You'll need to create at least one Group before adding Racers, generating heats, and entering result data.
3. The main index is set up so that you could display it on a projector and have it show the current heat and up to two heats on deck.

## To start:

Simply pull the project and run `docker compose up -d`
and visit http://localhost/admin in your browser.


## To stop the server:

run `docker compose down`