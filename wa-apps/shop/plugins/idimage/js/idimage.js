var idImageUpload = {

    imageSizeThumb: 150,
    upload: function () {
        let src = $('.product_gallery-images-main-img').attr('src')
        // Проверяем, содержит ли src хост (начинается ли с http:// или https://)
        if (!/^https?:\/\//.test(src)) {
            const currentUrl = window.location.href; // Получаем текущий URL
            const baseUrl = window.location.origin; // Получаем только хост (http://127.0.0.1:8401)

            // Если src не содержит хоста, добавляем хост и протокол
            src = baseUrl + src;
        }
        this.Image.uploadImageFromCanvas(src, 1, function (data) {
            console.log(data);
        });

        this.Image.createThumbnail(src, function (dataUrl) {
            $('.product_action-desc').append('<img width="150px" src="' + dataUrl + '" alt="">')
        });
    },
    createThumbnail(src, callback) {

        const img = new Image();
        img.crossOrigin = "anonymous"; // если требуется, чтобы обрабатывать изображения с другого домена

        img.onload = function () {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            const size = 150;

            canvas.width = size;
            canvas.height = size;

            // Вычисляем масштаб и смещение
            const ratio = Math.min(img.width / size, img.height / size);
            const scaledWidth = img.width / ratio;
            const scaledHeight = img.height / ratio;

            const dx = (size - scaledWidth) / 2;
            const dy = (size - scaledHeight) / 2;

            ctx.fillStyle = "#fff"; // Заполним фон белым
            ctx.fillRect(0, 0, size, size);

            ctx.drawImage(img, dx, dy, scaledWidth, scaledHeight);

            // Получаем JPEG в base64
            const dataUrl = canvas.toDataURL("image/jpeg", 0.9); // 0.9 - качество JPEG

            if (callback) callback(dataUrl);
        };

        img.onerror = function () {
            console.error("Не удалось загрузить изображение");
            if (callback) callback(null);
        };

        img.src = src;
    },
    dataURLtoFile(dataurl, filename) {

        console.log(filename);
        const arr = dataurl.split(',');
        const mime = arr[0].match(/:(.*?);/)[1];
        const bstr = atob(arr[1]);
        let n = bstr.length;
        const u8arr = new Uint8Array(n);

        while (n--) {
            u8arr[n] = bstr.charCodeAt(n);
        }

        return new File([u8arr], filename, {type: mime});
    },
    uploadImageFromCanvas(src, fileId, callback) {
        const img = new Image();
        img.crossOrigin = "anonymous";

        img.onload = function () {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            const size = 224; // Laravel ждет 224x224

            canvas.width = size;
            canvas.height = size;

            const ratio = Math.min(img.width / size, img.height / size);
            const scaledWidth = img.width / ratio;
            const scaledHeight = img.height / ratio;

            const dx = (size - scaledWidth) / 2;
            const dy = (size - scaledHeight) / 2;

            ctx.fillStyle = "#fff";
            ctx.fillRect(0, 0, size, size);
            ctx.drawImage(img, dx, dy, scaledWidth, scaledHeight);

            const dataUrl = canvas.toDataURL("image/jpeg", 0.9);
            const file = idImage.Image.dataURLtoFile(dataUrl, "image.jpg");

            const formData = new FormData();
            formData.append('files[]', file);
            formData.append('file_ids[]', fileId);

            fetch('https://idimage.ru/api/ai/upload', {
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer '
                },
                body: formData,
            })
                .then(response => response.json())
                .then(data => {
                    console.log("Ответ сервера:", data);
                    if (callback) callback(data);
                })
                .catch(error => {
                    console.error("Ошибка при отправке:", error);
                    if (callback) callback(null);
                });
        };

        img.onerror = function () {
            console.error("Не удалось загрузить изображение");
            if (callback) callback(null);
        };

        img.src = src;
    }


}
