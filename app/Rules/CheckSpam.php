<?php

namespace App\Rules;

use App\SpamRules\InvalidKeywords;
use App\SpamRules\RepeatToMuchCharacter;
use App\SpamRules\Spam;
use App\SpamRules\SpamTheContentContinously;
use Illuminate\Contracts\Validation\Rule;

class CheckSpam implements Rule
{
//    protected $data = [];
    protected $ruleClass;

    protected $rules = [
        InvalidKeywords::class,
        RepeatToMuchCharacter::class,
        SpamTheContentContinously::class
    ];

    /**
     * Create a new rule instance.
     *
     * @return void
     */
    public function __construct(protected string $model, protected string $attribute)
    {
        //
    }

    protected function detect($value, $model, $attribute)
    {
        foreach ($this->rules as $rule) {
            if (app($rule)->detect($value, $model, $attribute)) {
                $this->ruleClass = $rule;
                return true;
            }
        }

        return false;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
//        return ! app(Spam::class)->detect($value, $this->data);
        return ! $this->detect($value, $this->model, $this->attribute);
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        switch ($this->ruleClass) {
            case InvalidKeywords::class:
            {
                return 'The content has invalid keyword';
            }
            case RepeatToMuchCharacter::class:
            {
                return 'The content has too much repeat character';
            }
            case SpamTheContentContinously::class:
            {
                return 'Please wait a white before make a new attempt';
            }
        }
    }

//    public function setData($data)
//    {
//        if (! array_key_exists('timeTrack', $data)) {
//            throw new \Exception('Please define $timeTrack property/name for validation, then set the timeTrack to currentTime after validation');
//        }
//
//        $this->data['timeTrack'] = $data['timeTrack'];
//
//        return $this;
//    }
}
