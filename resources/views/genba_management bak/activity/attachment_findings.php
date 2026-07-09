<div class="row g-6 g-xl-9 mb-6 mb-xl-9 justify-content-center text-center" id="findingsPhoto">
    <?php if ($count > 0) {
        $foto = explode(',', $photo);
        $cn = count($foto);
        for ($i = 0; $i < $cn; $i++) {
    ?>
            <div class=" col-md-6 col-lg-4 col-xl-3" onclick="showAttachment('<?= $foto[$i] ?>', '<?= $i ?>')">
                <div class="card border-primary border border-dashed h-100">
                    <div class="card-body d-flex justify-content-center text-center flex-column p-8">
                        <a href="#" class="text-gray-800 text-hover-primary d-flex flex-column">
                            <div class="symbol symbol-150px mb-5" id="findingsPhoto-<?= $i ?>">
                                <img src="<?= env('APP_ASSETS') ?>storage/<?= $foto[$i] ?>" alt="" />
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        <?php
        }
    } else { ?>

        <div class="col-md-12 col-lg-12 col-xl-12">
            <div class="card h-100 flex-center bg-light-primary border-primary border border-dashed p-8">
                <img src="<?= env('APP_ASSETS') ?>assets/media/svg/files/upload.svg class=" alt="" />
                <a href="#" class="text-hover-primary fs-5 fw-bolder mb-2">No File Upload</a>
                <div class="fs-7 fw-bold text-gray-400">We didn't find the file</div>
            </div>
        </div>
    <?php } ?>

</div>
<div class="pl-0">
    <h4 class="text-dark-75 font-weight-bolder text-hover-primary mb-1 font-size-lg">Comment</h4>
    <div>
        <h5 class="font-weight-bolder"><?= $findings; ?></h5>
    </div>
</div>
<div class="separator separator-dashed my-10"></div>

<div class="row">
    <div class="col-md-6">
        <label for="duedate">Due Date</label>
        <input type="date" name="duedate" id="duedate" class="form-control" value="<?= $due_date ?>" disabled />
        <div class="mt-3">
            <label for="uploadImage">Evidence</label>
            <input type="file" id="uploadImage" class="form-control" accept="image/*"
                onchange="uploadImage()" multiple>
        </div>
        <div class="mt-3">
            <button type="button" id="openCameraBtn" class="btn btn-primary" onclick="open_camera()">Open
                Camera</button>
            <button type="button" id="closeCameraBtn" class="btn btn-danger" onclick="close_camera()"
                style="display: none;">Tutup Kamera
            </button>
            <button type="button" id="captureBtn" class="btn btn-primary mt-2" onclick="capture()"
                style="display: none;">Ambil
                Foto</button>
        </div>
    </div>
    <div class="col-md-6">
        <div class="camera-section mt-3" style="display: none;" id="cameraSection">
            <video id="video" width="100%" height="200" autoplay style="display: none;"></video>
            <canvas id="canvas" style="display: none;"></canvas>
            <img id="photo" src="" alt="Hasil Foto" class="img-fluid mt-2"
                style="display: none;">
            <input type="hidden" name="photo_data[]" id="photoData">
            <input type="hidden" name="photo_name[]" id="photoname">
            <div class="mt-3">
                <h5></h5>
                <div id="fileNamesContainer">
                </div>
            </div>
        </div>
        <!-- Kamera -->
        <div id="photo_show">
        </div>

    </div>
</div>
<div class="col-md-12 mt-3">

    <label for="findings">Action Plan</label>
    <textarea class="form-control" name="findings" id="findings" cols="20" rows="10"><?= $execution_comment ?></textarea>
</div>
<div class="col-md-12 mt-3">
    <button class="btn btn-primary" style="float: right;" onclick="saveAction()"><i class="fa fa-save"></i> Save</button>
</div>