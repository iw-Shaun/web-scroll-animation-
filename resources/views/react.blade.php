<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
        <title>LINE OA DEVELOPER</title>
        <style type="text/css">
            #__vconsole {
                display: none;
            }
            #app {
                height: 100vh;
            }
        </style>
        <script type="module">
            import RefreshRuntime from 'http://localhost:5173/@react-refresh'
            RefreshRuntime.injectIntoGlobalHook(window)
            window.$RefreshReg$ = () => {}
            window.$RefreshSig$ = () => (type) => type
            window.__vite_plugin_react_preamble_installed__ = true
        </script>
        <!-- <script type="text/javascript" src="{{ $assetUrl }}/js/vconsole.min.js"></script> -->
        <script type="text/javascript">
        //var vConsole = new VConsole();
        window.appUrl = '{{ $appUrl }}';
        window.assetUrl = function(path, withoutVersion) {
            var url = '{{ $assetUrl }}' + path;
            if (withoutVersion !== true) {
                url += '?ver={{ $appVer }}';
            }
            return url;
        }
        window.useMockUser = '{{ $useMockUser }}';
        window.lineOaUrl = '{{ $oaUrl }}';
        window.liffId = '{{ $liffId }}';
        window.liffUrl = '{{ $liffUrl }}';
        window.enPwdLogin = '{{ $enPwdLogin }}';
        window.enSamlLogin = '{{ $enSamlLogin }}';
        window.gtagId = '{{ $gtagId }}';
        </script>
        <!-- Google tag (gtag.js) -->
        <script async src="https://www.googletagmanager.com/gtag/js?id={{ $gtagId }}"></script>
        <script>
            window.dataLayer = window.dataLayer || [];
            function gtag(){dataLayer.push(arguments);}
            gtag('js', new Date());

            gtag('config', "{{ $gtagId }}");
        </script>
        {{ vite_assets() }}
    </head>
    <body>
        <div id="app"></div>
        <div id="my-modal"></div>
    </body>
</html>