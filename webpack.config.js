var Encore = require('@symfony/webpack-encore');

Encore
    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .addEntry("gallery/main", [
        "./assets/gallery/main.scss",
        "./assets/gallery/main.js"
    ])
    .addEntry('app', "./assets/app.js")
    .enableVersioning()
    .enableSourceMaps(!Encore.isProduction())
    .enableSassLoader()
    .autoProvidejQuery()
    .enableReactPreset()
    .enableSingleRuntimeChunk()
    // uncomment to create hashed filenames (e.g. app.abc123.css)
    // .enableVersioning(Encore.isProduction())

    // uncomment to define the assets of the project
    // .addEntry('js/app', './assets/js/app.js')
    // .addStyleEntry('css/app', './assets/css/app.scss')
;

module.exports = Encore.getWebpackConfig();
