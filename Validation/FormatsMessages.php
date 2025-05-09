<?php

namespace Totocsa\DatabaseTranslationLocally\Validation;

use Illuminate\Support\Str;
use Totocsa\DatabaseTranslationLocally\Translation\DatabaseLoader;

trait FormatsMessages
{
    use \Illuminate\Validation\Concerns\FormatsMessages;

    protected function getMessage($attribute, $rule, $format = null)
    {
        $attributeWithPlaceholders = $attribute;

        $attribute = $this->replacePlaceholderInString($attribute);

        $inlineMessage = $this->getInlineMessage($attribute, $rule);

        // First we will retrieve the custom message for the validation rule if one
        // exists. If a custom validation message is being used we'll return the
        // custom message, otherwise we'll keep searching for a valid message.
        if (! is_null($inlineMessage)) {
            return $inlineMessage;
        }

        $lowerRule = Str::snake($rule);

        $customKey = "validation.custom.{$attribute}.{$lowerRule}";

        $customMessage = $this->getCustomMessageFromTranslator(
            in_array($rule, $this->sizeRules)
                ? [$customKey . ".{$this->getAttributeType($attribute)}", $customKey]
                : $customKey
        );

        // First we check for a custom defined validation message for the attribute
        // and rule. This allows the developer to specify specific messages for
        // only some attributes and rules that need to get specially formed.
        if ($customMessage !== $customKey) {
            return $customMessage;
        }

        // If the rule being validated is a "size" rule, we will need to gather the
        // specific error message for the type of attribute being validated such
        // as a number, file or string which all have different message types.
        elseif (in_array($rule, $this->sizeRules)) {
            $value = $this->getSizeMessage($attributeWithPlaceholders, $rule, $format);
            if (is_null($format)) {
                return $value;
            } else if ($format === 'translatable') {
                return [
                    'key' => DatabaseLoader::_appPrefix . ".{$value['key']}",
                    'original' =>  $this->translator->get($value['key'], [], env('APP_LOCALE', app()->getLocale())),
                    'message' => $value['message'],
                ];
            }
        }

        // Finally, if no developer specified messages have been set, and no other
        // special messages apply for this rule, we will just pull the default
        // messages out of the translator service for this validation rule.
        $key = "validation.{$lowerRule}";

        if ($key !== ($value = $this->translator->get($key))) {
            if (is_null($format)) {
                return $value;
            } else if ($format === 'translatable') {
                return [
                    'key' => DatabaseLoader::_appPrefix . ".$key",
                    'original' =>  $this->translator->get($key, [], env('APP_LOCALE', app()->getLocale())),
                    'message' => $value,
                ];
            }
        }

        return $this->getFromLocalArray(
            $attribute,
            $lowerRule,
            $this->fallbackMessages
        ) ?: $key;
    }

    protected function getSizeMessage($attribute, $rule, $returnString = true)
    {
        $lowerRule = Str::snake($rule);

        // There are three different types of size validations. The attribute may be
        // either a number, file, or string so we will check a few things to know
        // which type of value it is and return the correct line for that type.
        $type = $this->getAttributeType($attribute);

        $key = "validation.{$lowerRule}.{$type}";
        $message = $this->translator->get($key);

        return $returnString ? $message : compact('key', 'message');
    }
}
