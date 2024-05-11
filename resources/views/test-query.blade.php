<select id="route-type">
    <option value="">Не выбрано</option>
    @foreach($routeList as $route)
        <option value="{{ $route['uri'] }}" data-method="{{ $route['method'] }}" data-demo="{{ $route['demo'] }}">{{ $route['uri'] }}</option>
    @endforeach
</select>

<form onsubmit="return;">
    @csrf
    <div id="captcha-container">
    </div>
    <label for="body">Body (json)</label>
    <textarea name="body" cols="30" rows="10" id="body"></textarea>
    <label for="url">URL</label>
    <input type="text" name="url" id="url">
    <label for="method">Method</label>
    <select name="method" id="method">
        <option value="POST">POST</option>
        <option value="GET">GET</option>
    </select>
    <input type="submit" value="Отправить запрос">
    <pre id="json-renderer" style="background: #dedede; max-height: 500px;overflow:scroll;"></pre>
</form>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery.json-viewer@1.5.0/json-viewer/jquery.json-viewer.min.js"></script>
<link href="https://cdn.jsdelivr.net/npm/jquery.json-viewer@1.5.0/json-viewer/jquery.json-viewer.min.css" rel="stylesheet">
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script>
    $(document).ready(function () {
        function showCaptcha() {
            let captchaContainer = document.getElementById('captcha-container');
            let newElement = document.createElement('div');
            newElement.id = 'captcha';
            captchaContainer.appendChild(newElement);
            window.captcha = grecaptcha.render('captcha', {
                'sitekey': '6LdIVmQpAAAAAIQ8LO4RQBDEWO0CZj6y2MJJPaO0'
            });
        }
        function hideCaptcha() {
            document.getElementById('captcha').remove();
        }

        $('#route-type').on('change', function () {
            let selected = $(this).find(":selected");
            let uri = selected.val()
            if (uri.length === 0) {
                return;
            }
            let method = selected.data('method')
            let demo = selected.data('demo')
            $('input[name=url]').val(uri)
            $('select[name=method]').val(method)
            $('textarea[name=body]').val(JSON.stringify(demo, undefined, 4))
        })
        $('form').on('submit', function (e) {
            console.log($('#captcha').val())
            e.preventDefault()
            $('.output').html();
            let token = $('input[name=_token]').val()
            let url = $('input[name=url]').val()
            let body = $('textarea[name=body]').val()
            if (body.length > 0) {
                try {
                    body = JSON.parse(body)
                    if($('#captcha').length > 0) {
                        body['captcha'] = grecaptcha.getResponse(window.captcha)
                    }
                } catch (e) {

                }
            }
            let method = $('select[name=method]').val()
            body._token = token

            if (method === 'GET' && body.length > 0) {
                body = body.serialize()
            }

            var headers = {};
            if (localStorage.getItem('auth_token')) {
                headers['Authorization'] = 'Bearer ' + localStorage.getItem('auth_token');
            }

            $.ajax({
                url: url,
                type: method,
                data: body,
                headers: headers,
                success:function(response){
                    console.log(response)
                    if (isJson(response)) {
                        $('#json-renderer').jsonViewer(response);
                        if (response.result.captcha) {
                            showCaptcha();
                        } else {
                            hideCaptcha();
                            if(response.result.token) {
                                localStorage.setItem('auth_token', response.result.token);
                            }
                        }
                    } else {
                        $('#json-renderer').html(response);
                    }
                },
                error: function (jqXHR, exception) {
                    if (isJson(jqXHR.responseText)) {
                        $('#json-renderer').jsonViewer(jqXHR.responseJSON);
                    } else {
                        $('#json-renderer').html(jqXHR.responseText);

                    }
                }
            });
        });
        function isJson(str) {
            if( (typeof str === "object" || typeof str === 'function') && (str !== null) )
            {
                return true
            }

            try {
                JSON.parse(str);
            } catch (e) {
                return false;
            }
            return true;
        }

    })
</script>
