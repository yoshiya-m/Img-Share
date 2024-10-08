<div class="bg-warning text-center py-2">
    <h1 class="">Snippet Share Service</h1>
</div>
<div class="d-flex flex-column align-items-center">
    <div id="editor-container" class="w-100 m-3" style="height:600px;border:1px solid grey"></div>
    <div class="d-flex text-nowrap flex-wrap justify-content-center">
        <div class="d-flex align-items-center m-2">
            <label for="mySelect">言語</label>
            <select id="languages" class="form-select mx-1" aria-label="Default select example"></select>
        </div>
        <div class="d-flex align-items-center m-2">
            <label for="mySelect">スニペット有効期限</label>
            <select id="expiration-time" class="form-select mx-1" aria-label="Default select example">
                <option selected value="+10 minutes">10分</option>
                <option value="+1 hour">1時間</option>
                <option value="+1 day">1日</option>
                <option value="+10 years">無期限</option>
            </select>
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
                    <div class="modal-body d-flex justify-content-center" id="modal-body">
                        <span id="modal-message"></span><span id="share-url"></span>
                    </div>
                    <div class="modal-footer d-flex justify-content-center">
                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>