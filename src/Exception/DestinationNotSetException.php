<?php
namespace SimpleFileUploader\Exception;

class DestinationNotSetException extends \Exception {

  public function __construct($message = null)
  {
    if(empty($message))
      $message = 'O diretório de destino para upload de arquivo não foi informado.';

    parent::__construct($message, null);
  }
}

?>
