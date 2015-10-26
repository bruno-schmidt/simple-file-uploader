<?php
namespace SimpleFileUploader;

use SimpleFileUploader\Exception\UploadException;
use SimpleFileUploader\Exception\FileTypeNotAllowedException;
use SimpleFileUploader\Exception\DestinationNotSetException;

class FileUploader {

  private $allowed_file_types = [];
  private $destination = null;
  private $filename;

  /**
   * Usado para informar os tipos de arquivos permitidos a serem recebidos pelo formulário.
   * Os tipos informados devem ser por mime type. Cada parâmetro um tipo diferente.
   * Exemplo:
   *  $FileUploader->allowTypes('image/jpg', 'image/png', 'image/gif');
   */
  public function allowTypes()
  {
    $types = func_get_args();
    $this->allowed_file_types = $types;

    return $this;
  }

  /**
   * Usado para informar o diretório para onde será enviado o arquivo recebido.
   * Deve ser o caminho completo.
   * @param string $destination o caminho completo para onde será enviado o arquivo recebido.
   */
  public function setDestination($destination)
  {
    if(!preg_match('/[\/]$/', $destination))
      $destination .= '/';

    if(!is_dir($destination))
      throw new InvalidArgumentException('Diretório $destination não encontrado.');

    $this->destination = $destination;
  }

  /**
   * Retorna o caminho do diretório para onde é enviado o arquivo recebido.
   */
  public function getDestination()
  {
    if(empty($this->destination))
      throw new DestinationNotSetException();
    return $this->destination;
  }

  /**
   * Valida se o arquivo recebido pelo formulário é um arquivo com o tipo permitido através do método FileUploader::allowTypes()
   * @param array $data um array com o conteúdo da variável global $_FILES.
   */
  private function _validateDataType($data)
  {
    if(empty($this->allowed_file_types))
      return true;

    $finfo = new \finfo(FILEINFO_MIME_TYPE);

    if(!in_array($finfo->file($data['tmp_name']), $this->allowed_file_types))
      throw new FileTypeNotAllowedException();

    return true;
  }

  private function _validateFormDataStructure($data)
  {
    if(!isset($data['error']) and !isset($data['tmp_name']))
      throw new \Exception('A estrutura dos dados do arquivo é inválida.');
  }
  /**
   * Usado para informar o nome que o arquivo recebido terá após a conclusão do upload no diretório informado através de FileUploader::destination().
   * @param string $filename o nome final do arquivo sem extensão.
   */
  public function setFilename($filename)
  {
    $this->filename = $filename;
  }

  /**
   * Valida os dados recebidos e executa o upload.
   * Será feito o upload de um arquivo apenas. Se o array $_FILES é um array com informações de upload de multiplos arquivos, então essa função deve ser chamada uma vez para cada um deles.
   * @param array $formData as informações do arquivo enviado no formato do array $_FILES de um único arquivo.
   * @param string $path (opcional) o caminho para onde o arquivo deve ser enviado. Substitui o valor informado através de FileUploader::destination().
   */
  public function upload($formData, $path = null)
  {
    $this->_validateFormDataStructure($formData);
    if($formData['error'] == UPLOAD_ERR_OK) {
      $this->_validateDataType($formData);

      if (empty($path))
        $path = $this->setDestination();

      if (!empty($this->filename))
        $filename = $this->getFilename();
      else
        $filename = md5($formData['name'] . date('hisdmY'));

      $fullpathUploadedFile = $path . $filename . '.' . pathinfo($formData['name'], PATHINFO_EXTENSION);

      if (move_uploaded_file($formData['tmp_name'], $fullpathUploadedFile))
        return $fullpathUploadedFile;
    }
    return false;
  }
}

?>
