/**
 * Compile Dist.
 *
 * @package Boilderplate
 *
 * @since 1.0.0
 */

/* global del, dist, gulp, handleErrors, paths, plumber */

/**
 * Delete the dist directory.
 *
 * @since 1.0.0
 */
gulp.task( 'cleanDist', () =>
	del([ paths.dist ])
);

 /**
  * Copy files to the dist directory.
  *
  * @since 1.0.0
  */
gulp.task( 'copy', [ 'cleanDist' ], () =>
	gulp.src( dist )
		.pipe( plumber({'errorHandler': handleErrors}) )
		.pipe( gulp.dest( paths.dist ) )
);

/**
  * Build Dist.
  *
  * @since 1.0.0
  */
gulp.task( 'build', [ 'copy' ]);
