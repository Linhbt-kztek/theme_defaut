<?php
namespace App\Http\Rules;


use Illuminate\Contracts\Validation\Rule;

class CheckImageRules implements Rule
{
    protected $allowType = [];

    public function __construct($allowType= ['image/png','image/x-png','image/jpg','image/jpeg','image/pjpeg','image/gif','image/webp'])
    {
        $this->allowType = $allowType;
    }
    public function passes($attribute, $value)
    {
        $mime_type = $value->getClientMimeType();
        return in_array($mime_type, $this->allowType);
    }

    public function message()
    {
        return 'Sai định dạng ảnh!';
    }
}