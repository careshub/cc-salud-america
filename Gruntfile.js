'use strict';
module.exports = function(grunt) {

    // load all grunt tasks matching the `grunt-*` pattern
    require('load-grunt-tasks')(grunt);

    grunt.initConfig({
		pkg: grunt.file.readJSON('package.json'),

        // watch for changes and trigger sass, jshint, uglify and livereload
        watch: {
            options: {
                livereload: true,
            },
            scripts: {
                files: 'public/js/public.js',
                tasks: ['uglify']
            },
            styles: {
				files: ['public/less/*.less'],
                tasks: ['less:cleancss', 'autoprefixer']
            },
            // cssautoprefix: {
            //     files: ['public/less/*.less'],
            //     tasks: ['autoprefixer']
            // },
            // images: {
            //     files: ['img/**/*.{png,jpg,gif}'],
            //     tasks: ['imagemin']
            // },
            // livereload: {
            //     options: { livereload: true },
            //     files: ['style.css', 'js/*.js', 'img/**/*.{png,jpg,jpeg,gif,webp,svg}']
            // }
        },

		less: {
		  cleancss: {
			options: {
			  // paths: ["css"],
			  cleancss: true,
			},
			files: {
				"public/css/public.css": "public/less/public.less",
                "public/css/public-ie.css": "public/less/public-ie.less"
			}
		  }
		},

        // autoprefixer
        autoprefixer: {
            options: {
                // browsers: ['last 2 versions', 'ie 9', 'ios 6', 'android 4'],
                map: true
            },
            files: {
                expand: true,
                flatten: true,
                src: 'public/css/*.css',
                dest: 'public/css/' //replaces source file
            },
        },

        // css minify
        // Using the "cleancss" option in less for this
        // cssmin: {
        //     options: {
        //         keepSpecialComments: 1
        //     },
        //     minify: {
        //         expand: true,
        //         cwd: 'public/css',
        //         src: ['*.css', '!*.min.css'],
        //         dest: 'public/css',
        //         ext: '.min.css'
        //     }
        // },

        // javascript linting with jshint
        jshint: {
            options: {
                jshintrc: '.jshintrc',
                "force": true
            },
            all: [
                'Gruntfile.js',
                'public/js/*.js'
                ]
        },

        // uglify to concat, minify, and make source maps
        uglify: {
			options: {
				banner: '/*! <%= pkg.name %> - v<%= pkg.version %> - ' +
						'<%= grunt.template.today("yyyy-mm-dd") %> */'
			},
			common: {
				files: {
					'public/js/public.min.js': 'public/js/public.js'
				}
			}
        },
        // image optimization
        imagemin: {
            dist: {
                options: {
                    optimizationLevel: 7,
                    progressive: true,
                    interlaced: true
                },
                files: [{
                    expand: true,
                    cwd: 'img/',
                    src: ['**/*.{png,jpg,gif}'],
                    dest: 'img/'
                }]
            }
        },

    });



    // Register tasks
	// Typical run, cleans up css and js
    grunt.registerTask('default', ['less:cleancss', 'autoprefixer', 'uglify:common', 'watch']);
    // Before releasing a build, do above plus minimize all images
	grunt.registerTask('build', ['less:cleancss', 'autoprefixer',  'uglify:common', 'imagemin']);

};