@servers(['web' => 'craig@do'])

@task('load')
    cd /var/www/html/moviesowl.com/current
    php artisan movies:clearall
    php artisan movies:load
@endtask

@task('log')
    cd /var/www/html/moviesowl.com/current
    tail -f storage/logs/laravel.log
@endtask

@task('imdb')
    cd /var/www/html/moviesowl.com/current
    php artisan movies:imdb
@endtask


