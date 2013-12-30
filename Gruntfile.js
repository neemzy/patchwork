module.exports = function(grunt)
{
    // Configuration

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        clean: ['public/assets/'],

        phpunit: {
            options: {
                bin: 'vendor/bin/phpunit',
                colors: true
            },

            default: {
                dir: 'app/tests/'
            }
        },



        copy: {
            js: {
                files: {
                    'public/assets/js/main.js': 'app/assets/js/main.js',
                    'public/assets/js/nicEdit.js': 'vendor/neemzy/patchwork-core/assets/js/nicEdit.js',
                    'public/assets/img/nicEditorIcons.gif': 'vendor/neemzy/patchwork-core/assets/img/nicEditorIcons.gif'
                }
            },

            img: {
                files: [{
                    expand: true,
                    cwd: 'app/assets/img/',
                    src: '*.{png,jpg,gif}',
                    dest: 'public/assets/img/'
                }]
            },

            font: {
                files: [{
                    expand: true,
                    cwd: 'app/assets/font/',
                    src: '*.ttf',
                    dest: 'public/assets/font/'
                }]
            },

            icon: {
                files: [{
                    expand: true,
                    cwd: 'bower_components/bootstrap/dist/fonts/',
                    src: '*.{ttf,eot,woff}',
                    dest: 'public/assets/font/'
                }]
            }
        },



        less: {
            default: {
                files: {
                    'public/assets/css/front.css': 'app/assets/less/front/main.less',
                    'public/assets/css/admin.css': 'app/assets/less/admin.less'
                }
            }
        },

        autoprefixer: {
            no_dest: {
                src: 'public/assets/css/front.css'
            }
        },

        csso: {
            default: {
                files: [{
                    expand: true,
                    cwd: 'public/assets/css/',
                    src: '*.css',
                    dest: 'public/assets/css/'
                }]
            }
        },



        jshint: {
            default: ['app/assets/js/main.js']
        },

        uglify: {
            default: {
                files: [{
                    expand: true,
                    cwd: 'public/assets/js/',
                    src: '*.js',
                    dest: 'public/assets/js/'
                }]
            }
        },



        imagemin: {
            default: {
                files: [{
                    expand: true,
                    cwd: 'public/assets/img/',
                    src: ['**/*.{png,jpg,gif}'],
                    dest: 'public/assets/img/'
                }]
            }
        },



        ttf2eot: {
            default: {
                src: 'public/assets/font/*.ttf',
                dest: 'public/assets/font/'
            }
        },

        ttf2woff: {
            default: {
                src: 'public/assets/font/*.ttf',
                dest: 'public/assets/font/'
            }
        },



        open: {
            default: {
                path: 'http://www.patch.work/'
            }
        },

        watch: {
            css: {
                files: 'app/assets/less/**/*.less',
                tasks: ['css']
            },

            js: {
                files: 'app/assets/js/**/*.js',
                tasks: ['js']
            },

            livereload: {
                options: {
                    livereload: true
                },

                files: ['public/assets/**/*', 'app/views/**/*'],
            }
        },

        concurrent: {
            font: ['ttf2eot', 'ttf2woff']
        }
    });



    // Tasks

    require('load-grunt-tasks')(grunt);

    grunt.registerTask('css', ['less', 'autoprefixer']);
    grunt.registerTask('js', ['jshint', 'copy:js']);
    grunt.registerTask('img', ['copy:img']);
    grunt.registerTask('font', ['copy:font', 'concurrent:font', 'copy:icon']);

    grunt.registerTask('common', ['clean', 'phpunit', 'css', 'js', 'img', 'font']);
    grunt.registerTask('dev', ['open', 'watch']);
    grunt.registerTask('prod', ['csso', 'uglify', 'imagemin'])

    grunt.registerTask('default', ['common', 'dev']);
    grunt.registerTask('dist', ['common', 'prod']);
};