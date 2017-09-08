<?php

$app->get('/register', 'App\Controllers\web\UserController:getRegister')->setName('register');
$app->post('/register', 'App\Controllers\web\UserController:postRegister');
$app->get('/activateaccount/{token}', 'App\Controllers\web\UserController:activateAccount')->setName('register');

$app->get('/admin', 'App\Controllers\web\UserController:getLoginAsAdmin')->setName('login.admin');
$app->post('/admin', 'App\Controllers\web\UserController:loginAsAdmin');
$app->get('/', 'App\Controllers\web\UserController:getLogin')->setName('login');
$app->post('/', 'App\Controllers\web\UserController:login');

$app->group('', function() use ($app, $container) {
    $app->get('/home', 'App\Controllers\web\HomeController:index')->setName('home');
    $app->get('/logout', 'App\Controllers\web\UserController:logout')->setName('logout');
    $app->get('/profile', 'App\Controllers\web\UserController:viewProfile')->setName('user.profile');
    $app->get('/setting', 'App\Controllers\web\UserController:getSettingAccount')->setName('user.setting');
    $app->post('/setting', 'App\Controllers\web\UserController:settingAccount');

    $app->group('/admin', function() use ($app, $container) {

        $app->group('/group', function(){
            $this->get('', 'App\Controllers\web\GroupController:index')->setName('group.list');
            $this->get('/inactive', 'App\Controllers\web\GroupController:inActive')->setName('group.inactive');
            $this->get('/detail/{id}', 'App\Controllers\web\GroupController:findGroup')->setName('group.detail');
            $this->get('/create', 'App\Controllers\web\GroupController:getAdd')->setName('create.group.get');
            $this->post('/create', 'App\Controllers\web\GroupController:add')->setName('create.group.post');
            $this->get('/edit/{id}', 'App\Controllers\web\GroupController:getUpdate')->setName('edit.group.get');
            $this->post('/edit/{id}', 'App\Controllers\web\GroupController:update')->setName('edit.group.post');
            $this->post('/active', 'App\Controllers\web\GroupController:setInactive')->setName('group.set.inactive');
            $this->post('/inactive', 'App\Controllers\web\GroupController:setActive')->setName('group.set.active');
            $this->get('/{id}/users', 'App\Controllers\web\GroupController:getMemberGroup')->setName('user.group.get');
            $this->post('/users', 'App\Controllers\web\GroupController:setUserGroup')->setName('user.group.set');
            $this->get('/{id}/allusers', 'App\Controllers\web\GroupController:getNotMember')->setName('all.users.get');
            $this->post('/allusers', 'App\Controllers\web\GroupController:setMemberGroup')->setName('member.group.set');
            $this->get('/{id}/item', 'App\Controllers\web\ItemController:getItemInGroup')->setName('get.group.item');
        });

        $app->group('/user', function(){
            $this->get('/list', 'App\Controllers\web\UserController:listUser')->setName('user.list.all');
            $this->get('/trash', 'App\Controllers\web\UserController:trashUser')->setName('user.trash');
            $this->get('/adduser', 'App\Controllers\web\UserController:getCreateUser')->setName('user.create');
            $this->post('/adduser', 'App\Controllers\web\UserController:postCreateUser')->setName('user.create.post');
            $this->get('/del/{id}', 'App\Controllers\web\UserController:softDelete')->setName('user.del');
            $this->get('/delete/{id}', 'App\Controllers\web\UserController:hardDelete')->setName('user.delt');
            $this->get('/restore/{id}', 'App\Controllers\web\UserController:restoreData')->setName('user.restore');
            $this->get('/edit/{id}', 'App\Controllers\web\UserController:getUpdateData')->setName('user.edit.data');
            $this->post('/edit/{id}', 'App\Controllers\web\UserController:postUpdateData')->setName('user.edit.data');
            $this->get('/{id}/item', 'App\Controllers\web\UserController:getItemByadmin')->setName('user.item.admin');
        });

        $app->group('/article/', function() {
            $this->get('add', 'App\Controllers\web\ArticleController:getAdd')
            ->setName('article-add');
            $this->post('add', 'App\Controllers\web\ArticleController:add');
            $this->get('edit/{id}', 'App\Controllers\web\ArticleController:getUpdate')
            ->setName('article-edit');
            $this->post('edit/{id}', 'App\Controllers\web\ArticleController:update');
            $this->get('list/active', 'App\Controllers\web\ArticleController:getActiveArticle')
            ->setName('article-list-active');
            $this->post('list/active', 'App\Controllers\web\ArticleController:setInactive');
            $this->get('list/in-active', 'App\Controllers\web\ArticleController:getInactiveArticle')
            ->setName('article-list-inactive');
            $this->get('list/in-active/{id}', 'App\Controllers\web\ArticleController:setActive')
            ->setName('article-restore');
            $this->get('read/{id}', 'App\Controllers\web\ArticleController:readArticle')
            ->setName('article-read');
            $this->post('delete', 'App\Controllers\web\ArticleController:setDelete')
            ->setName('article-del');
        });

        $app->group('/item', function(){
            $this->get('', 'App\Controllers\web\ItemController:index')->setName('item.list');
            $this->get('/add', 'App\Controllers\web\ItemController:getAdd')->setName('item.add');
            $this->post('/add', 'App\Controllers\web\ItemController:postAdd')->setName('item.add.post');
            $this->get('/update/{id}', 'App\Controllers\web\ItemController:getUpdateItem')->setName('item.update');
            $this->post('/update/{id}', 'App\Controllers\web\ItemController:postUpdateItem')->setName('item.update.post');
            $this->get('/del/{id}', 'App\Controllers\web\ItemController:hardDeleteItem')->setName('item.delete');
            $this->get('/softdel/{id}', 'App\Controllers\web\ItemController:softDeleteItem')->setName('item.soft.delete');
            $this->get('/restore/{id}', 'App\Controllers\web\ItemController:restoreItem')->setName('item.restore');
            $this->get('/trash', 'App\Controllers\web\ItemController:getTrash')->setName('item.trash');
        });
    });
    // ->add(new \App\Middlewares\web\AdminMiddleware($container));

    $app->group('/pic', function(){
        $this->get('/group', 'App\Controllers\web\GroupController:getPicGroup')
        ->setName('pic.group');
        $this->post('/create', 'App\Controllers\web\GroupController:createByUser')
        ->setName('pic.create.group');
        $this->get('/group/{id}/item', 'App\Controllers\web\ItemController:getItemInGroup')
        ->setName('pic.item.group');
        $this->get('/detail/{id}', 'App\Controllers\web\GroupController:findGroup')
        ->setName('pic.group.detail');
        $this->get('/{id}/users', 'App\Controllers\web\GroupController:getMemberGroup')
        ->setName('pic.member.group.get');
        $this->get('/{id}/allusers', 'App\Controllers\web\GroupController:getNotMember')
        ->setName('pic.all.users.get');
        $this->post('/users', 'App\Controllers\web\GroupController:setUserGroup')
        ->setName('pic.user.group.set');
        $this->post('/addusers', 'App\Controllers\web\GroupController:setMemberGroup')
        ->setName('pic.member.group.set');
        $this->get('/group/pic', 'App\Controllers\web\GroupController:getPic')
        ->setName('get.pic.group');
        $this->get('/group/{id}/del', 'App\Controllers\web\GroupController:delGroup')
        ->setName('del.pic.group');
        $this->post('/item/create', 'App\Controllers\web\ItemController:createItemByPic')
        ->setName('pic.create.item');
        $this->get('/delete/item/{id}', 'App\Controllers\web\ItemController:deleteItemByPic')
        ->setName('pic.delete.item');
        $this->get('/edit/{id}', 'App\Controllers\web\GroupController:getUpdate')
        ->setName('pic.edit.group.get');
    });

    $app->group('/user', function(){
        $this->get('/article/read/{id}', 'App\Controllers\web\ArticleController:readArticle')
        ->setName('user.article-read');
        $this->get('/group', 'App\Controllers\web\GroupController:getGroup')
        ->setName('user.group');
        $this->get('/item/status/{id}', 'App\Controllers\web\UserController:setItemUserStatus')
        ->setName('user.item.status');
        $this->get('/item/reset/{id}', 'App\Controllers\web\UserController:restoreItemUserStatus')
        ->setName('user.item.reset.status');
        $this->get('/change/password', 'App\Controllers\web\UserController:getChangePassword')
        ->setName('user.change.password');
        $this->post('/change/password', 'App\Controllers\web\UserController:changePassword');
        $this->get('/item/all', 'App\Controllers\web\ItemController:getSelectItem')
        ->setName('user.item.all');
        $this->post('/item/add', 'App\Controllers\web\ItemController:setItem')
        ->setName('user.item.add');
        $this->get('/{id}/item/create', 'App\Controllers\web\ItemController:getCreateItem')
        ->setName('user.item.create');
        $this->post('/item/create', 'App\Controllers\web\ItemController:createItemByUser')
        ->setName('user.item.create.post');
        $this->post('/group/search', 'App\Controllers\web\GroupController:searchGroup')
        ->setName('post.group.search');
        $this->get('/group/add/{id}', 'App\Controllers\web\GroupController:joinGroup')
        ->setName('post.group.add');
        $this->get('/group/{id}/del', 'App\Controllers\web\GroupController:leaveGroup')
        ->setName('get.del.group');
        $this->get('/group/{id}/item', 'App\Controllers\web\UserController:getItemsUser')
        ->setName('user.item.group');
        $this->post('/item/report', 'App\Controllers\web\ItemController:reportItem')
        ->setName('user.item.report');
        $this->get('/item/delete/{id}', 'App\Controllers\web\ItemController:deleteItemByUser')
        ->setName('user.item.delete');
        $this->get('/delete/guard/{id}', 'App\Controllers\web\UserController:deleteGuardian')
        ->setName('user.guard.delete');
        $this->post('/search', 'App\Controllers\web\UserController:searchUser')
        ->setName('user.search');
        $this->get('/group/{id}', 'App\Controllers\web\UserController:enterGroup')
        ->setName('enter.group');
        $this->post('/group/post/create', 'App\Controllers\web\PostController:addPost')
        ->setName('create.post');
        $this->get('/{group}/post/{id}/del', 'App\Controllers\web\PostController:delPost')
        ->setName('delete.post');
        $this->get('/guard/list', 'App\Controllers\web\UserController:viewGuardian')
        ->setName('list.guard');
        $this->get('/guard/add/{id}', 'App\Controllers\web\UserController:setGuardianByUser')
        ->setName('set.guardian');
        $this->post('/guard/search', 'App\Controllers\web\UserController:searchGuard')->setName('post.guard.search');
        $this->get('/del/guard/{id}', 'App\Controllers\web\UserController:delGuardian')->setName('delete.guard');
    });

    $app->group('/article/', function() {
        $this->get('add', 'App\Controllers\web\ArticleController:getAdd')
        ->setName('article-add');
        $this->post('add', 'App\Controllers\web\ArticleController:add');
        $this->get('edit/{id}', 'App\Controllers\web\ArticleController:getUpdate')
        ->setName('article-edit');
        $this->post('edit/{id}', 'App\Controllers\web\ArticleController:update');
        $this->get('list/active', 'App\Controllers\web\ArticleController:getActiveArticle')
        ->setName('article-list-active');
        $this->post('list/active', 'App\Controllers\web\ArticleController:setInactive');
        $this->get('list/in-active', 'App\Controllers\web\ArticleController:getInactiveArticle')
        ->setName('article-list-inactive');
        $this->get('list/in-active/{id}', 'App\Controllers\web\ArticleController:setActive')
        ->setName('article-restore');
        $this->get('read/{id}', 'App\Controllers\web\ArticleController:readArticle')
        ->setName('article-read');
        $this->post('delete', 'App\Controllers\web\ArticleController:setDelete')
        ->setName('article-del');
        $this->get('search', 'App\Controllers\web\ArticleController:search')
        ->setName('article-search');
    });

    $app->group('/guard', function(){
        $this->get('/user/list', 'App\Controllers\web\UserController:listUserByGuard')->setName('list.user');
        $this->get('/user/{id}/item', 'App\Controllers\web\UserController:getItemUser')->setName('user.item');
        $this->get('/user/add/{id}', 'App\Controllers\web\UserController:setGuardUser')->setName('post.user.add');
        $this->get('/user/{id}/delete', 'App\Controllers\web\UserController:delGuardUser')->setName('get.user.del');
        $this->post('/user/search', 'App\Controllers\web\UserController:searchUser')->setName('post.user.search');
    });
    // ->add(new \App\Middlewares\web\GuardMiddleware($container));
})->add(new \App\Middlewares\web\AuthMiddleware($container));
