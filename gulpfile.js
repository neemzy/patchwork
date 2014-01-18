var gulp = require('gulp'),
    tasks = require('gulp-load-tasks')(),
    server = require('tiny-lr')();



gulp.task('rimraf', function () {
    gulp.src('public/assets/', { read: false })
        .pipe(tasks.rimraf());
});



gulp.task('css', function () {
    gulp.src('app/assets/less/*.less')
        .pipe(tasks.less())
        .pipe(tasks.autoprefixer())
        .pipe(tasks['if'](gulp.env.production, tasks.csso()))
        .pipe(gulp.dest('public/assets/css/'))
        .pipe(tasks.livereload(server));
});



gulp.task('js', function () {
    gulp.src('app/assets/js/**/*.js')
        .pipe(tasks.jshint())
        .pipe(tasks.jshint.reporter('default'))
        .pipe(tasks.browserify())
        .pipe(tasks['if'](gulp.env.production, tasks.uglify()))
        .pipe(gulp.dest('public/assets/js/'))
        .pipe(tasks.livereload(server));

    gulp.src('vendor/neemzy/patchwork-core/assets/js/nicEdit.js')
        .pipe(gulp.dest('public/assets/js/'));
});



gulp.task('img', function () {
    gulp.src(['app/assets/img/**/*', 'vendor/neemzy/patchwork-core/assets/img/**/*'])
        .pipe(tasks['if'](gulp.env.production, tasks.imagemin({ interlaced: true, progressive: true })))
        .pipe(gulp.dest('public/assets/img/'))
        .pipe(tasks.livereload(server));
});



gulp.task('font', function () {
    var from = 'app/assets/font/*.ttf',
        to = 'public/assets/font/';

    gulp.src(from, { buffer: false })
        .pipe(gulp.dest(to));

    gulp.src(from, { buffer: false })
        .pipe(tasks.ttf2woff())
        .pipe(gulp.dest(to));

    gulp.src(from, { buffer: false })
        .pipe(tasks.ttf2eot())
        .pipe(gulp.dest(to));
});



gulp.task('icon', function () {
    gulp.src(['node_modules/bootstrap/fonts/*', '!node_modules/bootstrap/fonts/*.svg'], { buffer: false })
        .pipe(gulp.dest('public/assets/font/'))
});



gulp.task('workflow', function () {
    gulp.src('gulpfile.js')
        .pipe(tasks.open('', { url: 'http://www.patch.work/' }));

    server.listen(35729, function (err) {
        gulp.watch('app/assets/less/**/*.less', function () {
            gulp.run('css');
        });

        gulp.watch('app/assets/js/**/*.js', function () {
            gulp.run('js');
        });

        gulp.watch('app/assets/img/**/*', function () {
            gulp.run('img');
        });

        gulp.watch('app/views/**/*.twig', function () {
            gulp.src('').pipe(tasks.livereload(server));
        });
    });
});



gulp.task('default'/*, ['rimraf']*/, function() {
    gulp.run('css', 'js', 'img', 'font', 'icon');
    gulp.env.production || gulp.run('workflow');
});