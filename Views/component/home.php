<?php
use Helpers\Settings;

$siteURL = Settings::env('BASE_URL');;


?>

<div class="bg-info text-center py-2 h-20">
    <h1><a href="<?php echo $siteURL?>" class="text-decoration-none text-dark">Image Share Service</a></h1>
</div>
<div class="drop-area d-flex justify-content-center align-items-center p-0" id="drop-area">
    <p>ここにファイルをドラッグ＆ドロップ</p>

</div>
<div class="d-flex flex-column align-items-center justify-content-center m-3">
    <!-- 選択されたファイルを表示 -->
    <div class="m-3">
        <span>選択されたファイル：</span><span id="file-name"></span>
    </div>
    <button id="share-btn" type="button" class="btn btn-info m-2" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">共有URL作成</button>

    <!-- Modal -->
    <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false"
        tabindex="-1" aria-labelledby="result-label" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header d-flex justify-content-center">
                    <h1 class="modal-title fs-5" id="modal-title"></h1>
                </div>
                <div class="modal-body d-flex flex-column justify-content-center align-items-center" id="modal-body">
                    <div>
                        <span id="modal-message"></span>
                    </div>
                    <div>
                        <span id="share-url"></span>
                    </div>
                    <div>
                        <span id="delete-url"></span>
                    </div>
                </div>
                <div class="modal-footer d-flex justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

</div>