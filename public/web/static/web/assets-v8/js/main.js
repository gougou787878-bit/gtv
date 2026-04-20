$(document).ready(function () {
    const fullUrl = window.location.href

    function getDeviceType() {
        const userAgent = navigator.userAgent.toLowerCase() || navigator.vendor || window.opera
        if (
            Boolean(
                userAgent.match(/android|mobile|pad/i) &&
                    Boolean(userAgent.match(/ipad/i)) === false &&
                    Boolean(userAgent.match(/mac/i)) === false
            )
        ) {
            return "Android"
        }
        if (Boolean(userAgent.match(/iphone/i))) {
            return "iOS"
        }
        if (Boolean(userAgent.match(/ipad|pad/i))) {
            return "pad"
        }
        return "Unknown"
    }

    function selectDownload(data) {
        $("#iphone")
            .off("click")
            .on("click", function (e) {
                e.preventDefault()
                const modal = $('<div class="download-modal">')
                    .css({
                        position: "fixed",
                        top: 0,
                        left: 0,
                        right: 0,
                        bottom: 0,
                        backgroundColor: "rgba(0,0,0,0.5)",
                        display: "flex",
                        justifyContent: "center",
                        alignItems: "center",
                        zIndex: 1000
                    })
                    .append(
                        $('<div class="download-options">')
                            .css({
                                padding: "20px",
                                borderRadius: "8px",
                                display: "flex",
                                flexDirection: "column",
                                gap: "10px",
                                textAlign: "center",
                                width: "80%"
                            })
                            .append(
                                $("<button>")
                                    .text("商店下载（App Store）")
                                    .css({
                                        width: "90%",
                                        margin: "0 auto",
                                        padding: "12px",
                                        borderRadius: "6px",
                                        backgroundColor: "#fff"
                                    })
                                    .on("click", function () {
                                        window.location.href = data.store_url
                                    }),
                                $("<button>")
                                    .text("直接安装到桌面")
                                    .css({
                                        width: "90%",
                                        margin: "0 auto",
                                        padding: "12px",
                                        borderRadius: "6px",
                                        backgroundColor: "#fff"
                                    })
                                    .on("click", function () {
                                        window.location.href = "/pages/ios.html?aff_code=" + data.aff_code
                                    })
                            )
                    )

                $("body").append(modal)

                modal.on("click", function (e) {
                    if (e.target === this) {
                        modal.remove()
                    }
                })
            })
    }

    const deviceType = getDeviceType()
    let params = {}
    var _hmt = _hmt || []
    fetch(`/index.php?m=index&a=api_index&url=${fullUrl}`, { method: "GET" })
        .then(response => response.json())
        .then(data => {
            $(".spinner-container").remove()
            params = data
            const traceId = Tracker.getTraceId();
            if (data.store_url == "") {
                $("#iphone,#iphone-web").attr({ href: "/pages/ios.html?aff_code=" + data.aff_code + '&trace_id=' + traceId })
            } else {
                $("#iphone,#iphone-web").removeAttr("href")
                selectDownload(data)
            }

            $("#business").attr({ href: data.shangwu })
            $("#group").attr({ href: data.group })
            $("#android").attr({ href: data.version_and, "data-clipboard-text": data.share })
            $("#androidWeb").attr({ href: data.version_and, "data-clipboard-text": data.share })
            $("#android_sep").attr({ href: data.special_and, "data-clipboard-text": data.share })
            if (data.is_download == 1) {
                if (deviceType == "Android") {
                    window.location.href = data.version_and
                }
                if (deviceType == "iOS") {
                    window.location.href = "/pages/ios.html?aff_code=" + data.aff_code + '&trace_id=' + traceId
                }
            }
            new ClipboardJS(".clipboard-btn")
        })
        .catch(() => {
            $(".spinner-container").remove()
            alert("加载失败，请刷新重试")
        })

    $(".clipboard-btn").on("click", function () {
        const parsedUrl = new URL(fullUrl)
        fetch(`${parsedUrl.protocol}//${parsedUrl.hostname}/index.php?m=index&a=stat`)
    })

    // $("#fullpage").fullpage({
    //     navigation: true,
    //     sectionSelector: ".vertical-scrolling"
    // })

    if (deviceType == 'PC' || deviceType == "Unknown" || deviceType == 'pad' ) {
        $(".qr").each(function () {
            $(this).qrcode({
                correctLevel: 1,
                text: location.href,
            });
        });
    }

    $(".setup-tips").on("click", function () {
        if (deviceType == "iOS") {
            $("#ios-detail").fadeIn().css("display", "flex")
        }
        if (deviceType == "Android") {
            $("#platform-list").fadeIn().css("display", "flex")
        }
    })

    $(".platform-item").on("click", function () {
        const imgSrc = $(this).data("src")
        const imgSrc2 = $(this).data("src2")
        const platformDetail = $("#platform-detail")
        platformDetail.find(".modal-common-img").attr("src", imgSrc)
        if (imgSrc2 !== "" || imgSrc2 !== undefined) {
            platformDetail.find(".modal-common-img-2").attr("src", imgSrc2)
        }
        platformDetail.fadeIn().css("display", "flex")
    })

    $(".android-modal-arrow").on("click", function () {
        const type = $(this).data("type")
        if (type === 1) {
            $("#platform-list").fadeOut()
        }
        if (type === 2) {
            $("#platform-detail").fadeOut()
            const platformDetail = $("#platform-detail")
            platformDetail.find(".modal-common-img").attr("src", "")
            platformDetail.find(".modal-common-img-2").attr("src", "")
        }
        if (type === 3) {
            $("#ios-detail").fadeOut()
        }
    })
})
