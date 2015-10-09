<?php
namespace SimpleFileUploader;

use AppCore\Lib\Upload\Exception\UploadException;
use AppCore\Lib\Upload\Exception\FileTypeNotAllowedException;

class FileUploader {

  private $allowed_file_types = [];
  private $destination = null;
  private $create_destination = true;

  /**
   * Usado para informar os tipos de arquivos permitidos a serem recebidos pelo formulário.
   * Os tipos informados devem ser por mime type. Cada parâmetro um tipo diferente.
   * Exemplo:
   *  $FileUploader->allowTypes('image/jpg', 'image/png', 'image/gif');
   */
  public function allowTypes()
  {
    $types = func_get_args();
    $this->allowed_file_types = $this->allowed_file_types + $types;

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

    $finfo = new finfo(FILEINFO_MIME_TYPE);

    if(!in_array($this->allowed_file_types, $finfo->file($data['tmp_name'])))
      throw new FileTypeNotAllowedException();

    return true;
  }

  /**
   * Usado para informar o nome que o arquivo recebido terá após a conclusão do upload no diretório informado através de FileUploader::destination().
   * @param string $filename o nome final do arquivo.
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
    if($formData['error'] == UPLOAD_ERR_OK) {
      $this->_validateDataType($formData);

      if (!empty($path))
        $this->setDestination($path);

      if (empty($this->filename))
        $this->setFilename(md5($formData['name'] . date('hisdmY')));

      $fullpathUploadedFile = $this->destination . $this->filename;

      if (move_uploaded_file($this->form_data['tmp_name'], $fullpathUploadedFile))
        return $fullpathUploadedFile;
      else
        throw new UploadException($formData['error']);
    }
  }
}

?>