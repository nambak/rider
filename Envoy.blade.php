@servers(['dev' => ['ubuntu@3.36.57.29']])

@task('deploy', ['on' => 'dev'])
cd /var/www/rider
sudo git pull origin master
sudo composer install
@endtask
