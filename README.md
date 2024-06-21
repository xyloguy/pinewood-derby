# Simple web app for managing a pinewood derby.

## A few things to be aware of.

1. The `/admin` folder is protected via `.htaccess`. You can either delete the file or generate a password using http://localhost/htpasswd_gen.html (if you have started the server, otherwise open htpasswd_gen.html in your browser) then add that `user:password` string to the `htpasswd` file. The default user is `pinewood` and the password `derby`.
2. You'll need to create at least one Group before adding Racers, generating heats, and entering result data.
3. The main index is set up so that you could display it on a projector and have it show the current heat and up to two heats on deck.

## To start the server:

Simply pull the project and run `docker-compose up -d`
and visit http://localhost/admin in your browser.


## To stop the server:

run `docker-compose down`


## To enable SSL support

1. Generate Your Certificates
2. Add your certificates to the `certs` folder as `certs/fullchain.pem` and `certs/privkey.pem`.
3. Modify `docker-compose.yaml` uncommenting
   ``` 
   - ./ssl.conf:/etc/apache2/sites-enabled/000-default.conf
   - ./certs/fullchain.pem:/var/www/fullchain.pem
   - ./certs/privkey.pem:/var/www/privkey.pem
   ```
4. Modify `ssl.conf` to update `YOURDOMAIN` so that http->https redirection works.
5. Restart the service (stop and start the service again).