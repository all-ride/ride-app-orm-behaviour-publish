<?php

namespace ride\application\orm\model\behaviour;

use ride\application\orm\entry\PublishedEntry;

use ride\library\orm\model\behaviour\AbstractBehaviour;
use ride\library\orm\model\Model;

/**
 * Behaviour to add publication status to a model
 */
class PublishBehaviour extends AbstractBehaviour {

    /**
     * Hook after creating an entry
     * @param \ride\library\orm\model\Model $model
     * @param mixed $entry
     * @return null
     */
    public function postCreateEntry(Model $model, $entry) {
        $this->updatePublishState($entry);
    }

    /**
     * Hook before inserting an entry
     * @param \ride\library\orm\model\Model $model
     * @param mixed $entry
     * @return null
     */
    public function preInsert(Model $model, $entry) {
        $this->updatePublishState($entry);
    }

    /**
     * Hook before updating an entry
     * @param \ride\library\orm\model\Model $model
     * @param mixed $entry
     * @return null
     */
    public function preUpdate(Model $model, $entry) {
        $this->updatePublishState($entry);
    }

    /**
     * Initializes the published from date
     * @param mixed $entry
     * @return null
     */
    private function updatePublishState($entry) {
        if (!$entry instanceof PublishedEntry || !$entry->isPublished()) {
            return;
        }

        if (!$entry->getDatePublishedFrom()) {
            $entry->setDatePublishedFrom(time());
        }
    }

}
