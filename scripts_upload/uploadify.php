<?php
include('class.upload_0.31/class.upload.php');

if (!empty($_FILES)) {
        //montando o diretório de destino
        $diretorio_destino = $_SERVER['DOCUMENT_ROOT'] . $_REQUEST['folder'] . '/';
        
        //criando um id único para o nome dos arquivos.Sem a extensão.
        $file_id = md5($_FILES["Filedata"]["tmp_name"] + rand()*100000 + time());
        
        //recuperando a extensão do arquivo.
        $ext = preg_replace('/^.*\.([^.]+)$/D', '$1', $_FILES['Filedata']['name']);
        
        //encodando os diretórios de destino. ORIGINAL E THUMBS respectivamente.
        $targetFile      = utf8_decode(str_replace('//','/',$diretorio_destino));
        $targetFileThumb = utf8_decode(str_replace('//','/',$targetFile."/thumbs/"));
        
        //montando a estrutura final do arquivo para ser enviado como resposta, para inserção no banco.
        $file = $file_id.".".$ext;
        
        //Inicia-se o processo de upload.
        $handle = new Upload($_FILES['Filedata']);
        if ($handle->uploaded) {
            $handle->file_src_name_body  = $file_id;
            $handle->image_resize        = true;
            $handle->image_ratio_y       = true;
            $handle->image_x             = 600;
            
            $handle->Process($targetFile);
            
            
            //Processando Thumb
            $handle->image_resize  = true;
            $handle->image_ratio_x = true;
            $handle->image_y       = 70; //size of picture
            
            $handle->Process($targetFileThumb);
            $handle-> Clean();
            echo $file;
        }
	
}else{
    echo "Sem arquivos";
}	
?>