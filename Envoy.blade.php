@servers(['web' => 'craig@do'])

@task('migrate')
    cd /var/www/html/moviesowl.com/current
    php artisan migrate
@endtask

@task('clearall')
    cd /var/www/html/moviesowl.com/current
    php artisan movies:clearall
@endtask

@task('load')
    cd /var/www/html/moviesowl.com/current
    php artisan movies:clearall
    php artisan movies:load
@endtask

@task('tail')
    cd /var/www/html/moviesowl.com/current
    tail -f storage/logs/laravel.log
@endtask

@task('imdb')
    cd /var/www/html/moviesowl.com/current
    php artisan movies:imdb
@endtask


