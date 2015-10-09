<?php
namespace SimpleFileUploader\Exception;

class FileTypeNotAllowedException extends \Exception {

  public function __construct($message = null)
  {
    if(empty($message))
      $message = 'Tipo de arquivo não permitido.';

    parent::__construct($message, null);
  }

}

?>
