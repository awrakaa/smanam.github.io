<!DOCTYPE html>
<html>
<head>
    <title>Upload and Extract Zip/Rar Files</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
</head>
<body>
    <h1>Upload Zip/Rar File</h1>
    <form action="upload.php" method="post" enctype="multipart/form-data">
        <input type="file" name="file" accept=".zip,.rar">
        <button type="submit">Upload</button>
    </form>

    <?php
    if (isset($_FILES['file']) && $_FILES['file']['error'] == UPLOAD_ERR_OK) {
        $filename = basename($_FILES['file']['name']);
        $foldername = pathinfo($filename, PATHINFO_FILENAME);
        $target_dir = "uploads/" . $foldername . "/";
        $target_file = $target_dir . $filename;

        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
            if ($filename[strlen($filename) - 1] == 'r') {
                $rar = new PharData($target_file);
                $rar->extractTo($target_dir);
            } else {
                $zip = new ZipArchive;
                if ($zip->open($target_file) === true) {
                    $zip->extractTo($target_dir);
                    $zip->close();
                }
            }
            echo "<script>swal('Success!', 'File uploaded and extracted successfully. The extracted files can be found in the folder <a href='$target_dir/kelulusan'>$foldername/kelulusan</a>', 'success');</script>";
        } else {
            echo "<script>swal('Error!', 'There was an error uploading the file.', 'error');</script>";
        }
    }
    ?>
</body>
</html>