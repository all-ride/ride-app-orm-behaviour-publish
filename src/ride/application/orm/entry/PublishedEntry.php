<?php

namespace ride\application\orm\entry;

/**
 * Interface for an entry with publication support
 */
interface PublishedEntry {

    /**
     * Checks if this entry is published for the provided date
     * @param integer $date Timestamp of the date to check
     * @return boolean
     */
    public function isPublishedEntry($date = null);

}
