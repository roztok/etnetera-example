# Pozadavky #

  * OS Linux, idealne Debian
  * apache2.2 + mod\_php5
  * php 5.5 (vcetne php5-mysql, php5-curl)
  * mysql 5.5
  * php-github API client - https://github.com/KnpLabs/php-github-api
  * leolos controler framework - https://code.google.com/p/leolos/
  * subversion client

# Instalace #

Nize popsanou instalaci nepovazuji za provozni instalaci, tu by idealne zastresoval deb balicek... Nicmene je nutne aplikaci jednoduse "rozbehat" ;)

## 1. Stazeni aplikace a knihoven ##

Prepneme se do pracovniho adresare, kam budeme stahovat zdrojove kody (napr. /home/martin/myprojects/), a provedeme checkout ze svn.

```
$ svn checkout https://etnetera-example.googlecode.com/svn/trunk/ etnetera
```

checkout _leolos_ frameworku
```
$ svn checkout https://leolos.googlecode.com/svn/trunk/ leolos
```

checkout _github-api_ php knihovny
```
$ svn checkout https://github.com/KnpLabs/php-github-api/trunk github-api
```

Dalsi instalace github-api dle navodu na https://github.com/KnpLabs/php-github-api
ve skratce nize:
```
$ cd github
$ curl -s http://getcomposer.org/installer | php
$ ./composer.phar install
$ cd..
```

Jelikoz jsem nenasel moznost nastaveni parametru pri volani vypisu repozitare uzivatele, vytvoril jsem regulerni patch (lepsi uprava nez editace a kopirovani kodu...). Nyni je potreba jej aplikovat.
```
$ patch github-api/lib/Github/Api/User.php < etnetera/patch/github-api/User.patch
```

## 2. Priprava document root apache ##

Instalovat budeme standardne do _/var/www/_, nastaveni apache do _/etc/apache2/_

Vytvoreni adresare (nutno jako root)
```
# mkdir -p /var/www/etnetera
# chmod a+wx /var/www/etnetera
```

Prolinkovani sablon a konfigurace
```
$ ln -s `pwd`/etnetera/conf /var/www/etnetera/
$ ln -s `pwd`/etnetera/templ `pwd`/etnetera/src/
```

Prolinkovani _leolos_ frameworku
```
$ ln -s `pwd`/leolos/src `pwd`/etnetera/src/leolos
```

Prolinkovani _github-api_
```
$ ln -s `pwd`/github-api `pwd`/etnetera/src/github-api
```

Prolinkovani zdrojovych souboru do document rootu
```
$ ln -s `pwd`/etnetera/src/ /var/www/etnetera/server
```

Link na index.php
```
$ln -s /var/www/etnetera/server/index.php /var/www/etnetera/
```

## 3. Vytvoreni databaze ##

Create sql script databaze obsahuje vytvoreni databaze, tabulek a zaroven vytvori uzivatele _`frodo`_ s RO opravnenim - tohoto uzivatele pouziva aplikace
```
# cat etnetera/sql/searchlog_00_create.sql | mysql
```

Implicitni heslo je _`baggins`_.

## 4. Nastaveni apache ##

Prolinkovani nastaveni apache (nutno jako root)

```
# ln -s `pwd`/etnetera/conf/etnetera.httpd.conf /etc/apache2/conf-anabled/etnetera.conf
```

Provedeme restart apache
```
# /etc/init.d/apache2 restart
```

Implicitne je nyni aplikace dostupna na adrese: http://localhost/etnetera