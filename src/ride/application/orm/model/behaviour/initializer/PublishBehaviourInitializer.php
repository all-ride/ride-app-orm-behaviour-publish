<?php

namespace ride\application\orm\model\behaviour\initializer;

use ride\application\orm\model\behaviour\GeocodeBehaviour;

use ride\library\generator\CodeClass;
use ride\library\generator\CodeGenerator;
use ride\library\orm\definition\field\PropertyField;
use ride\library\orm\definition\ModelTable;
use ride\library\orm\model\behaviour\initializer\BehaviourInitializer;
use ride\library\reflection\Boolean;

use \InvalidArgumentException;

/**
 * Setup the publish behaviour based on the model options
 */
class PublishBehaviourInitializer implements BehaviourInitializer {

    /**
     * Gets the behaviours for the model of the provided model table
     * @param \ride\library\orm\definition\ModelTable $modelTable
     * @return array An array with instances of Behaviour
     * @see \ride\library\orm\model\behaviour\Behaviour
     */
    public function getBehavioursForModel(ModelTable $modelTable) {
        if (!$modelTable->getOption('behaviour.publish')) {
            return array();
        }

        $baseOptions = array();

        // if tabs are used, add a new tab 'visibility'
        $tabs = $modelTable->getOption('scaffold.form.tabs');
        if ($tabs) {
            $baseOptions['scaffold.form.tab'] = 'visibility';

            // add the tab if it isn't available yet
            $tabArray = explode(',', str_replace(' ', '', $tabs));
            if (!in_array('visiblity', $tabArray)) {
                $modelTable->setOption('scaffold.form.tabs', $tabs . ',visibility');
            }
        }

        if (!$modelTable->hasField('isPublished')) {
            $options = $baseOptions;
            $options['label.name'] = 'label.published';
            $options['label.description'] = 'label.published.description';
            $options['scaffold.order'] = '1';

            $isPublishedField = new PropertyField('isPublished', 'boolean');
            $isPublishedField->setOptions($options);

            $modelTable->addField($isPublishedField);
        }

        if (!$modelTable->hasField('datePublishedFrom')) {
            $options = $baseOptions;
            $options['label.name'] = 'label.date.published.from';
            $options['scaffold.form.dependant'] = 'isPublished';
            $options['scaffold.order'] = '1';

            $datePublishedFromField = new PropertyField('datePublishedFrom', 'datetime');
            $datePublishedFromField->setOptions($options);

            $modelTable->addField($datePublishedFromField);
        }

        if (!$modelTable->hasField('datePublishedTill')) {
            $options = $baseOptions;
            $options['label.name'] = 'label.date.published.till';
            $options['scaffold.form.dependant'] = 'isPublished';
            $options['scaffold.order'] = '1';

            $datePublishedTillField = new PropertyField('datePublishedTill', 'datetime');
            $datePublishedTillField->setOptions($options);

            $modelTable->addField($datePublishedTillField);
        }

        return array();
    }

    /**
     * Generates the needed code for the entry class of the provided model table
     * @param \ride\library\orm\definition\ModelTable $table
     * @param \ride\library\generator\CodeGenerator $generator
     * @param \ride\library\generator\CodeClass $class
     * @return null
     */
    public function generateEntryClass(ModelTable $modelTable, CodeGenerator $generator, CodeClass $class) {
        if ($modelTable->getOption('behaviour.publish')) {
            $this->performEntryClassGeneration($generator, $class);
        }

        // $this->performRelationGeneration($modelTable, $generator, $class);
    }

    // private function performRelationGeneration(ModelTable $modelTable, CodeGenerator $generator, CodeClass $class) {
        // $fields = $modelTable->getFields();
        // foreach ($fields as $field) {
            // $relationModelName = $field->getRelationModelName();
            // $relationModel = $this->orm->getModel($relationModelName);
            // if (!$relationModel->getMeta()->getOption('behaviour.publish')) {
                // continue;
            // }

            // $suffix = ucfirst($field->getName());

            // if ($field instanceof HasManyField) {
                // $description = 'Get the published entries for this relation';
                // $code =
// 'if (!$date) {
    // $date = time();
// }

// $publishedEntries = array();

// $entries = $this->get' . $suffix . '();
// foreach ($entries as $entry) {
    // if ($entry->isPublishedEntry($date)) {
        // $publishedEntries[$entry->getId()] = $entry;
    // }
// }

// return $publishedEntries;';
            // } elseif ($field instanceof RelationField) {
                // $description = 'Gets the published entry of this relation';
                // $code =
// 'if (!$date) {
    // $date = time();
// }

// $entry = $this->get' . $suffix . '();
// if (!$entry->isPublishedEntry($date)) {
    // return null
// }

// return $entry;';
            // }

            // $dateArgument = $generator->createVariable('date', 'integer');
            // $dateArgument->setDescription('Timestamp of the date to check');
            // $dateArgument->setDefaultValue(null);

            // $getPublishedRelationMethod = $generator->createMethod('getPublished' . $suffix, array($dateArgument), $code);
            // $getPublishedRelationMethod->setDescription($description);
            // $getPublishedRelationMethod->setReturnValue($generator->createVariable('result', 'array'));

            // $class->addMethod($getPublishedRelationMethod);
        // }
    // }

    private function performEntryClassGeneration(CodeGenerator $generator, CodeClass $class) {
        $class->addImplements('ride\\application\\orm\\entry\\PublishedEntry');

        $code =
'if (!$this->isPublished()) {
    return false;
}

if (!$date) {
    $date = time();
}

$from = $this->getDatePublishedFrom();
$till = $this->getDatePublishedTill();

if (!$from && !$till) {
    return true;
} elseif ($from && $till && $from <= $date && $date < $till) {
    return true;
} elseif ($from && $from <= $date) {
    return true;
} elseif ($till && $date < $till) {
    return true;
}

return false;';

        $dateArgument = $generator->createVariable('date', 'integer');
        $dateArgument->setDescription('Timestamp of the date to check');
        $dateArgument->setDefaultValue(null);

        $isPublishedEntryMethod = $generator->createMethod('isPublishedEntry', array($dateArgument), $code);
        $isPublishedEntryMethod->setDescription('Checks if this entry is published for the provided date');
        $isPublishedEntryMethod->setReturnValue($generator->createVariable('result', 'boolean'));

        $class->addMethod($isPublishedEntryMethod);
    }

}
