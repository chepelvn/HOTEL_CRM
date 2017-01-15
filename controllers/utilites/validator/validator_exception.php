<?
namespace ul\validator;

class validator_exception extends \Exception{	public function __construct($message = null, $code = 0){		$this->code = $code;        $this->message = $message;
	}
}
?>