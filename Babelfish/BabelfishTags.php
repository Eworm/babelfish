<?php

namespace Statamic\Addons\Babelfish;

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

        switch ($schema) {
            case "article":
                return $this->article();
                break;
            case "person":
                return $this->person();
                break;
            case "product":
                return $this->product();
                break;
            case "recipe":
                return $this->recipe();
                break;
            case "review":
                break;
            case "service":
                break;
            case "software":
                break;
        }

        endif;
    }

    /**
     * Article
     *
     * @return string
     */
    private function article()
    {
        $user = User::find($this->context('recipe_author'));
        return '<script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "' . ucfirst($this->context('article_type')) . '",
            "mainEntityOfPage": {
                "@type": "WebPage",
                "@id": "woutmager.nl"
            },
            "headline": "' . $this->context('article_title') . '",
            "description": "' . $this->context('article_description') . '",
            "image": {
                "@type": "ImageObject",
                "url": "' . $this->context('article_photo') . '"
            },
            "author": {
                "@type": "Organization",
                "name": "' . $user->get('first_name') . ' ' . $user->get('last_name') .'"
            },
            "publisher": {
                "@type": "Organization",
                "name": "' . $this->context('article_publisher') . '",
                "logo": {
                    "@type": "ImageObject",
                    "url": "' . $this->context('article_publisher_logo') . '"
                }
            },
            "datePublished": "' . date_format($this->context('date'), "Y-m-d") . '",
            "dateModified": "// TODO: "
        }
        </script>';
    }

    /**
     * Person
     *
     * @return string
     */
    private function person()
    {
        return '<script type="application/ld+json">
        {
            "@context": "https://schema.org/",
            "@type": "Person",
            "name": "' . $this->context('person_name') . '",
            "url": "' . $this->context('person_url') . '",
            "image": "' . $this->context('person_photo') . '",
            "sameAs": [\'' . \implode("','", $this->context('person_social_profiles')) . '\'],
            "jobTitle": "' . $this->context('person_job_title') . '",
            "worksFor": {
                "@type": "Organization",
                "name": "' . $this->context('person_company') . '"
            }
        }
        </script>';
    }

    /**
     * Product
     *
     * @return string
     */
    private function product()
    {
        return '<script type="application/ld+json">
        {
        "@context": "https://schema.org/",
        "@type": "Product",
        "name": "' . $this->context('product_name') . '",
        "image": "' . $this->context('product_image') . '",
        "description": "' . $this->context('product_description') . '",
        "brand": "' . $this->context('product_brand') . '",
        ' . $this->product_properties($this->context('product_properties')) . '
        "offers": {
            "@type": "Offer",
            "url": "' . $this->context('product_url') . '",
            "priceCurrency": "' . $this->context('product_currency') . '",
            "price": "' . $this->context('product_price') . '",
            "priceValidUntil": "' . $this->context('product_valid_until') . '",
            "availability": "' . $this->product_availabilty($this->context('product_availabilty')). '",
            "itemCondition": "' . $this->product_condition($this->context('product_condition')). '"
            }
        }
        </script>';
    }

    /**
     * Recipe
     *
     * @return string
     */
    private function recipe()
    {
        $user = User::find($this->context('recipe_author'));
        $steps = $this->context('recipe_steps_list');
        return '<script type="application/ld+json">
        {
            "@context": "https://schema.org/",
            "@type": "Recipe",
            "name": "' . $this->context('recipe_title') . '",
            "image": "' . $this->context('recipe_photo') . '",
            "description": "' . $this->context('recipe_description') . '",
            "keywords": "' . \implode(",", $this->context('recipe_keywords')) . '",
            "author": {
                "@type": "Person",
                "name": "' . $user->get('first_name') . ' ' . $user->get('last_name') .'"
            },
            "prepTime": "PT' . $this->context('recipe_preparation_time') . 'M",
            "cookTime": "PT' . $this->context('recipe_cook_time') . 'M",
            "totalTime": "PT' . $this->context('recipe_total_time') . 'M",
            "recipeCategory": "' . $this->context('recipe_category') . '",
            "nutrition": {
                "@type": "NutritionInformation",
                "servingSize": "' . $this->context('recipe_serving_size') . '",
                "calories": "' . $this->context('recipe_calories') . ' cal",
                "fatContent": "' . $this->context('recipe_fat') . ' g"
            },
            "recipeIngredient": [
                \'' . \implode("','", $this->context('recipe_ingredients_list')) . '\'
            ],
            "recipeInstructions": [{"@type": "HowToStep","text":"' . \implode("\"},{\"@type\": \"HowToStep\",\"text\":\"", $this->context('recipe_steps_list')) . '"}]
        }
        </script>';
    }

    /**
     * Product availability
     *
     * @return string
     */
    public function product_availabilty($availability)
    {
        switch ($availability) {
            case "in_stock":
                return 'https://schema.org/InStock';
                break;
            case "out_of_stock":
                return 'https://schema.org/OutOfStock';
                break;
            case "online_only":
                return 'https://schema.org/OnlineOnly';
                break;
            case "in_store_only":
                return 'https://schema.org/InStoreOnly';
                break;
            case "preorder":
                return 'https://schema.org/PreOrder';
                break;
            case "presale":
                return 'https://schema.org/PreSale';
                break;
            case "limited_availability":
                return 'https://schema.org/LimitedAvailability';
                break;
            case "soldout":
                return 'https://schema.org/SoldOut';
                break;
            case "discontinued":
                return 'https://schema.org/Discontinued';
                break;
        }
    }

    /**
     * Product condition
     *
     * @return string
     */
    public function product_condition($availability)
    {
        switch ($availability) {
            case "new":
                return 'https://schema.org/NewCondition';
                break;
            case "used":
                return 'https://schema.org/UsedCondition';
                break;
        }
    }

    /**
     * Product properties
     *
     * @return string
     */
    public function product_properties($properties)
    {
        $list = '';
        foreach ($properties as $property) {
            $list .= '"' . $property['property'] . '": "' . $property['value'] . '",';
        }
        return $list;
    }

    /**
     * Get a fieldtype value
     *
     * @return string
     */
    private function context($fieldtype)
    {
        if (isset($this->context[$fieldtype])) {
            return $this->context[$fieldtype];
        } else {
            return null;
        }
    }
}
