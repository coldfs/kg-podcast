#/bin/bash
php import.php
wget -nc -P audio/source -i commands/wget.list
bash commands/ffmpeg.list