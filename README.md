
![Scotch Box](https://scotch.io/wp-content/uploads/2015/11/scotch-box-2.5-share.png)


## WordPress Starter Template

This is a simple WP starter template meant to get you up and running with an empty custom theme and sick dev env with ScotchBox and gulp for a smooth workflow.

### System Stuff

- Ubuntu 14.04 LTS (Trusty Tahr)
- PHP 5.6
- Ruby 2.2.x
- Vim
- Git
- cURL
- GD and Imagick
- Composer
- Beanstalkd
- Node
- NPM
- Mcrypt

### Database Stuff
- MySQL
- PostgreSQL
- SQLite

## Get Started

* Download and Install Vagrant
* Download and Install Virtual Box
* Clone the Scotch Box [GitHub Repository](https://github.com/scotch-io/scotch-box)
* Run ``` vagrant up ```
* Access Your Project at  [http://192.168.33.10/][14]

## Basic Vagrant Commands


### Start or resume your server
```bash
vagrant up
```

### Pause your server
```bash
vagrant suspend
```

### Delete your server
```bash
vagrant destroy
```

### SSH into your server
```bash
vagrant ssh
```



## Database Access

### MySQL 

- Hostname: localhost or 127.0.0.1
- Username: root
- Password: root
- Database: scotchbox

### PostgreSQL

- Hostname: localhost or 127.0.0.1
- Username: root
- Password: root
- Database: scotchbox
- Port: 5432


### MongoDB

- Hostname: localhost
- Database: scotchbox
- Port: 27017


## SSH Access

- Hostname: 127.0.0.1:2222
- Username: vagrant
- Password: vagrant

## Mailcatcher

Just do:

```
vagrant ssh
mailcatcher --http-ip=0.0.0.0
```

Then visit:

```
http://192.168.33.10:1080
```

### Run Mailcatcher on every `vagrant up`

Add

```
# Mailcatcher
config.vm.provision "shell", inline: "/home/vagrant/.rbenv/shims/mailcatcher --http-ip=0.0.0.0", run: "always"
```

to your `Vagrantfile`, inside the `Vagrant.configure("2") do |config|` block. If your machine is already provisioned, you need to run `vagrant up --provision` (or `vagrant provision` on a running machine) once to get this working. After that, Mailcatcher will run on every `vagrant up`.



## Updating the Box

Although not necessary, if you want to check for updates, just type:

```bash
vagrant box outdated
```

It will tell you if you are running the latest version or not, of the box. If it says you aren't, simply run:

```bash
vagrant box update
```
