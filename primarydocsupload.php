<?php

$docFileType = pathinfo(basename($_FILES["fileToUpload"]["name"]),PATHINFO_EXTENSION);

$target_dir_a = "primarydocs/";

if ($docFileType == "jpg" || $docFileType == "png" || $docFileType == "jpeg"
|| $docFileType == "gif" || $docFileType == "JPG" || $docFileType == "JPG" || $docFileType == "JPEG"
|| $docFileType == "GIF") {

	$target_dir_b = "images/";
}
elseif ($docFileType == "mp3" || $docFileType == "MP3") {

	$target_dir_b = "audio/";
}
elseif ($docFileType == "pdf" || $docFileType == "htm" || $docFileType == "html" || $docFileType == "txt" || $docFileType == "doc" || $docFileType == "docx" || $docFileType == "ppt" || $docFileType == "pptx" || $docFileType == "PDF" || $docFileType == "HTM" || $docFileType == "HTML" || $docFileType == "TXT" || $docFileType == "DOC" || $docFileType == "DOCX" || $docFileType == "PPT" || $docFileType == "PPTX") {

	$target_dir_b = "";
}
else {
	echo "Sorry, only JPG, JPEG, PNG, GIF, PDF, HTM, HTML, TXT, DOC, PPT, and MP3 files are allowed.<br>";
    $uploadOk = 0;
    exit();
}

$target_file_c = str_replace(" ", "_", basename($_FILES["fileToUpload"]["name"]));

$target_file = $target_dir_a . $target_dir_b . $target_file_c;

$for_filename = $target_dir_b . $target_file_c;

$uploadOk = 1;

// Check if image file is a actual image or fake image
$check = filesize($_FILES["fileToUpload"]["tmp_name"]);
if($check !== false) {
    echo "$target_file is $check bytes and is a $docFileType file.<br>";
    $uploadOk = 1;
} else {
    echo "Sorry, file doesn't seem to exist.<br>";
    $uploadOk = 0;
}

// Check if file already exists
if (file_exists($target_file)) {
    echo "Sorry, a file called <strong>&ldquo;" . $target_file . "&rdquo;</strong> already exists. If you want to copy this one over that one, please use FTP instead, and make sure you&rsquo;re not deleting something we need to keep.<br>";
    $uploadOk = 0;
}
// Check file size
if ($_FILES["fileToUpload"]["size"] > 250000000) {
    echo "Sorry, but over a quarter gigabyte? Please upload this enormous file via FTP.<br>";
    $uploadOk = 0;
}
// Check if $uploadOk is set to 0 by an error
if ($uploadOk == 0) {
    echo "Sorry, your file was not uploaded.";
// if everything is ok, try to upload file
} else {
    if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $target_file)) {
        $basenm = $target_file;
		echo "The file <strong>$basenm</strong> has been uploaded, and the filename has been added to the &ldquo;filename&rdquo; blank in the form below.";
    } 
    else {
        echo "Sorry, there was an error uploading your file.";
    }
}

?>