<?php
namespace SimpleFileUploader\Exception;

class UploadException extends \Exception {

  public function __construct($code)
  {
    $message = $this->_codeToMessage($code);
    parent::__construct($message, $code);
  }

  private function _codeToMessage($code)
  {
    switch ($code) {
      case UPLOAD_ERR_INI_SIZE:
        $message = "O arquivo enviado excede a diretiva upload_max_filesize no php.ini";
        break;
      case UPLOAD_ERR_FORM_SIZE:
        $message = "O arquivo enviado excede a diretiva MAX_FILE_SIZE que foi especificada no formulário";
        break;
      case UPLOAD_ERR_PARTIAL:
        $message = "O arquivo foi enviado parcialmente";
        break;
      case UPLOAD_ERR_NO_FILE:
        $message = "Nenhum arquivo foi enviado";
        break;
      case UPLOAD_ERR_NO_TMP_DIR:
        $message = "Diretório temporário não encontrado";
        break;
      case UPLOAD_ERR_CANT_WRITE:
        $message = "Não foi possível armazenar o arquivo no disco";
        break;
      case UPLOAD_ERR_EXTENSION:
        $message = "File upload stopped by extension";
        break;
      default:
        $message = "Erro desconhecido ao fazer upload";
        break;
      }
    return $message;
  }
}

?>
