<?php
/**
 * Doctrine Session Handler
 * Stores session data in database
 */

class ZendX_Doctrine_Session_SaveHandler implements Zend_Session_SaveHandler_Interface
{
    private $_table = 'Session';
    private $_sessionName;
    private $_session;
    private $_lifetime;

    /**
     * Set session table name
     * @access public
     * @param string $table
     * @return ZendX_Doctrine_Session
     */
    public function setTable($table) {
        $this->_table = $table;
        return $this;
    }

    /**
     * Get session table name
     *
     * @access public
     * @return string Session table name
     */
    public function getTable() {
        return $this->_table;
    }

    /**
     * Get session table
     *
     * @access public
     * @return Doctrine_Table Session table object
     */
    public function getTableObject() {
        return Doctrine::getTable($this->_table);
    }

    /**
     * Constructor
     * @param int $lifetime null
     */
    public function __construct($lifetime = null)
    {
        if(is_null($lifetime))
        {
            $this->_lifetime = (int) ini_get('session.gc_maxlifetime');
        }
        else {
            $this->_lifetime = (int) $lifetime;
        }
    }

    /**
     * Session lifetine
     * @param int $lifetime Session lifetime (unix timestamp)
     * @return ZendX_Doctrine_Session
     */
    public function setLifetime($lifetime)
    {
        $this->_lifetime = $lifetime;
        return $this;
    }

    /**
     * Get session lifetime
     * @return int Session lifetime
     */
    public function getLifetime()
    {
        return $this->_lifetime;
    }

    /**
     * Read session data
     * @param string $id Session identifier
     * @return string Session data
     */
    public function read($id)
    {
        $this->_session = Doctrine::getTable($this->_table)->find($id);

        if(empty($this->_session))
        {
            $this->_session = new Session();
            $this->_session->id = $id;
            $this->_session->lifetime = $this->_lifetime;

            return '';
        }
        return $this->_session->data;
    }

    /**
     * Write session data
     * @param string $id Session data identifier
     * @param string|array $data session data
     */
    public function write($id, $data)
    {
        $this->_session->data = $data;
        $this->_session->modified = time();
        $this->_session->save();

        return true;
    }

    /**
     * Destroy session
     * @param string $id Session identifier
     * @return bool
     */
    public function destroy($id)
    {
        if($this->_session->id == $id)
        {
            $this->_session->delete();
            return true;
        }
        return false;
    }

    /**
     * @param int $maxlifetime
     */
    public function gc($maxlifetime)
    {
        Doctrine_Query::create()->delete($this->_table . ' s')->where('s.modified < (? - s.lifetime)', time())->execute();
    }

    /**
     * Open session
     * @param string $save_path
     * @param string $name
     */
    public function open($save_path, $name)
    {
        $this->_sessionName = $name;
        return true;
    }

    /**
     * Close sesion connection
     * @return true
     */
    public function close()
    {
        return true;
    }
}