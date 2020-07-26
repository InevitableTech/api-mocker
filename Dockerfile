FROM php:7.2-cli
RUN curl -sL https://deb.nodesource.com/setup_10.x | bash -
RUN apt-get install -y nodejs
RUN npm install -g nodemon

WORKDIR /app