@php
    $dashboard = route('dashboard.index');
    $playStore = config('constant.PLAY_STORE_URL');
    $appStore = config('constant.APP_STORE_URL');
@endphp

    <p>Nếu trình duyệt không tự động điều hướng, vui lòng truy cập vào đường dẫn: <span id="link"></span></p>

<script>
    let req_id = @json($id);
    let dashboard = @json($dashboard);
    let data = @json($data);
    data = data.data;
    let index = 0;
    let data_length = Object.keys(data).length;
    let play_store = @json($playStore);
    let app_store = @json($appStore);

    const IOS = 'app_store_id';
    const ANDROID = 'play_store_id';

    function getOS() {
        let userAgent = window.navigator.userAgent,
            platform = window.navigator.platform,
            macosPlatforms = ['Macintosh', 'MacIntel', 'MacPPC', 'Mac68K', 'Mac'],
            windowsPlatforms = ['Win32', 'Win64', 'Windows', 'WinCE'],
            iosPlatforms = ['iPhone', 'iPad', 'iPod'],
            os = -1;

        if (macosPlatforms.indexOf(platform) !== -1) {
            os = IOS;
        } else if (iosPlatforms.indexOf(platform) !== -1) {
            os = IOS;
        } else if (windowsPlatforms.indexOf(platform) !== -1) {
            os = -1;
        } else if (/Android/.test(userAgent)) {
            os = ANDROID;
        } else if (!os && /Linux/.test(platform)) {
            os = -1;
        }

        return os;
    }

    let os = getOS();

    for (const [key, value] of Object.entries(data)) {
        index++;
        if (req_id == value.id && typeof os == 'string') {
            let link = "";

            os == ANDROID ? link = play_store : link = app_store;

            link += value[os];

            document.getElementById('link').innerHTML = '<a href="' + link + '">' + link + '</a>';

            window.location.replace(link);
            break;
        }

        if (index === data_length || os === -1) {
            document.getElementById('link').innerHTML = '<a href="' + dashboard + '">' + dashboard + '</a>';

            window.location.replace(dashboard);
        }
    }
</script>