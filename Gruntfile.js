module.exports = function( grunt ) {
	require( 'load-grunt-tasks' )( grunt );

	// Project configuration.
	grunt.initConfig( {
		// Package
		pkg: grunt.file.readJSON( 'package.json' ),

		// JSHint
		jshint: {
			all: [ 'Gruntfile.js', 'composer.json', 'package.json' ]
		},

		// PHP Code Sniffer
		phpcs: {
			application: {
				src: [
					'**/*.php',
					'!node_modules/**',
					'!vendor/**',
					'!wp-content/**'
				]
			},
			options: {
				bin: 'vendor/bin/phpcs',
				standard: 'phpcs.xml.dist',
				showSniffCodes: true
			}
		},

		// PHPLint
		phplint: {
			all: [ 'src/**/*.php' ]
		},

		// PHP Mess Detector
		phpmd: {
			application: {
				dir: 'src'
			},
			options: {
				bin: 'vendor/bin/phpmd',
				exclude: 'node_modules',
				reportFormat: 'xml',
				rulesets: 'phpmd.ruleset.xml'
			}
		},
		
		// PHPUnit
		phpunit: {
			options: {
				bin: 'vendor/bin/phpunit'
			},
			application: {

			}
		},

		// Compass
		compass: {
			build: {
				options: {
					sassDir: 'sass',
					cssDir: 'css'
				}
			}
		},

		// PostCSS
		postcss: {
			options: {
				map: {
					inline: false,
					annotation: false
				},

				processors: [
					require( 'autoprefixer' )( { browsers: 'last 2 versions' } )
				]
			},
			dist: {
				src: 'css/admin.css'
			}
		},

		// CSS min
		cssmin: {
			assets: {
				files: {
					'css/admin.min.css': 'css/admin.css',
				}
			}
		}
	} );

	// Default task(s).
	grunt.registerTask( 'default', [ 'jshint', 'phplint', 'phpmd', 'phpcs' ] );
	grunt.registerTask( 'assets', [ 'compass', 'postcss', 'cssmin' ] );
};
