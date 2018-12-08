<?php

class File {

    protected $_id;
    protected $_content;
    protected $_currentViews;
    protected $_maxViews;
    protected $_deletionPassword;
    protected $_metaData;

    /**
     * Main function of File class.
     * @since 2.2
     * @param array $file $_FILES array if available.
     * @param string $id ID if one is already set.
     */
    public function __construct($file, string $id = NULL) {
        if ($id === NULL) {
            $this->generateID();
        } else {
            $this->setID($id);
        }
        if ($file !== NULL) {
            $this->_metadata = ['name' => $file['name'], 'size' => $file['size'], 'type' => $file['type']];
        }
    }

    /**
     * Gets ID of the file.
     * @since 2.2
     * @return string Returns the ID of the file.
     */
    public function __toString() {
        return $this->_id;
    }

    /**
     * Gets ID of the file.
     * @since 2.2
     * @return string Returns the ID of the file.
     */
    public function getID() {
        return $this->_id;
    }

    /**
     * Gets file content.
     * @since 2.2
     * @return string Returns file content in clear text.
     */
    public function getContent() {
        return $this->_content;
    }

    /**
     * Gets the current views of the file if available.
     * @since 2.2
     * @return mixed Returns current views/downloads of the file if supplied, otherwise NULL.
     */
    public function getCurrentViews() {
        return $this->_currentViews;
    }

    /**
     * Gets the max available views/downloads before the file gets deleted.
     * @since 2.2
     * @return mixed Returns maxs views of the file if supplied, otherwise NULL.
     */
    public function getMaxViews() {
        return $this->_maxViews;
    }

    /**
     * Gets the deletion password of the file.
     * @since 2.2
     * @return string Returns deletion password if supplied, otherwise NULL.
     */
    public function getDeletionPassword() {
        return $this->_deletionPassword;
    }

    /**
     * Gets the metadata of the file if supplied.
     * @since 2.2
     * @param string $type Array key of the desired value.
     * @return mixed Returns data of the desired array key if a $type is supplied, otherwise the entire array.
     */
    public function getMetaData(string $type = NULL) {
        if ($type !== NULL) return $this->_metadata[$type];
        return $this->_metaData;
    }

    /**
     * Sets the ID of the file.
     * @since 2.2
     * @param string $id New ID of the file.
     * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
     */
    private function setID(string $id) {
        return $this->_id = $id;
    }

    /**
     * Generates a new ID for the file.
     * @since 2.2
     * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
     */
    private function generateID() {
        $id = strtoupper(uniqid("d"));
        return $this->_id = $id;
    }

    /**
     * Sets the current views/downloads of the file.
     * @since 2.2
     * @param int $views New views/downloads of the file.
     * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
     */
    public function setCurrentViews(int $views) {
        if ($views > $this->_maxViews) {
            // DataStorage::setViews($this->_maxViews, $views, $this->getID());
            return $this->_currentViews = $views;
        } elseif ($views <= $this->maxViews) {
            DataStorage::deleteFile($this->_id);
        }
    }

    /**
     * Sets the max views of the file.
     * @since 2.2
     * @param int $views New maxs views of the file.
     * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
     */
    public function setMaxViews(int $views) {
        $this->_maxViews;
    }

    /**
     * Sets the metadata of the file.
     * @since 2.2
     * @param array New metadata of the file.
     * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
     */
    public function setMetaData(array $metadata) {
        $this->_metaData = $metadata;
    }

    /**
     * Sets the deletion password of the file.
     * @since 2.2
     * @param string $deletionpassword New deletion password of the file.
     * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
     */
    public function setDeletionPassword(string $deletionpassword) {
        $this->_deletionPassword = $deletionpassword;
    }

    /**
     * Sets the content of the file.
     * @since 2.2
     * @param string $content New content of the file. Should be sent as clear text.
     * @return boolean Returns TRUE if the action was successfully executed, otherwise FALSE.
     */
    public function setContent($content) {
        $this->_content = $content;
    }

}
