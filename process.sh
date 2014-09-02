#/bin/bash
php import.php $1 $2
wget -nc -P audio/source -i commands/wget.list
bash commands/ffmpeg.list
#Очщаем папки
#rm -rfv audio/source/*
#rm -rfv commands/*
#rm -rfv commands/*
