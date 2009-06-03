<?php
class ZendX_Doctrine_Application_Resource_Doctrine extends Zend_Application_Resource_ResourceAbstract
{
    /**
     * @var Doctrine_Manager
     */
    protected $_manager = null;

    /**
     * Initialize Doctrine
     *
     * @return bool
     */
    public function init()
    {
        // register Doctrine namespace
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace('Doctrine');

        // @bug Model class loading fails without fallback autoloader
        // @see
        // http://www.nabble.com/Autoload-models-with-no-namespace-to23387744.html
        //
        // we could disable fallback autoloader and use:
        // Doctrine::loadModels($options['paths']['models_path']);
        // but it works only with MODEL_LOADING_AGGRESIVE (default, but slower),
        // so always use resources.doctrine.attributes.model_loading = "conservative"

        Zend_Loader_Autoloader::getInstance()->setFallbackAutoloader(true);

        $options = &$this->_options;

        if ((is_array($options)) || (!empty($options))) {

            if ((isset($options['debug'])) && (!empty($options['debug']))) {
                $this->getDebug($options['debug']);
            }

            if ((isset($options['attributes'])) && (!empty($options['attributes']))) {
                $this->getAttributes($options['attributes']);
            }

            $this->getPaths();
            // Doctrine::loadModels($options['paths']['models_path']);

            if ((isset($options['connections'])) && (!empty($options['connections']))) {
                $this->getConnections($options['connections']);
            }

            if ((isset($options['session'])) && (!empty($options['session']))) {
                $this->setSessionHandler($options['session']);
            }
        }

        return $this->getManager();
    }

    /**
     * Retrieve manager instance
     *
     * @return Doctrine_Manager
     * @throws ZendX_Doctrine_Application_Resource_Exception If unable to retrieve manager instance.
     */
    public function getManager()
    {
        if (NULL === $this->_manager) {
            try {
                $this->_manager = Doctrine_Manager::getInstance();
            } catch (Doctrine_Exception $e) {
                throw new
                ZendX_Doctrine_Application_Resource_Exception('Unable to retrieve Doctrine_Manager instance.');
            }
        }

        return $this->_manager;
    }

    /**
     * Set global attributes
     *
     * @param array $attributes
     * @return ZendX_Doctrine_Application_Resource_Doctrine
     */
    public function getAttributes(array $attributes = array())
    {
        foreach($attributes as $key => $value) {
            $this->getManager()->setAttribute($key, $value);
        }

        return $this;
    }

    /**
     * Lazy load connections
     *
     * @param array $connections
     * @return ZendX_Doctrine_Application_Resource_Doctrine
     * @throws ZendX_Doctrine_Application_Resource_Exception If Doctrine resource dsn is wrong
     * @see Zend_Application_Resource_ResourceAbstract
     * @todo Handle event listeners
     */
    public function getConnections(array $connections = array())
    {
        foreach($connections as $name => $params) {
            if ((!isset($params['dsn'])) || (empty($params['dsn']))) {
                throw new
                ZendX_Doctrine_Application_Resource_Exception('Doctrine
                                                              resource dsn not present.');
            }

            $dsn = null;
            $options = null;

            if (is_string($params['dsn'])) {
                $dsn = $params['dsn'];
            } elseif (is_array($params['dsn'])) {
                $dsn = $this->_buildConnectionString($params['dsn']);
            } else {
                throw new
                ZendX_Doctrine_Application_Resource_Exception("Invalid
                                                              Doctrine resource dsn format.");
            }

            try {
                $conn = Doctrine_Manager::connection($dsn, $name);

                if ((isset($params['attributes'])) && (!empty($params['attributes']))) {
                    foreach ($params['attributes'] as $key => $value) {
                        $conn->setAttribute($key, $value);
                    }
                }
            } catch(Doctrine_Exception $e) {
                throw new
                ZendX_Doctrine_Application_Resource_Exception("Unable to
                                                              establish database connection. Check application.ini settings.");
            }
        }

        return $this;
    }

    /**
     * Set the debug status
     *
     * @param  bool $debug
     * @return ZendX_Doctrine_Application_Resource_Doctrine
     */
    public function getDebug($debug = 0)
    {
        Doctrine::debug($debug);

        return $this;
    }

    /**
     * Add global paths to the registry
     *
     * @param  array $paths
     * @return ZendX_Doctrine_Application_Resource_Doctrine
     */
    public function getPaths()
    {
        // set default paths

        $paths = &$this->_options['paths'];
        // apply default paths if not set in application.ini
        $defaults = array(
                          'data_fixtures_path' => APPLICATION_PATH . '/../doctrine/data/fixtures/',
                          'models_path' => APPLICATION_PATH . '/models/',
                          'generated_models_path' => APPLICATION_PATH . '/models/generated/',
                          'migrations_path' => APPLICATION_PATH . '/../doctrine/migrations/',
                          'sql_path' => APPLICATION_PATH . '/../doctrine/data/sql/',
                          'yaml_schema_path' => APPLICATION_PATH . '/../doctrine/schema/'
                          );

        foreach ($defaults as $key=>$path) {
            if (!isset($paths[$key])) {
                $paths[$key] = $path;
            }
        }

        Zend_Registry::set('doctrine', array('paths' => $paths));

        // add models and generated models path to the include_path
        set_include_path(implode(PATH_SEPARATOR, array(
                                                       $paths['models_path'],
                                                       $paths['generated_models_path'],
                                                       get_include_path(),
                                                       )));

        return $this;
    }



    /**
     * Build connection string
     *
     * @param  array $dsnData
     * @return string
     */
    private function _buildConnectionString(array $dsnData = array())
    {
        $connectionOptions = null;
        if ((isset($dsnData['options'])) || (!empty($dsnData['options']))) {
            $connectionOptions = $this->_buildConnectionOptionsString($dsnData['options']);
        }

        // @see
        // http://www.doctrine-project.org/documentation/manual/1_1/en/introduction-to-connections
        return sprintf('%s://%s:%s@%s/%s?%s',
                       $dsnData['adapter'],
                       $dsnData['user'],
                       $dsnData['pass'],
                       $dsnData['hostspec'],
                       $dsnData['database'],
                       $connectionOptions);
    }

    /**
     * Enable or disable Doctrine Session SaveHandler
     * @param array $options Doctrine Session SaveHandler options: handler.enabled and lifetime).
     *                       If at least one of those two is set (true), session is enabled.
     *
     * @throws ZendX_Doctrine_Application_Resource_Exception If session table is inaccesible.
     * @return ZendX_Doctrine_Application_Resource_Doctrine
     */
     public function setSessionHandler($options = null)
     {

        if (isset($options)) {

            // enable by default if any session option is set
            if (!isset($options['handler'])) {
                $options['handler'] = true;
            }

            if ($options['handler']) {

                if (!empty($options['table'])) {
                    $table = $options['table'];
                } else {
                    $table = 'Session';
                }

                try {
                    $handler = new ZendX_Doctrine_Session_SaveHandler();
                    $handler->setTable($table);
                    // session lifetime from options
                    if (isset($options['lifetime']) && $options['lifetime']) {
                        $handler->setLifetime($options['lifetime']);
                    }
                    Zend_Session::setSaveHandler($handler);
                    Zend_Session::start();
                } catch (Exception $e) {
                    throw new ZendX_Doctrine_Application_Resource_Exception('Unable to access Doctrine session table.');
                }
            }
        }

        return $this;
    }


    /**
     * Build connection options string
     *
     * @param  array $optionsData
     * @return string
     */
    private function _buildConnectionOptionsString(array $optionsData = array())
    {
        $i = 0;
        $count = count($optionsData);
        $options  = null;

        // @todo Determine if there is a better way to calculate when the end
        // of the array has been reached
        foreach ($optionsData as $key => $value) {
            if ($i == $count) {
                $options .= "$key=$value";
            } else {
                $options .= "$key=$value&";
            }

            $i++;
        }

        return $options;
    }
}
