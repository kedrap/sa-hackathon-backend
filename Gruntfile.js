'use strict';

module.exports = function(grunt) {
    grunt.initConfig({
        php: {
            dev: {
                options: {
                    hostname: 'localhost',
                    port: 8000,
                    base: 'web',
                    open: true,
                    keepalive: true
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-php');

    grunt.registerTask('serve', [
        'php:dev'
    ]);
};
