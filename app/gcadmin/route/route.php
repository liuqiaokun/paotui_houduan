<?php

//decode by http://www.yunlu99.com/
\think\facade\Route::post("Sys.Menu/add", "Sys.Menu/add")->middleware(["SetTable"]);
\think\facade\Route::post("Sys.Menu/update", "Sys.Menu/update")->middleware("UpTable");
\think\facade\Route::post("Sys.Menu/delete", "Sys.Menu/delete")->middleware("DeleteMenu");
\think\facade\Route::post("Sys.Field/add", "Sys.Field/add")->middleware("SetField");
\think\facade\Route::post("Sys.Field/update", "Sys.Field/update")->middleware("UpField");
\think\facade\Route::post("Sys.Field/delete", "Sys.Field/delete")->middleware("DeleteField");
\think\facade\Route::post("Sys.Application/delete", "Sys.Application/delete")->middleware("DeleteApplication");