<?php 
namespace Exam\Pages;
use Exam\Pages\twigLoader;
class Register {
    private array $OPTIONS = [
        "title" => "Add Your NID",
        "formTitle" => "FCU Test SignUp"
    ];
    public function __construct(){
        new twigLoader(__FILE__,false,$this->OPTIONS);
    }
}