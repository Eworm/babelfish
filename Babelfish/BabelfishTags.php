<?php

namespace Statamic\Addons\Babelfish;

use Statamic\API\Config;
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
            } elseif ($type['type'] == 'job') {
                $schemas[] = $this->job($type);
            } elseif ($type['type'] == 'book') {
                $schemas[] = $this->book($type);
            } elseif ($type['type'] == 'howto') {
                $schemas[] = $this->howto($type);
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
            "recipeInstructions": ' . $this->listtoarray($this->issetor($type['List of steps']), "HowToStep", "text") . '
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
     * Book
     *
     * @return string
     */
    private function book($type)
    {
        return '<script type="application/ld+json">
        {
            "@context": "https://schema.org",
            "@type": "WebPage",
            "mainEntity": {
                "@type": "Book",
                "author": "' . $this->issetor($type['Author URL']) . '",
                "bookFormat": "http://schema.org/' . str_replace(' ', '', $this->issetor($type['Type'])) . '",
                "datePublished": "' . $this->issetor($type['Published date']) . '",
                "image": "' . $this->issetor($type['Cover image']) . '",
                "inLanguage": "' . $this->issetor($type['Language']) . '",
                "isbn": "' . $this->issetor($type['ISBN']) . '",
                "name": "' . $this->issetor($type['Title']) . '",
                "numberOfPages": "' . $this->issetor($type['Number of pages']) . '",
                "offers": {
                    "@type": "Offer",
                    "availability": "' . $this->product_availabilty($this->issetor($type['Availabilty'])). '",
                    "price": "' . $this->issetor($type['Amount']) . '",
                    "priceCurrency": "' . $this->issetor($type['Currency']) . '"
                },
                "publisher": "' . $this->issetor($type['Publisher']) . '"
            }
        }
        </script>';
    }

    /**
     * Job posting
     *
     * @return string
     */
    private function job($type)
    {
        return '<script type = "application/ld+json" >
        {
            "@context": "http://schema.org",
            "@type": "JobPosting",
            "estimatedSalary": {
                "@type": "MonetaryAmount",
                "currency": "' . $this->issetor($type['Currency']) . '",
                "value": {
                    "@type": "QuantitativeValue",
                    "minValue": "' . $this->issetor($type['Minimum']) . '",
                    "maxValue": "' . $this->issetor($type['Maximum']) . '",
                    "unitText": "YEAR"
                }
            },
            "datePosted": "' . $this->issetor($type['Date posted']) . '",
            "description": "' . $this->issetor($type['Description']) . '",
            "title": "' . $this->issetor($type['Title']) . '",
            "validThrough": "' . $this->issetor($type['Valid through']) . '",
            "employmentType": "' . \str_replace('-', '_', $this->issetor($type['Employment type'])) . '",
            "hiringOrganization": {
                "@type": "Organization",
                "name": "' . $this->issetor($type['Hiring organization']) . '"
            },
            "identifier": {
                "@type": "PropertyValue",
                "name": "' . $this->issetor($type['Hiring organization']) . '"
            },
            "jobLocation": {
                "@type": "Place",
                "address": {
                    "@type": "PostalAddress",
                    "streetAddress": "' . $this->issetor($type['Street address']) . '",
                    "addressLocality": "' . $this->issetor($type['Locality']) . '",
                    "addressRegion": "' . $this->issetor($type['Region']) . '",
                    "postalCode": "' . $this->issetor($type['Postal code']) . '",
                    "addressCountry": "' . $this->issetor($type['Country code']) . '"
                }
            },
            "baseSalary": {
                "@type": "MonetaryAmount",
                "currency": "' . $this->issetor($type['Currency']) . '",
                "value": {
                    "@type": "QuantitativeValue",
                    "value": ' . $this->issetor($type['Amount']) . ',
                    "unitText": "HOUR"
                }
            }
        }
        </script>';
    }

    /**
     * How-to
     *
     * @return string
     */
    private function howto($type)
    {
        return '<script type="application/ld+json">
        {
            "@context": "https://schema.org/",
            "@type": "HowTo",
            "name": "' . $this->issetor($type['Name']) . '",
            "description": "' . $this->issetor($type['Description']) . '",
            "image": "' . $this->issetor($type['Image']) . '",
            "totalTime": "PT' . $this->issetor($type['Total time']) . 'M",
            "estimatedCost": {
                "@type": "MonetaryAmount",
                "currency": "' . $this->issetor($type['Currency']) . '",
                "value": "' . $this->issetor($type['Estimated cost']) . '"
            },
            "supply": ' . $this->listtoarray($this->issetor($type['Supply list']), "HowToSupply", "name") . ',
            "tool": ' . $this->listtoarray($this->issetor($type['Tools']), "HowToTool", "name") . ',
            "step": ' . $this->howto_instructions($this->issetor($type['Instructions'])) . '
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
                $list .= '"telephone": "' . $this->issetor($contact['Phone']) . '",' . "\r\n";
                $list .= '"contactType": "' . $this->issetor($contact['Type']) . '",' . "\r\n";
                $list .= '"contactOption": ' . $this->context($contact['Extras']) . ',' . "\r\n";
                $list .= '"availableLanguage": ' . $this->context($contact['Languages']) . '' . "\r\n";
                $list .= '}';
            }
            return $list;
        }
    }

    /**
     * How-to instructions
     *
     * @return string
     */
    private function howto_instructions($instructions)
    {
        $list = '';
        if (isset($instructions) && $instructions != false) {
            $hasComma = false;
            $list .= "[";
            foreach ($instructions as $instruction) {
                $list .= '{' . "\r\n";
                $list .= '"@type": "HowToStep",' . "\r\n";
                $list .= '"text": "' . $this->issetor($instruction['Description']) . '",' . "\r\n";
                $list .= '"image": "' . $this->issetor($instruction['Image']) . '",' . "\r\n";
                $list .= '"name": "' . $this->issetor($instruction['Name']) . '",' . "\r\n";
                $list .= '"url": "' . $this->issetor($instruction['URL']) . '"' . "\r\n";
                $list .= '}';
                if (!$hasComma) {
                    $list .= ",";
                }
                $hasComma = true;
            }
            $list .= "]";
            return $list;
        }
    }

    /**
     * List fieldtype to an array
     *
     * @return string
     */
    private function listtoarray($instructions, $type, $field)
    {
        $list = '';
        if (isset($instructions) && $instructions != false) {
            $hasComma = false;
            $list .= "[";
            foreach ($instructions as $instruction) {
                $list .= '{"@type": "' . $type . '",';
                $list .= '"' . $field . '":"' . $instruction . '"}';
                if (!$hasComma) {
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
