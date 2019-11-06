# Expose PHP and Wordpress method to Javascript #

This is a proof of concept (POC) to stablish a methodology to 
quick access PHP and Wordpress method from Javascript. Security
should be a concern, since the methodology allows accessing the
server terminal command which is not a desire scenario if the 
methodoloy is enabled for a non-trusted user.

This is exploratory only.

## Implementation ##

### 1. Add the following code to your **functions.php** inside you theme. ###

```
add_action( 'wp_ajax_nopriv_expose', function() {
    $params = empty($_POST['params'])? [] : $_POST['params'];
    $post = call_user_func_array($_POST['method'], $params);
    $response['success'] = true;
    $response['message'] = 'success';
    $response['data'] = $post;
    $response['_POST'] = $_POST;

    header('Content-type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}, 1);
```


### 2. Inside you page template (e.g. page.php) add the Javascript code ###

```
\<script src="resources/js/appcropolis.expose.js">\<\/script>
```

### 3. Create an instance of the class (function) and use functionality ###

```
var wp = new WP();

$('#get_post').click(function() {
    var post_id = 2; // sample-page post
    wp.get_post(post_id).then(data=>{
        console.log(data);
    });
});
```

## Examples ##


### Set debug mode so you can see responses in the dev console ###

```
wp.debug = true;
```


### Get post by ID ###

```
wp.get_post(3).then(data=>{
     console.log(data);    
});
```

### Using async awiat to get data ###

```
(async function(){
     var post = await get_post(2);
     console.log(post);
})();
```


### Get site URL option value ###

```
wp.get_option('site_url').then(data=>{
     console.log(data);    
});
```


### Get site URL option value ###

```
wp.get_option('site_url').then(data=>{
     console.log(data);    
});
```


### Get date from PHP with an especif format ###

```
wp.date('Y-m-d H:i:s').then(data=>{
     console.log(data);    
});
```


### Expose "scandir" (PHP) to the window context ###

```
wp.expose('scandir', window);

scandir('././').then(data=>{
     console.log(data);    
});
```


### Expose "scandir" (PHP) to the window context with an alias ###

```
wp.expose('scandir', window, 'getDirectoryConent');

getDirectoryConent('././').then(data=>{
     console.log(data);    
});
```

### Run a terminal command (ls) ###

```
wp.run('exec', ['ls -al']);
```

### Expose "scandir" to the your own app ###

```
var myApp = {}

wp.expose('scandir', myApp);

myApp.scandir('./').then(data=>{
     console.log(data);
});
```

### Call a custom PHP method ###

PHP code (add to functions.php):

```
function hello($name='Guest') {
    $response['success'] = true;
    $response['message'] = 'success';
    $response['data'] = 'Hello, ' . $name;

    header('Content-type: application/json');
    echo json_encode($response, JSON_PRETTY_PRINT);
    exit;
}
```

Javascript code:

```

wp.run('hello', ['Raul']).then(data=>{
    console.log(data); // outputs "Hello, Raul"
});
