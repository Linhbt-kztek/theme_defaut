<?php
namespace App\Http\Rules;


use Illuminate\Contracts\Validation\Rule;

class CheckVideosRules implements Rule
{
    protected $allowType = [];

    public function __construct($allowType= ['video/mp4', 'video/mpeg', 'video/quicktime'])
    {
        $this->allowType = $allowType;
    }
    public function passes($attribute, $value)
    {
        // dd($value);
        $maxFileSize = 200000; //dung lượng tối da là 200MB
        $mime_type = $value->getClientMimeType();
        return in_array($mime_type, $this->allowType);
    }

    public function message()
    {
        return 'Không thể uploads videos này!';
    }
}