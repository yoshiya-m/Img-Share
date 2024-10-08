const config = {
    modalTitle: document.getElementById("modal-title"),
    modalMessage: document.getElementById("modal-message"),
    shareUrl: document.getElementById("share-url")
};
require.config({ paths: { 'vs': 'https://cdnjs.cloudflare.com/ajax/libs/monaco-editor/0.20.0/min/vs' } });
require(['vs/editor/editor.main'], function () {

    var editor = monaco.editor.create(document.getElementById('editor-container'), {
        value: "",
        language: 'plaintext'
    });

    let path = window.location.pathname.replace("/", "");
    let content = "";
    if (path !== "") {
        url = 'https://snippet-share.yoshm.com/content?path=' + path;
        fetch(url)
            .then(response => response.json())
            .then(data => {
                
                content = data.content;
                console.log(content);
                editor.setValue(content);
            })
            .catch(error => {
                console.error('エラー:', error);
            })
    }
    console.log("setting monaco");

    const languages = monaco.languages.getLanguages();

    let languageSelector = document.getElementById("languages");
    for (let i = 0; i < languages.length; i++) {
        option = `<option value="${languages[i].id}">${languages[i].id}</option>`;
        languageSelector.innerHTML += option;
    }

    languageSelector.addEventListener('change', function () {
        console.log("language changed: " + this.value);
        const selectedLanguage = this.value; 
        monaco.editor.setModelLanguage(editor.getModel(), selectedLanguage);
    });



    document.getElementById("share-btn").addEventListener("click", function () {

        const data = {
            inputText: editor.getValue(),
            expirationTime: document.getElementById("expiration-time").value
        }

        config.modalTitle.innerHTML = "リクエスト送信中";
        config.modalMessage.innerHTML = "少々お待ちください。"
        config.shareUrl.innerHTML = "";

        if (data.inputText.length > 500) {
            config.modalTitle.innerHTML = "文字数を500文字以下にしてください。";
            config.modalMessage.innerHTML = "現在の文字数: " + data.inputText.length;
            config.shareUrl.innerHTML = "";
            return;
        }

        console.log("btn pushed");
        console.log("文字数" + data.inputText.length);
        console.log(data);

        url = 'https://snippet-share.yoshm.com/create';

        fetch(url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        })
            .then(response => response.json())
            .then(data => {
                console.log(data);
                config.modalTitle.innerHTML = "共有URL作成完了！";
                config.modalMessage.innerHTML = "共有URL: ";
                config.shareUrl.innerHTML = data.url;
            })
            .catch(error => {
                console.error('エラー:', error);
                config.modalTitle.innerHTML = "エラーが発生しました。";
                config.modalMessage.innerHTML = "エラー: " + error;
                config.shareUrl.innerHTML = "";

            })
    })


});
