    protected static $_instances = [];

    /**
     * @return static
     */
    static function in() {
        $class = get_called_class();
        if (!isset(self::$_instances[$class])) {
            $request = Yaf_Dispatcher::getInstance()->getRequest();
            $response = new Response();
            $view = View::in();
            self::$_instances[$class] = new static($request, $response, $view);
        }
        return self::$_instances[$class];
    }
