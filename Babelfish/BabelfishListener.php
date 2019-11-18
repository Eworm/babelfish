<?php

namespace Statamic\Addons\Babelfish;

use Statamic\Extend\Listener;
use Statamic\Events\Data\FindingFieldset;
use Statamic\API\YAML;
use Statamic\API\File;

class BabelfishListener extends Listener
{
    /**
     * The events to be listened for, and the methods to call.
     *
     * @var array
     */
    public $events = [
        \Statamic\Events\Data\FindingFieldset::class => 'addSnippetsTab'
    ];

    /**
     * Add the rich snippets tab to the chosen entry
     *
     * @var array
     */
    public function addSnippetsTab(FindingFieldset $eventCollection)
    {
        $fieldset = $eventCollection->fieldset;
        $sections = $fieldset->sections();
        $fields = YAML::parse(File::get($this->getDirectory().'/resources/fieldsets/content.yaml'))['fields'];

        $sections['event'] = [
             'display' => 'Rich snippets',
             'fields' => $fields
         ];

        $contents = $fieldset->contents();
        $contents['sections'] = $sections;
        $fieldset->contents($contents);
    }
}
