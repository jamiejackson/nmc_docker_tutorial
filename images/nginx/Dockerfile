FROM debian:jessie

# rip-off of https://github.com/nginxinc/docker-nginx/blob/19799fa644461ed6c5ea07c0bc0ea0cc277c2d77/mainline/jessie/Dockerfile

ENV NGINX_VERSION 1.9.14-1~jessie

RUN apt-key adv --keyserver hkp://pgp.mit.edu:80 --recv-keys 573BFD6B3D8FBC641079A6ABABF5BD827BD9BF62 \
	&& echo "deb http://nginx.org/packages/mainline/debian/ jessie nginx" >> /etc/apt/sources.list \
	&& apt-get update \
	&& apt-get install --no-install-recommends --no-install-suggests -y \
						ca-certificates \
						nginx=${NGINX_VERSION} \
						nginx-module-xslt \
						nginx-module-geoip \
						nginx-module-image-filter \
						nginx-module-perl \
						nginx-module-njs \
						gettext-base \
	&& rm -rf /var/lib/apt/lists/*

# forward request and error logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log \
	&& ln -sf /dev/stderr /var/log/nginx/error.log

EXPOSE 80 443

# the phusion version from the tutorial used CMD ["/sbin/my_init"], but it 
#  didn't start the service properly, and it was roundabout and confusing, to
#  boot. i think doing it this straightforward way makes it more clear to a
#  beginner (like me :).
CMD ["nginx", "-g", "daemon off;"]