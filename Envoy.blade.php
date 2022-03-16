@servers(['dev' => ['ubuntu@3.38.166.17']])

@task('deploy', ['on' => 'dev'])
cd /var/www/rider
sudo git pull origin master
sudo composer install
@endtask
