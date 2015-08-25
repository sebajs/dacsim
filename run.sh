docker run -it --rm --name dacsim -p 5000:5000 -v "$PWD":/usr/src/myapp -w /usr/src/myapp php:5.6-cli sh init.sh
