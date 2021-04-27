$(function () {
    generateLine();

    $(".video-container").click(function (e) {
        let url = e.currentTarget.dataset.url;
        $("#iframe-video").attr('src', url);
        $("#modal").css('display', 'block');
        $("#modal").addClass('show');
    });

    window.addEventListener("keydown",function(e){
        if (e.target.localName == 'body' && $('#modal').hasClass('show')) {
            e.preventDefault();
            let keyCode = e.keyCode;
            switch (keyCode) {
                case 27:
                    closeModal();
                    break;
                default:
                    break;
            }
        }
    });

    window.onclick = function (e) {
        if (e.target == modal) {
            closeModal();
        }
    };

    $("#close").click(function (e) {
        e.preventDefault();
        closeModal();
    });

    window.onresize = generateLine;

    function generateLine() {
        let h = $('.timeline').height() - $('#last-item').height() + 2;

        let styleElem = document.head.appendChild(document.createElement("style"));

        if (screen.width <= 575) {
            h = 0;
        }

        styleElem.innerHTML = ".timeline:before {height: " + h +"px!important;}";
    }

    function closeModal() {
        $("#modal").css('display', 'none');
        $("#modal").removeClass('show');
        $("#iframe-video").attr('src', "");
    }
})