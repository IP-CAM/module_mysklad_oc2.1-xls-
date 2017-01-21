<?php
$uploaddir = './uploads/'; 
$file = $uploaddir . basename('import.xls'); 
 
$ext = substr($_FILES['uploadfile']['name'],strpos($_FILES['uploadfile']['name'],'.'),strlen($_FILES['uploadfile']['name'])-1); 
$filetypes = array('.xls','.xlsx');
 
if(!in_array($ext,$filetypes)){
	echo "<p>Данный формат файлов не поддерживается</p>";}
else{ 
	if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) { 
	  echo "success"; 
	} else {
		echo "error";
	}
}
 

?>