FROM daynnnnn/docker-chromium-xvfb

RUN apt-get update
RUN apt-get install \
	composer \
	php-curl \
	php-zip \
	wget \
	libappindicator1 \
	libappindicator3-1 \
	lsb-release -y

ADD /composer.json /src/
ADD /composer.lock /src/
ADD /database /src/database

RUN cd /src;\
 composer install --ignore-platform-reqs --no-scripts

ADD / /src


RUN php /src/artisan dusk:update 76
RUN chmod +x /src/scripts/startup.sh
CMD ["/src/scripts/startup.sh"]