# Request Logger
Package for logging Laravel HTTP requests. 
By default it uses **Monolog with LogStash** formatter, but it can be overriden in your ServiceProvider, check Advanced Configuration section.

## Installation
You can install the package via composer:
```sh
composer require san4io/request-logger
```
You can publish config
```
php artisan vendor:publish --provider="San4io\RequestLogger\RequestLoggerServiceProvider"
```

## Configuration
In `request-logger.php` config file you'll find current properties:

|Property|Default|Descirption|
|-|-|-|
|storage_path|/logs/request-logger.log|Where your logs gonna be stored|
|param_exceptions|['password', 'password_confirmation']|which params should be filtered out from RequestParamsFormatter|

## Usage
In global middleware:
```php
// in `app/Http/Kernel.php`

protected $middleware = [
    ...

    \San4io\RequestLogger\Middleware\RequestLoggerMiddleware::class
];
```
In group of routes:
```php
// in a routes file
Route::group(['middleware' => \San4io\RequestLogger\Middleware\RequestLoggerMiddleware::class], function () {
    //
});
```
In single route:
```php
// in a routes file
Route::get('/', function () {
    //
})->middleware(\San4io\RequestLogger\Middleware\RequestLoggerMiddleware::class);
```

## Default Log Context Formatters
|Formatter|Name|Descirption|Example|
|-|-|-|-|
|RequestMethodFormatter|request_method|Returns request method information|POST|
|RequestUriFormatter|request_uri|Returns request uri information|http://localhost/api/v1/authenticate|
|RequestHeadersFormatter|request_headers|Returns request headers information|{cookie:[Phpstorm-f176c91a=b5df2557-0cd3-44be-857d-7ea59b93c24a; io=Xm-fflHJzYnRSle0AAAB; JSESSIONID.41a4f06a=ux60gwkijzfr1cleylxjci7po;], "origin":["http://localhost"], "accept":["application/json"]}|
|RequestParamsFormatter|request_params|Returns request params(**filtered**) information|{"email":"qwe"}|
|ResponseBenchmarkFormatter|response_ms|Returns execution time|0.1858180046081543|
|ResponseContentFormatter|response_content|Return response content(**can be very heavy, use wisely**)|{\"message\":\"The given data was invalid.\",\"errors\":{\"email\":[\"The email must be a valid email address.\"]}}|

## Extending 
You can extend your Logger with your own ContextFormatters.

Your context formatter should extend `\San4io\RequestLogger\Contracts\ContextFormatterContract` interface.
In `format` method should return your desired output.
In `name` method, you should return **unique** name of formatter

## Advanced Configuration
### Overriding Logger
You can override logger in your ServiceProvider by binding your logger to `'app.request.logger'` For example you want other Formatter like MongoDBFormatter.
```php
// YourServiceProvider.php
public function register()
{
    $this->app->bind('app.request.logger', function () {
        $logger = new Logger('request-logger');

        $handler = new RotatingFileHandler(
            storage_path(config('request-logger.storage_path'))
        );
        // Changing Handler
        $handler->setFormatter(new MongoDBFormatter(
            env('APP_NAME'),
            null,
            null,
            null
        ));

        $logger->pushHandler($handler);

        return $logger;
    });
}
```
That's it, you have setted logger with your desired formatter.

Or you can create your own logger with different name, and inject it to `RequestLogger` for example:
```php
//YourServiceProvider.php
public function register()
{
    $this->app->bind('my_mega_super_dupper_logger', function () {
        ...

        return $logger;
    });
    
    $this->app->bind(RequestLogger::class, function (Application $app) {
        return new RequestLogger(
            // Injecting your logger
            $app->make('my_mega_super_dupper_logger'),
            $app->make(LogContextFormatter::class)
        );
    });
}
```
### Overriding LogContextFormatter
It is possible that you need some addidtional data to be returned to your log. In this case you extend `\San4io\RequestLogger\Contracts\ContextFormatterContract` interface (check Extending section) and adding it to LogContextFormatter. Example:

```php
// YourServiceProvider.php
public function register()
{
    // Totally override LogContextFormatter, in this case it will return only your data.
    $this->app->bind(LogContextFormatter::class, function (Application $app) {
        $contextFormatter = new LogContextFormatter();

        $contextFormatter->addContextFormatter($app->make(YourContextFormatter::class));

        return $contextFormatter;
    });
    
    
    // Adding additional contexts to default ones.
    $this->app->bind(LogContextFormatter::class, function (Application $app) {
        $contextFormatter = $app->make(LogContextFormatter::class);

        $contextFormatter->addContextFormatter($app->make(YourContextFormatter::class));

        return $contextFormatter;
    });
    
}
```

## Road map
* Tests

## Contribution
Any contributions welcome!

## Log Examples
```json
{  
   "@timestamp":"2017-11-05T13:49:24.308258+00:00",
   "@source":"3b1ceb06851b",
   "@fields":{  
      "channel":"request-logger",
      "level":200,
      "request_method":"POST",
      "request_uri":"http://localhost/api/v1/authenticate",
      "request_params":{  
         "email":"qwe"
      },
      "request_headers":{  
         "cookie":[  
            "Phpstorm-f176c91a=b5df2557-0cd3-44be-857d-7ea59b93c24a; io=Xm-fflHJzYnRSle0AAAB; JSESSIONID.41a4f06a=ux60gwkijzfr1cleylxjci7po; _nsuid=d3c208ab-5937-4e2c-a974-7ae2c71b8c43; ns.welcomemessage.displayed=true; user=undefined; token=undefined; refreshToken=Bunn1cX7CBS1VdS%2BfbWAY12oQOD0utiDGW5jKAK06two2a0eWOMwxhetPiAe3Ao73IX5fP56UymvucykD6ZbXT4sJvyorXUR%2FNph%2FuamwK2zkE%2F%2Bh8iUqPzK5qluRWklHtZV0AXUTxjTEok3hcksRTus2uxLz3cff1JMpwXJwzEYT8Oi1WxVj8WU7XzTevskrvoil1gtRkQKKq5porjntD2nk3IU4vCNvA6HnlRIeHpz2uooXNZYRJzpbpbZBXQTUDPEu9g7psspBbhnBw87ymvxGUqrWaWr9axtDSalkuNA1UFOgoQO3hjR864xEhiGKfGVsZvQYov4I5OEDcbt45xmNE1ZE6thgSbZqGAGdQDIvnpJf%2FAkImy48sfJ4DIduziP9SBbJvgRmVblnyUDQshCg2Gm1dEi6nGuqr2iUbmzTYgACaWUkRL67oCG0fIq18vrpNcxXXbhKtd3SIHx1lrSRN%2Bg0IjFD30QnquvXpJ09OvoOp5WF6fbz5yjfrK4E%2BSg7QOEleKJZeVM2Ax2u%2BH2ED1GsWkuYy7L7GtfBAL7QfDOvY9DgFGtL4lXOxu8b10IHxI9806J1gARvFojwrpCi4wQpyui%2F9Y1WKbCK0ii2IXk6aBq%2F917znc%2FycXOFCTtxgv5O5t9ed9E7%2BYO75%2FOWCJibn%2FI2bSLGm8VC8U%3D; accessToken=eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiIsImp0aSI6ImVhZDA0MDJiYjM2YmY3Mjc2Y2UxMGUzNTYxNmU1NGEwY2RmZTMyY2M1MTQ1NjExNDQ0ZGQyMDhhNGQ3OGNiZWNhZWU5NjM5YzdiOGQ2YTllIn0.eyJhdWQiOiI1ODdkMmFkMzljOWMwYjJhMTgyMmMyNjQiLCJqdGkiOiJlYWQwNDAyYmIzNmJmNzI3NmNlMTBlMzU2MTZlNTRhMGNkZmUzMmNjNTE0NTYxMTQ0NGRkMjA4YTRkNzhjYmVjYWVlOTYzOWM3YjhkNmE5ZSIsImlhdCI6MTQ5NDE1ODY5NiwibmJmIjoxNDk0MTU4Njk2LCJleHAiOjE1MjU2OTQ2OTYsInN1YiI6IjU5MGYwZDY4OWM5YzBiMzIyOTUzZDQwNCIsInNjb3BlcyI6WyIqIl19.HeVwuLNYmNYIsy9J-9uP3_sMa03XCddLLrkZVRqllUgB7Epx1YjQr4WiZgWF0uJl_JE0AxupavzOx94l-eCTau9UBT4EzRHpbB1wcKhy6mw4N2YYeYwHjJQ46gEN5idfASLsICgcrA5puXCZcn2iCopM-qQz2H7Hgxin_nga-X756C4_hCLR-fQZMw5PbKdKcAv63U8HlmsRsjdmkfintEW_eUrHW3uApExNbTGcluAxDwTL3WNVDAGlaWGXbyhDuMLksW22gUNjjCz8WfRXhyycnKuafiLZNOSq2U5GtT5erAalTn2yw5CIfwD-P2xvujjE54sgxQPjO9kC1H2m8fHqNrvEGHGroA6IWIrh5ko-0FsXRVtuHXEF9MTBB1_O-YGGZ2dzrt6zWuq3PW2pLCYgFBiDepcLjcnk_RkfsahgT6lVEKJc9vo0779cC5kStcBzP3iqtaGhcrptn9BSql6H8VktzS8FwgjQLJfMfG9VziQVoJz6wAw2aAdIkidQqVwp8jAuDQEU6TqQZc0Y9dtzcc1G-zF9Cqlv5AcR45tMWFX9UZxout6BrCdFvxSgWdl35QlgfYNNkxEwK0swvGH83WbnmNhy7OmtU4FKkmFWEDQA7GYxqnLKNYtSYoON3BhDzKeDoX6-z2Cb8mMHQoTQ6dud1syKdzxb-ekfqaY; JSESSIONID.ffeb04b9=1buw5fipdaki21qjk09qphnlrn; XDEBUG_SESSION=PHPSTORM; JSESSIONID.a11d2002=node01iw7cp8duqs3vmec8szk9ni3c1.node0; screenResolution=1920x1080"
         ],
         "accept-language":[  
            "ru-RU,ru;q=0.9,en-US;q=0.8,en;q=0.7,lt;q=0.6,it;q=0.5"
         ],
         "accept-encoding":[  
            "gzip, deflate, br"
         ],
         "referer":[  
            "http://localhost/api/documentation"
         ],
         "content-type":[  
            "multipart/form-data; boundary=----WebKitFormBoundaryNm6iocezUxk36GAH"
         ],
         "user-agent":[  
            "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Ubuntu Chromium/62.0.3202.62 Chrome/62.0.3202.62 Safari/537.36"
         ],
         "origin":[  
            "http://localhost"
         ],
         "accept":[  
            "application/json"
         ],
         "cache-control":[  
            "no-cache"
         ],
         "pragma":[  
            "no-cache"
         ],
         "content-length":[  
            "237"
         ],
         "connection":[  
            "keep-alive"
         ],
         "host":[  
            "localhost"
         ]
      },
      "response_content":"{\"message\":\"The given data was invalid.\",\"errors\":{\"email\":[\"The email must be a valid email address.\"]}}",
      "response_ms":0.121142101287842
   },
   "@message":"POST http://localhost/api/v1/authenticate",
   "@tags":[  
      "request-logger"
   ],
   "@type":"TestApplication"
}
```
