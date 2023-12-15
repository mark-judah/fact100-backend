<?php

if(!function_exists('config_path'))
{
        /**
        * Return the path to config files
        * @param null $path
        * @return string
        */
        function config_path($path=null)
        {
                return app()->getConfigurationPath(rtrim($path, ".php"));
        }
}

if(!function_exists('public_path'))
{

        /**
        * Return the path to public dir
        * @param null $path
        * @return string
        */
        function public_path($path=null)
        {
                return rtrim(app()->basePath('public/'.$path), '/');
        }
}

if(!function_exists('storage_path'))
{

        /**
        * Return the path to storage dir
        * @param null $path
        * @return string
        */
        function storage_path($path=null)
        {
                return app()->storagePath($path);
        }
}

if(!function_exists('database_path'))
{

        /**
        * Return the path to database dir
        * @param null $path
        * @return string
        */
        function database_path($path=null)
        {
                return app()->databasePath($path);
        }
}

if(!function_exists('resource_path'))
{

        /**
        * Return the path to resource dir
        * @param null $path
        * @return string
        */
        function resource_path($path=null)
        {
                return app()->resourcePath($path);
        }
}

if(!function_exists('lang_path'))
{

        /**
        * Return the path to lang dir
        * @param null $str
        * @return string
        */
        function lang_path($path=null)
        {
                return app()->getLanguagePath($path);
        }
}

if ( ! function_exists('asset'))
{
    /**
     * Generate an asset path for the application.
     *
     * @param  string  $path
     * @param  bool    $secure
     * @return string
     */
    function asset($path, $secure = null)
    {
        return app('url')->asset($path, $secure);
    }
}

if ( ! function_exists('elixir'))
{
    /**
     * Get the path to a versioned Elixir file.
     *
     * @param  string  $file
     * @return string
     */
    function elixir($file)
    {
        static $manifest = null;
        if (is_null($manifest))
        {
            $manifest = json_decode(file_get_contents(public_path().'/build/rev-manifest.json'), true);
        }
        if (isset($manifest[$file]))
        {
            return '/build/'.$manifest[$file];
        }
        throw new InvalidArgumentException("File {$file} not defined in asset manifest.");
    }
}
