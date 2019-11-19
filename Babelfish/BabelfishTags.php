<?php

namespace Statamic\Addons\Babelfish;

use Statamic\Data\Data;
use Carbon\Carbon;
use Statamic\Extend\Tags;
use Statamic\API\User;

class BabelfishTags extends Tags
{
    /**
     * The {{ babelfish }} tag
     *
     * @return string
     */
    public function index()
    {
        if (isset($this->context['schema_type'])) :
            $schema = $this->context['schema_type'];

        $schemas = [];

        foreach ($schema as $type) {
            if ($type['type'] == 'person') {
                $schemas[] = $this->person($type);
            } elseif ($type['type'] == 'recipe') {
                $schemas[] = $this->recipe($type);
            } elseif ($type['type'] == 'organization') {
                $schemas[] = $this->organization($type);
            } elseif ($type['type'] == 'product') {
                $schemas[] = $this->product($type);
            } elseif ($type['type'] == 'article') {
                $schemas[] = $this->article($type);
            } elseif ($type['type'] == 'website') {
                $schemas[] = $this->website($type);
            }
        }

        return implode($schemas);

        endif;
    }

    /**
     * Article
     *
     * @return string
     */
    private function article($type)
    {
        $user = User::find($type['Author']);
        return '<script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "' . $this->issetor($type['Type']) . '",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "woutmager.nl"
            },
            "headline": "' . $this->issetor($type['Title']) . '",
            "description": "' . $this->issetor($type['Description']) . '",
            "image": {
                "@type": "ImageObject",
                "url": "' . $this->issetor($type['Photo']) . '"
            },
            "author": {
                "@type": "Organization",
                "name": "' . $user->get('first_name') . ' ' . $user->get('last_name') .'"
            },
            "publisher": {
                "@type": "Organization",
                "name": "' . $this->issetor($type['Publisher']) . '",
                "logo": {
                    "@type": "ImageObject",
                    "url": "' . $this->issetor($type['Publisher logo']) . '"
                }
            },
            "datePublished": "' . $this->issetor_date($this->context['date']) . '",
            "dateModified": "' . date("Y-m-d", $this->context['last_modified']) . '"
        }
        </script>';
    }

    /**
     * Person
     *
     * @return string
     */
    private function person($type)
    {
        return '<script type="application/ld+json">
        {
            "@context": "https://schema.org/",
            "@type": "Person",
            "name": "' . $this->issetor($type['Name']) . '",
            "url": "' . $this->issetor($type['URL']) . '",
            "image": "' . $this->issetor($type['Photo']) . '",
            "sameAs": "' . $this->issetor($type['Social profiles']) . '",
            "jobTitle": "' . $this->issetor($type['Job title']) . '",
            "worksFor": {
                "@type": "Organization",
                "name": "' . $this->issetor($type['Company']) . '"
            }
        }
        </script>';
    }

    /**
     * Product
     *
     * @return string
     */
    private function product($type)
    {
        return '<script type="application/ld+json">
        {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "' . $this->issetor($type['Name']) . '",
        "image": "' . $this->issetor($type['Image']) . '",
        "description": "' . $this->issetor($type['Description']) . '",
        "brand": "' . $this->issetor($type['Brand']) . '",
        ' . $this->product_properties($type['Properties']) . '
        "offers": {
            "@type": "Offer",
            "url": "' . $this->issetor($type['URL']) . '",
            "priceCurrency": "' . $this->issetor($type['Currency']) . '",
            "price": "' . $this->issetor($type['Price']) . '",
            "priceValidUntil": "' . $this->issetor($type['Valid until']) . '",
            "availability": "' . $this->product_availabilty($this->issetor($type['Availabilty'])). '",
            "itemCondition": "' . $this->product_condition($this->issetor($type['Condition'])). '"
            }
        }
        </script>';
    }

    /**
     * Recipe
     *
     * @return string
     */
    private function recipe($type)
    {
        $user = User::find($type['Author']);
        $steps = $type['List of steps'];
        return '<script type="application/ld+json">
        {
            "@context": "https://schema.org/",
            "@type": "Recipe",
            "name": "' . $this->issetor($type['Title']) . '",
            "image": "' . $this->issetor($type['Photo']) . '",
            "video": "' . $this->issetor($type['Video']) . '",
            "description": "' . $this->issetor($type['Description']) . '",
            "recipeCuisine": "' . $this->issetor($type['Cuisine']) . '",
            "keywords": ' . $this->context($type['Keywords']) . ',
            "author": {
                "@type": "Person",
                "name": "' . $user->get('first_name') . ' ' . $user->get('last_name') .'"
            },
            "prepTime": "PT' . $this->issetor($type['Preparation time']) . 'M",
            "cookTime": "PT' . $this->issetor($type['Cook time']) . 'M",
            "totalTime": "PT' . $this->issetor($type['Total time']) . 'M",
            "recipeCategory": "' . $this->issetor($type['Category']) . '",
            "nutrition": {
                "@type": "NutritionInformation",
                "servingSize": "' . $this->issetor($type['Serving size']) . '",
                "calories": "' . $this->issetor($type['Calories']) . ' cal",
                "fatContent": "' . $this->issetor($type['Fat']) . ' g"
            },
            "recipeIngredient": ' . $this->context($type['List of Ingredients']) . ',
            "recipeInstructions": ' . $this->recipe_instructions($type['List of steps']) . '
        }
        </script>';
    }

    /**
     * Organization
     *
     * @return string
     */
    private function organization($type)
    {
        return '<script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "' . str_replace(' ', '', $this->issetor($type['Type'])) . '",
            "name": "' . $this->issetor($type['Name']) . '",
            "alternateName": "' . $this->issetor($type['Alternate name']) . '",
            "url": "' . $this->issetor($type['URL']) . '",
            "logo": "' . $this->issetor($type['Logo']) . '",
            "contactPoint": ' . $this->organization_contacts($type['Contacts']) . ',
            "sameAs": ' . $this->context($type['Social profiles']) . '
        }
        </script>';
    }

    /**
     * Website
     *
     * @return string
     */
    private function website($type)
    {
        return '<script type="application/ld+json">
        {
            "@context": "http://schema.org",
            "@type": "WebSite",
            "name": "' . $this->issetor($type['Name']) . '",
            "url": "' . $this->issetor($type['URL']) . '"
        }
        </script>';
    }

    /**
     * Product availability
     *
     * @return string
     */
    private function product_availabilty($availability)
    {
        switch ($availability) {
            case "In stock":
                return 'https://schema.org/InStock';
                break;
            case "Out of stock":
                return 'https://schema.org/OutOfStock';
                break;
            case "Online only":
                return 'https://schema.org/OnlineOnly';
                break;
            case "In store only":
                return 'https://schema.org/InStoreOnly';
                break;
            case "Preorder":
                return 'https://schema.org/PreOrder';
                break;
            case "Presale":
                return 'https://schema.org/PreSale';
                break;
            case "Limited availability":
                return 'https://schema.org/LimitedAvailability';
                break;
            case "Sold out":
                return 'https://schema.org/SoldOut';
                break;
            case "Discontinued":
                return 'https://schema.org/Discontinued';
                break;
        }
    }

    /**
     * Product condition
     *
     * @return string
     */
    private function product_condition($availability)
    {
        switch ($availability) {
            case "New":
                return 'https://schema.org/NewCondition';
                break;
            case "Used":
                return 'https://schema.org/UsedCondition';
                break;
        }
    }

    /**
     * Product properties
     *
     * @return string
     */
    private function product_properties($properties)
    {
        $list = '';
        if (isset($properties)) {
            foreach ($properties as $property) {
                $list .= '"' . $property['property'] . '": "' . $property['value'] . '",';
            }
            return $list;
        }
    }

    /**
     * Organization contact
     *
     * @return string
     */
    private function organization_contacts($contacts)
    {
        $list = '';
        if (isset($contacts)) {
            foreach ($contacts as $contact) {
                $list .= '{' . "\r\n";
                $list .= '"@type": "ContactPoint",' . "\r\n";
                $list .= '"telephone": "' . $contact['Phone'] . '",' . "\r\n";
                $list .= '"contactType": "' . $contact['Type'] . '",' . "\r\n";
                $list .= '"contactOption": ' . $this->context($contact['Extras']) . ',' . "\r\n";
                $list .= '"availableLanguage": ' . $this->context($contact['Languages']) . '' . "\r\n";
                $list .= '}';
            }
            return $list;
        }
    }

    /**
     * Recipe instructions
     *
     * @return string
     */
    private function recipe_instructions($instructions)
    {
        $list = '';
        if (isset($instructions)) {
            $hasComma = false;
            $list .= "[";
            foreach ($instructions as $instruction) {
                $list .= '{"@type": "HowToStep",';
                $list .= '"text":"' . $instruction . '"}';
                if (!$hasComma){
                    $list .= ",";
                }
                $hasComma = true;
            }
            $list .= "]";
            return $list;
        }
    }

    /**
     * Return a fieldtype value or an array
     *
     * @return string|array
     */
    private function context($fieldtype)
    {
        if (isset($fieldtype)) {

            if (is_array($fieldtype)) {

                // $fieldtype is an array
                if (count($fieldtype) == 1) {

                    // $fieldtype has 1
                    return '"' . \implode("','", $fieldtype) . '"';
                } elseif (count($fieldtype) > 1) {

                    // $fieldtype has more than 1
                    return "[\"" . \implode("\",\"", $fieldtype) . "\"]";
                }
            } else {
                // $fieldtype is a string
                return $fieldtype;
            }
        } else {
            return null;
        }
    }

    /**
     * Return a fieldtype value
     *
     * @return string
     */
    public function issetor(&$var, $default = false)
    {
        return isset($var) ? $var : $default;
    }

    /**
     * Return a date value
     *
     * @return string
     */
    public function issetor_date(&$var, $default = false)
    {
        return isset($var) ? date_format($var, "Y-m-d") : $default;
    }
}
