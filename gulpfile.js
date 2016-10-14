const elixir = require('laravel-elixir');

require('laravel-elixir-vue-2');

Elixir.ready(function () {
	Elixir.webpack.mergeConfig({
		module: {
			loaders: [
				{
					test: /\.js$/,
					loader: 'babel',
					exclude: /node_modules/
				},
				{
					test: /\.css$/,
					loader: 'style!css'
				}
			]
		}
	})
});

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(mix => {
	mix.sass('app.scss')
		.scripts('prism.js')
		.webpack('app.js');
});