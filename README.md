# Introduction

This is based on the code from the [*Docker for PHP Developers* tutorial on the *New Media Campaigns* site](http://www.newmediacampaigns.com/blog/docker-for-php-developers).

The tutorial progresses through a series of steps, but this code is where you'd be at the *end* of the tutorial. One side effect of this is that you'd want to use a more specific URI at the beginning (when there's just a static HTML page) vs. at the end of the article (when there's a dynamic PHP page).

* Static HTML page's URI for the beginning of the tutorial: http://docker.dev:8080/index.html
* Dynamic PHP page's URI for the end of the article: http://docker.dev:8080/ or http://docker.dev:8080/index.php

# Tweaks

Although this is based on code from the tutorial, I tweaked a few things based on feedback from the tutorial's comments, as well as workarounds for problems I encountered.

I used an alternate base box for nginx (to avoid the confusing `CMD` entrypoint that `phusion/baseimage` provides). (FYI, the Dockerfile from the tutorial didn't work for me--nginx wouldn't work after a `docker run`, so that's another reason I bagged it and went with another one.)

You'll see that there is no `images/nginx/start.sh`, as a result.

## `docker-machine` Instead of Direct `boot2docker` Incantations

I used the more modern `docker-machine`, instead of `boot2docker` commands.
Therefore, I installed Docker Toolbox, and *instead* of these commands:

```
# I didn't use these
boot2docker init
boot2docker up
eval "$(boot2docker shellinit)"
```

...I used the following `docker-machine` commands. (Also, I had problems in `cygwin`, so I bagged it and went with the cheesy `cmd` prompt, instead.)

```
docker-machine create --driver virtualbox nmc
```

Set up `nmc` box's context in `docker-machine`.

Get the command which will establish the `docker-machine` context:
```
docker-machine env nmc
```

In Windows, the command that establishes the `docker-machine` context (for the `nmc` docker machine) is the following. OSX/Linux will be different.
```
FOR /f "tokens=*" %i IN ('docker-machine env nmc') DO %i
```

## Optional Explicit VirtualBox Shared Folder

Windows users: If your project files live in `C:\Users`, then I think you'll be okay without the following explicit VirtualBox shared folder. I don't usually work out of my home directory, so I created a share.

If you want to do the same, do it now, before you set up any containers.

```
docker-machine stop nmc
# virtualbox needs to be on the path for this to work.
# otherwise, just add a share from the gui: folder path = "C:\www\nmc_docker" (or whatever) and folder name = "nmc"
VBoxManage sharedfolder add "nmc" --name "nmc" --hostpath "C:\www\nmc_docker" --automount
# it's *possible* you'll need to reset the context after this. i didn't.
docker-machine start nmc
docker-machine ssh nmc
sudo mkdir /nmc
# now you're in the `nmc` docker machine VM.
# TODO: are these are the right uid and gid?
# TODO: add to fstab for permanence
sudo mount -t vboxsf -o uid=1000,gid=50 nmc /nmc
exit
```

## `docker run`

By the end of the tutorial, you'll be using `docker-compose` instead of these direct `docker run` commands, but until you get to the `docker-compose` part, this is how to follow along.

Because of the different base box (which isn't Ubuntu-based, as `phusion/baseimage` is), the `vhost.conf` path is different, so the `docker run` command's volume mount path looks different.

Mine looks like this, because I don't work in my "home" directory.

```
docker run ^
    -d ^
    -p 8080:80 ^
    -v /nmc/src/vhost.conf:/etc/nginx/conf.d/vhost.conf ^
    -v /nmc/src:/var/www ^
    tutorial/nginx
```

If on Windows, and working within your home (`C:\Users\...`) directory, since you didn't need an explicit share/mapping, I *think* you'll use:
```
docker run ^
    -d ^
    -p 8080:80 ^
    -v /c/Users/your_user_name/path/to/nmc/project/src/vhost.conf:/etc/nginx/conf.d/vhost.conf ^
    -v /c/Users/your_user_name/path/to/nmc/project/src:/var/www ^
    tutorial/nginx
```

On OSX/Linux, you're lucky, as you can use the portable version from the tutorial (but with the same tweak for the vhost.conf path):

```
docker run \
    -d \
    -p 8080:80 \
    -v $(pwd)/src/vhost.conf:/etc/nginx/conf.d/vhost.conf \
    -v $(pwd)/src:/var/www \
    tutorial/nginx;
````

## `docker-compose`

[`docker-compose.yml`](./docker-compose.yml) is configured for my environment (notice the volumes beginning with `/nmc/`). You'll need to change those to match your own environment, as you did for the `docker-run` commands.