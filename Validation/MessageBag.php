<?php

namespace Totocsa\DatabaseTranslationLocally\Validation;

use Illuminate\Support\MessageBag as OriginalMessageBag;

class MessageBag extends OriginalMessageBag
{
    public function add($key, $message, $messageType = 'default', &$forTranslation = null, $messageArray = [], $parameters = [])
    {
        if ($this->isUnique($key, $message)) {
            if ($messageType === 'default') {
                $this->messages[$key][] = $message;
            } else if ($messageType === 'translatable') {
                $this->messages[$key][] = $messageArray['message'];
                $forTranslation[$key][] = array_merge($messageArray, ['message' => $message]);
            }
        }

        return $this;
    }
}
