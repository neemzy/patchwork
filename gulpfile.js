var gulp = require('gulp'),
    tasks = require('gulp-load-tasks')(),
    rimraf = require('rimraf'),
    server = require('tiny-lr')();



gulp.task('clean', function (callback) {
    rimraf.sync('public/assets/');
    callback();
});



gulp.task('css', function () {
    gulp.src('app/assets/less/*.less')
        .pipe(tasks.less())
        .pipe(tasks.autoprefixer())
        .pipe(tasks.if(tasks.util.env.dist, tasks.csso()))
        .pipe(gulp.dest('public/assets/css/'))
        .pipe(tasks.livereload(server));
});



gulp.task('js', function () {
    gulp.src('app/assets/js/**/*.js')
        .pipe(tasks.jshint())
        .pipe(tasks.jshint.reporter('default'))
        .pipe(tasks.browserify())
        .pipe(tasks.if(tasks.util.env.dist, tasks.uglify()))
        .pipe(gulp.dest('public/assets/js/'))
        .pipe(tasks.livereload(server));

    gulp.src('vendor/neemzy/patchwork-core/assets/js/nicEdit.js')
        .pipe(gulp.dest('public/assets/js/'));
});



gulp.task('img', function () {
    gulp.src(['app/assets/img/**/*', 'vendor/neemzy/patchwork-core/assets/img/**/*'])
        .pipe(tasks.if(tasks.util.env.dist, tasks.imagemin({ interlaced: true, progressive: true })))
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
    if (! tasks.util.env.dist) {
        gulp.src('gulpfile.js')
            .pipe(tasks.open('', { url: 'http://www.patch.work/' }));

        server.listen(35729, function (err) {
            gulp.watch('app/assets/less/**/*.less', ['css']);
            gulp.watch('app/assets/js/**/*.js', ['js']);
            gulp.watch('app/assets/img/**/*', ['img']);

            gulp.watch('app/views/**/*.twig', function () {
                gulp.src('').pipe(tasks.livereload(server));
            });
        });
    }
});



gulp.task('default', ['clean', 'css', 'js', 'img', 'font', 'icon', 'workflow']);