var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
    Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
    // directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // only needed for CDN's or sub-directory deploy
    //.setManifestKeyPrefix('build/')

    /*
     * ENTRY CONFIG
     *
     * Add 1 entry for each "page" of your app
     * (including one that's included on every page - e.g. "app")
     *
     * Each entry will result in one JavaScript file (e.g. app.js)
     * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
     */
    .addEntry('app', [
        './assets/js/app.js',
        './assets/css/app.scss',
    ])
    .addEntry('map', [
        './assets/js/map.js',
        './assets/css/map.scss',
    ])
    .addEntry('listing', [
        './assets/js/map.js',
        './assets/css/map.scss',
    ])
    .addEntry('adminLte', [
        './node_modules/admin-lte/plugins/jquery/jquery.min.js',
        './node_modules/admin-lte/plugins/jquery-ui/jquery-ui.min.js',
        './node_modules/admin-lte/plugins/bootstrap/js/bootstrap.bundle.min.js',
        './node_modules/admin-lte/plugins/chart.js/Chart.min.js',
        './node_modules/admin-lte/plugins/sparklines/sparkline.js',
        './node_modules/admin-lte/plugins/jqvmap/jquery.vmap.min.js',
        './node_modules/admin-lte/plugins/jqvmap/maps/jquery.vmap.usa.js',
        './node_modules/admin-lte/plugins/jquery-knob/jquery.knob.min.js',
        './node_modules/admin-lte/plugins/daterangepicker/daterangepicker.js',
        './node_modules/admin-lte/plugins/tempusdominus-bootstrap-4/js/tempusdominus-bootstrap-4.min.js',
        './node_modules/admin-lte/plugins/summernote/summernote-bs4.min.js',
        './node_modules/admin-lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js',
        './node_modules/admin-lte/dist/js/adminlte.js',
        './node_modules/admin-lte/dist/js/pages/dashboard.js',
        './node_modules/admin-lte/plugins/fontawesome-free/css/all.min.css',
        './node_modules/admin-lte/plugins/tempusdominus-bootstrap-4/css/tempusdominus-bootstrap-4.min.css',
        './node_modules/admin-lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css',
        './node_modules/admin-lte/plugins/jqvmap/jqvmap.min.css',
        './node_modules/admin-lte/dist/css/adminlte.min.css',
        './node_modules/admin-lte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css',
        './node_modules/admin-lte/plugins/daterangepicker/daterangepicker.css',
        './node_modules/admin-lte/plugins/summernote/summernote-bs4.css',
    ])
    // .addEntry('app', './assets/css/app.scss')

    // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
    // .splitEntryChunks()

    // will require an extra script tag for runtime.js
    // but, you probably want this, unless you're building a single-page app
    .enableSingleRuntimeChunk()

    /*
     * FEATURE CONFIG
     *
     * Enable & configure other features below. For a full
     * list of features, see:
     * https://symfony.com/doc/current/frontend.html#adding-more-features
     */
    .cleanupOutputBeforeBuild()
    .enableBuildNotifications()
    .enableSourceMaps(!Encore.isProduction())
    // enables hashed filenames (e.g. app.abc123.css)
    .enableVersioning(Encore.isProduction())

    // enables @babel/preset-env polyfills
    .configureBabelPresetEnv((config) => {
        config.useBuiltIns = 'usage';
        config.corejs = 3;
    })

    // enables Sass/SCSS support
    .enableSassLoader()

    // uncomment if you use TypeScript
    //.enableTypeScriptLoader()

    // uncomment to get integrity="..." attributes on your script & link tags
    // requires WebpackEncoreBundle 1.4 or higher
    //.enableIntegrityHashes(Encore.isProduction())

    // uncomment if you're having problems with a jQuery plugin
    .autoProvidejQuery()

    // uncomment if you use API Platform Admin (composer req api-admin)
    //.enableReactPreset()
    //.addEntry('admin', './assets/js/admin.js')

     .copyFiles({
        from: './assets/images',

        // optional target path, relative to the output dir
        //to: 'images/[path][name].[ext]',

        // if versioning is enabled, add the file hash too
        to: 'images/[path][name].[hash:8].[ext]',

        // only copy files matching this pattern
        //pattern: /\.(png|jpg|jpeg)$/
    })
;

module.exports = Encore.getWebpackConfig();
