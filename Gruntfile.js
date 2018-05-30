'use strict';

module.exports = function(grunt) {

	require('load-grunt-tasks')(grunt);

	// Load shared tasks library
	var wpmudev = require('./shared-tasks/loader')(grunt);
// Grunt config
	grunt.initConfig({
		// Read package meta data
		pkg: grunt.file.readJSON('package.json'),
	});

	grunt.registerTask('package', ['wpmudev_cleanup', 'wpmudev_copy:full', 'wpmudev_compress']);

	grunt.registerTask('release', function (version) {
		grunt.config.set('wpmudev_release', {
			type: 'full',
			version: version,
			version_define: 'WDCP_PLUGIN_VERSION',
			build: ['package'],
			cleanup: ['wpmudev_cleanup'],
		});
		grunt.task.run('wpmudev_release');
	});
};
