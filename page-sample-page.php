<?php

/**
 * 
 * Add the code below to your functions.php in your Wordpress theme.
 * 
 * 
 * add_action( 'wp_ajax_nopriv_expose', function() {
 *     $params = empty($_POST['params'])? [] : $_POST['params'];
 *     $post = call_user_func_array($_POST['method'], $params);
 *     $response['success'] = true;
 *     $response['message'] = 'success';
 *     $response['data'] = $post;
 *     $response['_POST'] = $_POST;
 * 
 *     echo json_encode($response, JSON_PRETTY_PRINT);
 *     exit;
 * }, 1);
 */

?>
<script src="https://code.jquery.com/jquery-3.3.1.min.js"></script>


<input class="post_id" value="1" />
<button id="get_post">get_post</button>
<button id="get_option">get_option</button>
<button id="date">date</button>
<button id="list">list</button>


<script>

/**
 * Cosntructor of the entry point. This class exposes Wordpress and PHP
 * methods to the front.
 * 
 * IMPORTANT: the use of this approach is unclear since this will allow
 * Javascript to run any command in Wordpress or PHP.
 * 
 */
function WP() { }


WP.prototype = {
    /**
     * 
     */

    debug: false,


    /**
     * The Wordpress AJAX URL
     */
    AJAX_URL: 'http://tinysuperheroes.loc/public/wp-admin/admin-ajax.php',

    /**
     * The action registered by the Wordpress hook (add_action('wp_ajax_nopriv_expose', ...))
     */
    ACTION: 'expose',

    /**
     * Sends a request for running a method in Wordpress.
     * 
     * @param {string} method The method that you wish to run in the server. This
     *                      could be a Wordpress function or a PHP function.
     * 
     * @param {array} params A list of parameters that are required by the method 
     *                      you wish to run.
     *
     * @param {scope} scope The object in which the "method" will be called.
     *                      The scope for the method can also be specied when 
     *                      providing an array as a "method", e.g.
     * 
     *                      wp.run(['MyClass', 'theMethod'], ...).
     * 
     * @example
     * 
     * // #1: Get a single post from Wordpress
     * 
     * wp.run('get_post', [1], null).then(data=>{
     *      console.log(data);    
     * });
     * 
     *
     * // #2: Call PHP 'date'
     * 
     * wp.run('date', ['Y-m-d H:i:s'], null).then(data=>{
     *      console.log(data);    
     * });
     * 
     *
     * // #3: Call terminal list command
     * 
     * wp.run('exec', ['ls -al'], null).then(data=>{
     *      console.log(data);    
     * });
     */
    run: function(method, params, scope) {
        return new Promise((resolve, reject)=> {
            $.ajax({
                url: this.AJAX_URL,
                type: 'post',
                dataType: 'json',
                data: {
                    action: this.ACTION,
                    method: method,
                    params: params
                }, 

            }).then((response)=> {
                this.debug && console.log(response);
                resolve(response.data);
            });
        });
    },


    /**
     * Exposes a server side method to Javascript
     * 
     * @param {string} name The name of the server side method you wish to expose.
     *
     * @param {object} context The object or contex in which the method will be exposed.
     *                  if the context is not provided, the method will be added to the 
     *                  WP instance.
     * 
     * @example
     * 
     * // #1: Expose "scandir" to the window context.
     * 
     * wp.expose('scandir', window);
     *
     * // #1: Expose "scandir" to the your own app.
     * 
     * var myApp = {}
     * wp.expose('scandir', myApp);
     * myApp.scandir('./').then(data=>{
     *      console.log(data);
     * });
     *
     */
    expose: function(name, context) {
        var self = this;
        var context = context || this;
        context[name] = function() {
            return new Promise((resolve, reject)=> {
                self.run(name, arguments, null).then(data=>{
                    resolve(data);
                });
            });
        }

        return context[name];
    }, 


    /**
     * Wordpress get_post method.
     * 
     * @param {number} id The post ID.
     * 
     * @return {promise}
     * 
     * @example
     * 
     * #1: Get post ID 3
     * 
     * wp.get_post(3).then(data=>{
     *      console.log(data);    
     * });
     * 
     * #2: Using async awiat
     * (async function(){
     *      var post = await get_post(2);
     *      console.log(post);
     * })();
     * 
     */
    get_post: function(id) {
        return new Promise((resolve, reject)=> {
            this.run('get_post', [id], null).then(data=>{
                resolve(data);
            });
        });
    },


    /**
     * Wordpress get_option method.
     * 
     * @param {string} option The option name.
     * 
     * @return {promise}
     * 
     * @example
     * 
     * #1: Get site URL option value.
     * 
     * wp.get_option('site_url').then(data=>{
     *      console.log(data);    
     * });
     */
    get_option: function(option) {
        return new Promise((resolve, reject)=> {
            this.run('get_option', [option], null).then(data=>{
                resolve(data);
            });
        });
    },


    /**
     * Use PHP 'date' function to get a formatted date.
     * 
     * @param {string} format The format in which you wish the date to be returned.
     * 
     * @return {promise}
     * 
     * @example
     * 
     * #1: Get site URL option value.
     * 
     * wp.get_option('site_url').then(data=>{
     *      console.log(data);    
     * });
     */
    date: function(format) {
        return new Promise((resolve, reject)=> {
            this.run('date', [format], null).then(data=>{
                resolve(data);
            });
        });
    }
};




/**
 * Testing the library.
 * 
 */
var wp = new WP();


$('#get_post').click(function() {
    var post_id = $('.post_id').val();
    wp.get_post(post_id).then(data=>{
        console.log(data);
    });
});



$('#get_option').click(function() {
    wp.get_option('blogdescription').then(data=>{
        console.log(data);
    });
});



$('#date').click(function() {
    wp.date('Y-m-d').then(data=>{
        console.log(data);
    });
});


$('#list').click(function() {
    wp.run('scandir', ['.'], null).then(data=>{
        console.log(data);
    })
});
</script>



