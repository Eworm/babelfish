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
        \Statamic\Events\Data\FindingFieldset::class => 'addEventTab',
        'cp.add_to_head' => 'addToHead'
    ];

    /**
     * Add the events tab to the chosen entry
     *
     * @var array
     */
    public function addEventTab(FindingFieldset $eventCollection)
    {
        if ($eventCollection->type == 'entry') {
            $fieldset = $eventCollection->fieldset;
            $sections = $fieldset->sections();
            $fields = YAML::parse(File::get($this->getDirectory().'/resources/fieldsets/content.yaml'))['fields'];

            $sections['event'] = [
                 'display' => 'Rich snippet',
                 'fields' => $fields
             ];

            $contents = $fieldset->contents();
            $contents['sections'] = $sections;
            $fieldset->contents($contents);
        }
    }

    protected function getPlaceholder($key, $field, $data)
    {
        if (! $data) {
            return;
        }

        $vars = (new TagData)
            ->with(Settings::load()->get('defaults'))
            ->with($data->getWithCascade('anchorman', []))
            ->withCurrent($data)
            ->get();

        return array_get($vars, $key);
    }

    public function addToHead()
    {
        $assetContainer = $this->getConfig('asset_container');
        $suggestions = json_encode((new FieldSuggestions)->suggestions());
        return "<script>var Babelfish = { assetContainer: '{$assetContainer}', fieldSuggestions: {$suggestions} };</script>";
    }
}
