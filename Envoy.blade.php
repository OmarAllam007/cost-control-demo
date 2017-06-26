@servers(['web' => 'app@kps.alkifahcont.com', 'dev' => 'app@192.168.90.33'])

@task('deploy', ['on' => 'web'])
    cd cost-control
    git pull origin master
@endtask

@task('deploy-dev', ['on' => 'dev'])
    cd cost-control
    git pull origin develop
@endtask

@task('migrate')
    cd cost-control
    php artisan optimize
    php artisan migrate
@endtask

@task('cache:clear')
    cd cost-control
    php artisan cache:clear
@endtask

@task('composer')
    cd cost-control
    composer install
@endtask
