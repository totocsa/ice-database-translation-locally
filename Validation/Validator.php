<?php

namespace Totocsa\DatabaseTranslationLocally\Validation;

use Illuminate\Validation\Validator as LaravelValidator;
use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Support\MessageBag;
use Illuminate\Validation\ValidationRuleParser;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Validator extends LaravelValidator
{
    use FormatsMessages;

    protected $forTranslation = [];

    public function messages($format = null)
    {
        if (! $this->messages) {
            $this->passes($format);
        }

        return $format === 'translatable' ? $this->forTranslation : $this->messages;
    }

    public function passes($format = null)
    {
        $this->messages = new MessageBag;

        [$this->distinctValues, $this->failedRules] = [[], []];

        // We'll spin through each rule, validating the attributes attached to that
        // rule. Any error messages will be added to the containers with each of
        // the other error messages, returning true if we don't have messages.
        foreach ($this->rules as $attribute => $rules) {
            if ($this->shouldBeExcluded($attribute)) {
                $this->removeAttribute($attribute);

                continue;
            }

            if ($this->stopOnFirstFailure && $this->messages->isNotEmpty()) {
                break;
            }

            foreach ($rules as $rule) {
                $this->validateAttribute($attribute, $rule, $format);

                if ($this->shouldBeExcluded($attribute)) {
                    break;
                }

                if ($this->shouldStopValidating($attribute)) {
                    break;
                }
            }
        }

        foreach ($this->rules as $attribute => $rules) {
            if ($this->shouldBeExcluded($attribute)) {
                $this->removeAttribute($attribute);
            }
        }

        // Here we will spin through all of the "after" hooks on this validator and
        // fire them off. This gives the callbacks a chance to perform all kinds
        // of other validation that needs to get wrapped up in this operation.
        foreach ($this->after as $after) {
            $after();
        }

        return $this->messages->isEmpty();
    }

    protected function validateAttribute($attribute, $rule, $format = null)
    {
        $this->currentRule = $rule;

        [$rule, $parameters] = ValidationRuleParser::parse($rule);

        if ($rule === '') {
            return;
        }

        // First we will get the correct keys for the given attribute in case the field is nested in
        // an array. Then we determine if the given rule accepts other field names as parameters.
        // If so, we will replace any asterisks found in the parameters with the correct keys.
        if ($this->dependsOnOtherFields($rule)) {
            $parameters = $this->replaceDotInParameters($parameters);

            if ($keys = $this->getExplicitKeys($attribute)) {
                $parameters = $this->replaceAsterisksInParameters($parameters, $keys);
            }
        }

        $value = $this->getValue($attribute);

        // If the attribute is a file, we will verify that the file upload was actually successful
        // and if it wasn't we will add a failure for the attribute. Files may not successfully
        // upload if they are too large based on PHP's settings so we will bail in this case.
        if (
            $value instanceof UploadedFile && ! $value->isValid() &&
            $this->hasRule($attribute, array_merge($this->fileRules, $this->implicitRules))
        ) {
            return $this->addFailure($attribute, 'uploaded', [], $format);
        }

        // If we have made it this far we will make sure the attribute is validatable and if it is
        // we will call the validation method with the attribute. If a method returns false the
        // attribute is invalid and we will add a failure message for this failing attribute.
        $validatable = $this->isValidatable($rule, $attribute, $value);

        if ($rule instanceof RuleContract) {
            return $validatable
                ? $this->validateUsingCustomRule($attribute, $value, $rule)
                : null;
        }

        $method = "validate{$rule}";

        $this->numericRules = $this->defaultNumericRules;

        if ($validatable && ! $this->$method($attribute, $value, $parameters, $this)) {
            $this->addFailure($attribute, $rule, $parameters, $format);
        }
    }

    protected function isValidatable($rule, $attribute, $value)
    {
        if (in_array($rule, $this->excludeRules)) {
            return true;
        }

        $a1 = $this->presentOrRuleIsImplicit($rule, $attribute, $value);
        $a2 = $this->passesOptionalCheck($attribute);
        $a3 = $this->isNotNullIfMarkedAsNullable($rule, $attribute);
        $a4 = $this->hasNotFailedPreviousRuleIfPresenceRule($rule, $attribute);

        return $this->presentOrRuleIsImplicit($rule, $attribute, $value) &&
            $this->passesOptionalCheck($attribute) &&
            $this->isNotNullIfMarkedAsNullable($rule, $attribute) &&
            $this->hasNotFailedPreviousRuleIfPresenceRule($rule, $attribute);
    }

    public function addFailure($attribute, $rule, $parameters = [], $format = null)
    {
        if (! $this->messages) {
            $this->passes();
        }

        $attributeWithPlaceholders = $attribute;

        $attribute = $this->replacePlaceholderInString($attribute);

        if (in_array($rule, $this->excludeRules)) {
            return $this->excludeAttribute($attribute);
        }

        if ($this->dependsOnOtherFields($rule)) {
            $parameters = $this->replaceDotPlaceholderInParameters($parameters);
        }

        $value = $this->getMessage($attributeWithPlaceholders, $rule, $format);

        if (is_null($format)) {
            $replaced = $this->makeReplacements($value, $attribute, $rule, $parameters);
            $this->messages->add($attribute, $replaced);
        } elseif ($format === 'translatable') {
            $replaced = $this->makeReplacements($value['message'], $attribute, $rule, $parameters);

            $this->messages->add($attribute, $replaced);

            $value['message'] = $replaced;
            $value['params'] = $parameters;
            $this->forTranslation[$attribute][] = $value;
        }

        $this->failedRules[$attribute][$rule] = $parameters;
    }
}
