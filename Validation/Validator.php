<?php

namespace Totocsa\DatabaseTranslationLocally\Validation;

use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\Rule as RuleContract;
use Illuminate\Contracts\Validation\ValidatorAwareRule;
use Illuminate\Validation\InvokableValidationRule;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationRuleParser;
use Illuminate\Validation\Validator as LaravelValidator;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Totocsa\DatabaseTranslationLocally\Validation\MessageBag;

class Validator extends LaravelValidator
{
    use FormatsMessages;

    protected $messageType;
    protected $forTranslation = [];

    public function messages($messageType = null)
    {
        $this->messageType = $messageType;

        if (! $this->messages) {
            $this->passes($messageType);
        }

        return $messageType === 'translatable' ? $this->forTranslation : $this->messages;
    }

    public function passes($messageType = null)
    {
        $this->messageType = $messageType;
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
                $this->validateAttribute($attribute, $rule);

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

    protected function validateAttribute($attribute, $rule)
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
            return $this->addFailure($attribute, 'uploaded', []);
        }

        // If we have made it this far we will make sure the attribute is validatable and if it is
        // we will call the validation method with the attribute. If a method returns false the
        // attribute is invalid and we will add a failure message for this failing attribute.
        $validatable = $this->isValidatable($rule, $attribute, $value);

        if ($rule instanceof RuleContract) {
            if ($validatable) {
                if ($this->messageType === 'translatable') {
                    return $this->validateUsingCustomRule($attribute, $value, $rule);
                } else {
                    return parent::validateUsingCustomRule($attribute, $value, $rule);
                }
            } else {
                return null;
            }
        }

        $method = "validate{$rule}";

        $this->numericRules = $this->defaultNumericRules;

        if ($validatable && ! $this->$method($attribute, $value, $parameters, $this)) {
            $this->addFailure($attribute, $rule, $parameters);
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

    public function addFailure($attribute, $rule, $parameters = [])
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

        $message = $this->getMessage($attributeWithPlaceholders, $rule);

        if (is_null($this->messageType)) {
            $replaced = $this->makeReplacements($message, $attribute, $rule, $parameters);
            $this->messages->add($attribute, $replaced);
        } elseif ($this->messageType === 'translatable') {
            $replaced = $this->makeReplacements($message['message'], $attribute, $rule, $parameters);

            $this->messages->add($attribute, $replaced, $this->messageType, $this->forTranslation, $message, $parameters);
        }

        $this->failedRules[$attribute][$rule] = $parameters;
    }

    protected function validateUsingCustomRule($attribute, $value, $rule)
    {
        $originalAttribute = $this->replacePlaceholderInString($attribute);

        $attribute = match (true) {
            $rule instanceof Rules\Email => $attribute,
            $rule instanceof Rules\File => $attribute,
            $rule instanceof Rules\Password => $attribute,
            default => $originalAttribute,
        };

        $value = is_array($value) ? $this->replacePlaceholders($value) : $value;

        if ($rule instanceof ValidatorAwareRule) {
            if ($attribute !== $originalAttribute) {
                $this->addCustomAttributes([
                    $attribute => $this->customAttributes[$originalAttribute] ?? $originalAttribute,
                ]);
            }

            $rule->setValidator($this);
        }

        if ($rule instanceof DataAwareRule) {
            $rule->setData($this->data);
        }

        if (! $rule->passes($attribute, $value, $this->data)) {
            $ruleClass = $rule instanceof InvokableValidationRule ?
                get_class($rule->invokable()) :
                get_class($rule);

            $this->failedRules[$originalAttribute][$ruleClass] = [];

            $messages = $this->getFromLocalArray($originalAttribute, $ruleClass) ?? $rule->message();

            $messages = $messages ? (array) $messages : [$ruleClass];

            foreach ($messages as $key => $message) {
                $key = is_string($key) ? $key : $originalAttribute;
                $replaced = $this->makeReplacements($rule->message, $key, $ruleClass, $rule->parameters);
                $replaced = strtr($replaced, $rule->parameters);

                $messageArray = $this->getMessage($attribute, $rule->message(), $replaced);

                $this->messages->add($attribute, $replaced, $this->messageType, $this->forTranslation, $messageArray, $rule->parameters);
            }
        }
    }
}
