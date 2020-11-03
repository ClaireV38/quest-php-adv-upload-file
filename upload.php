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
        $imageExt = explode('.', $imageName);
        $imageExt = strtolower(end($imageExt));

        if (in_array($imageExt, $allowed)) {
            echo "coucou";
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
    $images = new FilesystemIterator(__DIR__ .'/uploads/');
}



?>

<form action="" method="post" enctype="multipart/form-data">
    <label for="imageUpload">Upload images</label>
    <input type="file" name="images[]" id="imageUpload" multiple="multiple"/>
    <button>Send</button>
</form>

<?php if (isset($images)) {
    foreach ($images as $fileinfo) { ?>
    <figure>
        <img src="/uploads/<?= $fileinfo->getFilename() ?>" alt="img in uploads directory" >
        <figcaption>  <?= $fileinfo->getFilename() ?> </figcaption>
    </figure>
<?php } }?>
