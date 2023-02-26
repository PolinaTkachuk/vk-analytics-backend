# установка php 8.1 debain
FROM php:8.1-fpm
# Задаём текущую рабочую директорию указывающую, в каком каталоге выполнять процесс
WORKDIR /var/www
# установка гит, zip
RUN apt-get -y update && apt-get install -y apt-transport-https && apt-get -y upgrade && apt-get -y install git zip
RUN docker-php-ext-install mysqli pdo pdo_mysql
#установка композера
RUN php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');" \
&& php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;" \
&& php composer-setup.php \
&& php -r "unlink('composer-setup.php');"  \
&& mv composer.phar /usr/local/bin/composer

#Чтобы вместо инициализации нового проекта у тебя копировались в /var/www/app(внутри контейнера) содержимое папки, в которой располагается Dockerfile.
# копируем из исходной папки . в app контейнера
COPY . ./app

RUN chgrp -R +1000 /var/www/app
RUN chown "$user_name":"$user_name" ./app -R
RUN chmod ug+rwx,o+rx,o-w  /var/www/app -R

ARG user_name=POLINA
ARG UID=1000
ARG GID=1000
ARG user_home=/home/${user_name}
# создание активного пользователя и группы
RUN groupadd -g $GID dev \
  && useradd -u $UID --gid dev -m $user_name


USER ${UID}:${GID}

CMD php -S 0.0.0.0:80 -t ./app/public
