<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str; // Use Str class

class BaseModel extends Model
{
    use SoftDeletes;
    /**
     * Override the constructor to dynamically define
     * mutators and accessors for encoded attributes.
     */
   public function __construct(array $attributes = [])
{
    parent::__construct($attributes);

    // Check if $encodedAttributes is defined and is an array
    if (!isset($this->encodedAttributes) || !is_array($this->encodedAttributes)) {
        $this->encodedAttributes = []; // Set it to an empty array if not set
    }

    // Filter out any blank values in $encodedAttributes
    $this->encodedAttributes = array_filter($this->encodedAttributes, function ($attribute) {
        return !empty($attribute); // Ensure the attribute is not blank
    });

    // Debugging - Check the filtered attributes
    // dd($this->encodedAttributes);

    // Register mutators and accessors for all valid encoded attributes
    foreach ($this->encodedAttributes as $attribute) {
        if (!empty($attribute)) {
            $this->registerAttributeMutator($attribute);
            $this->registerAttributeAccessor($attribute);
        }
    }
}



    protected function registerAttributeMutator($attribute)
    {
        $method = $attribute; 
        if (!method_exists($this, $method)) {
            $method = function ($value) use ($attribute) {
               
                if ($value != "") {
                    return htmlentities($value); // Encode the value
                }
    
                return "";
            };
        }
    }
    /**
     * Register accessor for decoding attribute after fetch.
     */
    protected function registerAttributeAccessor($attribute)
    {
        $method = $attribute; // Use Str::camel()

        $this->$method = function ($value) use ($attribute) {
            return html_entity_decode($value); // Decode value
        };
    }
}
