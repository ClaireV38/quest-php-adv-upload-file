<?php

if (!empty($_FILES['images']['name'][0])) {
    $images = $_FILES['images'];
    $uploaded = [];
    $failed = [];
    $allowed = ['png', 'jpg', 'gif'];
    foreach ($images['name'] as $position => $imageName) {
        $imageTmp = $images['tmp_name'][$position];
        $imageSize = $images['size'][$position];
        $imageError = $images['error'][$position];
        $imageExt = explode('/', mime_content_type( $imageTmp ));
        $imageExt = strtolower(end($imageExt));

        if (in_array($imageExt, $allowed)) {
            if ($imageError === 0) {
                if ($imageSize <= 1048576) {
                    $imageNameNew = uniqid() . '.' . $imageExt;
                    $imageDestination = "uploads/" . $imageNameNew;
                    if (move_uploaded_file($imageTmp, $imageDestination)) {
                        $uploaded[$position] = $imageDestination;
                    } else {
                        $failed[$position] = "{$imageName} failed to upload";
                    }
                } else {
                    $failed[$position] = "{$imageName} is too large";
                }
            } else {
                $failed[$position] = "{$imageName} errored with code {$imageError}";
            }
        } else {
            $failed[$position] = "{$imageName} has an invalid file extension : {$imageExt}";
        }
    }
    var_dump($failed);
}

$images = new FilesystemIterator(__DIR__ .'/uploads/');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['btn_delete']) && !empty($_POST)){
    $imageToDump = trim($_POST['image_to_delete']);
    echo $imageToDump;
    if (file_exists('./uploads/' . $imageToDump)) {
        unlink ('./uploads/' . $imageToDump);
        header('Location: upload.php');
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>upload images</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.3/dist/css/bootstrap.min.css"
          integrity="sha384-TX8t27EcRE3e/ihU7zmQxVncDAy5uIKz4rEkgIXeMed4M0jlfIDPvg6uqKI2xXr2" crossorigin="anonymous">
</head>
<body>
<form action="" method="post" enctype="multipart/form-data">
    <div class="form-group">
    <label for="imageUpload">Upload images</label>
    <input type="file"  class="form-control" name="images[]" id="imageUpload" multiple="multiple"/>
    </div>
    <button type="submit" class="btn btn-primary">Send</button>
</form>

<?php if (isset($images)) {
    foreach ($images as $fileinfo) { ?>
        <div class="card" style="width: 18rem;">
            <img src="/uploads/<?= $fileinfo->getFilename() ?>" alt="img in uploads directory"" class="card-img-top" alt="...">
            <div class="card-body">
                <h5 class="card-title"> <?= $fileinfo->getFilename() ?> </h5>
                <form action="upload.php" method="post" >
                    <input type="hidden" name="image_to_delete" value="<?= $fileinfo->getFilename() ?>">
                    <button type="submit" name="btn_delete" class="btn btn-primary">delete</button>
                </form>
            </div>
        </div>
    <?php } }?>
</body>
</html>


