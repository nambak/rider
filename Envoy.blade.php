@servers(['dev' => ['ubuntu@3.34.196.57']])

@task('deploy', ['on' => 'dev'])
cd /var/www/rider
sudo git pull origin master
sudo composer install
@endtask
