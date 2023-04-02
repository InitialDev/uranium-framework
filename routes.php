<?php
/**
 * Example
 * Standard Route: "/test" => "exampleController@example"
 * Variable Route: "/test/{variable_name}" => "exampleController@example"
 * The variables are passed as an array to the controller
 */
class routes{
    public static $get_routes=[
        "/" => "PageController@index",
    ];

    public static $post_routes=[
        "/" => "exampleController@postExample",
        "createUser" => "userController@createUser",
        "searchUser" => "userController@searchUser",
        "updateUser" => "userController@updateUser"
    ];
}
