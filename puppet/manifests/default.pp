group { "puppet":
    ensure => "present",
}

File { owner => 0, group => 0, mode => 0644 }

exec { 'update-apt':
    command => "/usr/bin/apt-get update",
}

Exec["update-apt"] -> Package <| |>

package { 'vim':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'curl':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libxpm-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libmcrypt-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libbz2-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libcurl4-gnutls-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libjpeg62-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libpng12-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libfreetype6-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libt1-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libgmp3-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libmysqlclient-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libpq-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

package { 'libpcre3-dev':
    ensure => latest,
    require => Exec["update-apt"],
}

$serial = "2012043001"
$serialfile = "/var/log/pe-bashrc-update.serial"
exec { "install-bashrc-update":
    command => "/bin/cat /vagrant/puppet/scripts/pe.sh >> /home/vagrant/.bashrc \
                && /bin/echo \"$serial\" > \"$serialfile\"",
    unless  => "/usr/bin/test \"`/bin/cat $serialfile 2> /dev/null`\" = \"$serial\"",
}

