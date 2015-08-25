SOCK=`php -m | grep sockets`

if [ $SOCK"x" = "x" ]; then
	docker-php-ext-install sockets
fi

php dacsim.php -h 0.0.0.0 -p 5000