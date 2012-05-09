group { "puppet":
    ensure => "present",
}

File { owner => 0, group => 0, mode => 0644 }


# Ensure that apt-get update has been run, else not all packages will install
exec { 'update-apt':
    command => "/usr/bin/apt-get update",
}

# install vim and all packages required to build PHP
$packages = [ "vim", "curl", "libxpm-dev", "libmcrypt-dev", "libbz2-dev", "libcurl4-gnutls-dev", "libjpeg62-dev", "libpng12-dev", "libfreetype6-dev", "libt1-dev", "libgmp3-dev", "libmysqlclient-dev", "libpq-dev", "libpcre3-dev" ]

package { $packages :
    ensure => latest,  # Don't use latest, or it will auto-update (or do if you like)
    require => Exec["update-apt"],
}

# Update .bashrc
$serial = "2012043001"
$serialfile = "/var/log/pe-bashrc-update.serial"
exec { "install-bashrc-update":
    command => "/bin/cat /vagrant/puppet/scripts/pe.sh >> /home/vagrant/.bashrc \
                && /bin/echo \"$serial\" > \"$serialfile\"",
    unless  => "/usr/bin/test \"`/bin/cat $serialfile 2> /dev/null`\" = \"$serial\"",
}

