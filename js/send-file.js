const dropArea = document.getElementById('drop-area');
const fileName = document.getElementById('file-name');
const shareBtn = document.getElementById('share-btn');
modalTitle = document.getElementById("modal-title"),
    modalMessage = document.getElementById("modal-message"),
    shareUrl = document.getElementById("share-url")
deleteUrl = document.getElementById("delete-url")
let targetFile;

if (dropArea) {
    // ドラッグオーバー時のイベント処理
    dropArea.addEventListener('dragover', (event) => {
        event.preventDefault(); // デフォルトの動作を防ぐ
        dropArea.classList.add('hover'); // スタイルの変更
    });

    // ドラッグリーブ時のイベント処理
    dropArea.addEventListener('dragleave', () => {
        dropArea.classList.remove('hover');
    });


    dropArea.addEventListener('drop', (event) => {
        event.preventDefault();
        dropArea.classList.remove('hover');

        targetFile = event.dataTransfer.files[0]; // ドロップされたファイルを取得
        // ファイルのサイズ確認 1MB以下
        if (targetFile.size > 1000000) {
            alert('ファイルサイズは1MB以下にしてください。');
            return;
        }
        // 拡張子を確認
        let validExtensions = ['png', 'jpeg', 'jpg', 'gif'];
        let fileExtension = targetFile.name.split('.').pop();
        if (!validExtensions.includes(fileExtension)) {
            alert('このファイルの拡張子は非対応です。\n対応拡張子: ' + validExtensions.join(', '));
            return;
        }

        console.log(targetFile);
        fileName.innerHTML = targetFile.name;
        console.log('file dropped');
    })

    shareBtn.addEventListener('click', () => {
        if (!targetFile) {
            modalTitle.innerHTML = "エラー";
            modalMessage.innerHTML = "ファイルをドラッグ＆ドロップしてください。"
            shareUrl.innerHTML = "";
            deleteUrl.innerHTML = "";
            return
        }
        console.log('sending file ' + targetFile.name);

        modalTitle.innerHTML = "リクエスト送信中";
        modalMessage.innerHTML = "少々お待ちください。"
        shareUrl.innerHTML = "";
        deleteUrl.innerHTML = "";

        url = 'http://localhost:8000/create';
        const formData = new FormData();
        formData.append('image', targetFile);
        fetch(url, {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                if (data.result === "作成成功") {
                    modalTitle.innerHTML = data.result;
                    modalMessage.innerHTML = "";
                    shareUrl.innerHTML = "共有URL: " + data.sharePath;
                    deleteUrl.innerHTML = "削除URL: " + data.deletePath
                } else {
                    modalTitle.innerHTML = data.result;
                    modalMessage.innerHTML = data.message;
                    shareUrl.innerHTML = "";
                    deleteUrl.innerHTML = "";
                }

            })
            .catch(error => {
                modalTitle.innerHTML = "エラーが発生しました。";
                modalMessage.innerHTML = "エラー: " + error;
                shareUrl.innerHTML = "";
                deleteUrl.innerHTML = "";

            })

    })


}

