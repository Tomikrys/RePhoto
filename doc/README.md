Obsah adresáře src přesunout na hosting zavolat následující příkazy:
	1. composer install
	2. php init
	3. // V soboru src/common/config/main-local.php změnit přístupy k DB
	4. php yii migrate
	5. php yii elasticsearch/init

je potřeba mít nainstalovaný Python 2.7, PHP 7.1, MariaDB 10.2, Elasticsearch 6.2 a případná rozšíření požadovaná při spuštění: composer install

yii serve --docroot="frontend/web/"


composer config -g -- disable-tls true

python - numpy, cv2
pip install numpy
pip install opencv-python